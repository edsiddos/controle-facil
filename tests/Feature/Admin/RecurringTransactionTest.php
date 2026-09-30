<?php

namespace Tests\Feature\Admin;

use App\Jobs\GenerateRecurringTransactionsJob;
use App\Models\AccountCard;
use App\Models\AccountType;
use App\Models\Category;
use App\Models\RecurringTransaction;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;
use App\Services\RecurringTransactionService;
use App\Repositories\Eloquent\TransactionRepository;
use App\Repositories\Eloquent\RecurringTransactionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Carbon\Carbon;
use Tests\TestCase;

class RecurringTransactionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private AccountCard $accountCard;
    private Category $category;
    private TransactionService $transactionService;
    private RecurringTransactionService $recurringService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Usuário Teste',
            'email' => 'teste@exemplo.com',
            'password' => bcrypt('password'),
        ]);

        $type = AccountType::create(['name' => 'Conta Corrente']);

        $this->accountCard = AccountCard::create([
            'user_id' => $this->user->id,
            'account_type_id' => $type->id,
            'name' => 'Banco Principal',
            'balance' => 1000.00,
        ]);

        $this->category = Category::create([
            'user_id' => $this->user->id,
            'name' => 'Assinaturas',
            'type' => 'expense',
        ]);

        $txRepo = new TransactionRepository();
        $recRepo = new RecurringTransactionRepository();

        $this->transactionService = new TransactionService($txRepo, $recRepo);
        $this->recurringService = new RecurringTransactionService($recRepo, $txRepo);
    }

    /** @test */
    public function test_registering_recurring_transaction_creates_recurring_record_and_dispatches_job()
    {
        Queue::fake();

        $data = [
            'account_card_id'    => $this->accountCard->id,
            'category_id'        => $this->category->id,
            'description'        => 'Netflix Assinatura',
            'amount'             => 55.90,
            'transaction_date'   => '2026-10-01',
            'is_recurring'       => true,
            'installment_number' => 12,
            'status'             => 'PAGO',
        ];

        $transaction = $this->transactionService->registerTransaction($this->user->id, $data);

        $this->assertDatabaseHas('recurring_transactions', [
            'user_id'            => $this->user->id,
            'description'        => 'Netflix Assinatura',
            'amount'             => 55.90,
            'installment_number' => 12,
            'status'             => 'ATIVO',
        ]);

        $recurring = RecurringTransaction::where('description', 'Netflix Assinatura')->first();
        $this->assertNotNull($recurring);
        $this->assertEquals($recurring->id, $transaction->fresh()->recurring_transaction_id);

        Queue::assertPushed(GenerateRecurringTransactionsJob::class, function ($job) use ($recurring) {
            return $job->recurringTransactionId === $recurring->id;
        });
    }

    /** @test */
    public function test_job_generates_future_transactions_up_to_installment_number()
    {
        $recurring = RecurringTransaction::create([
            'user_id'            => $this->user->id,
            'account_card_id'    => $this->accountCard->id,
            'category_id'        => $this->category->id,
            'description'        => 'Plano Academia 6x',
            'amount'             => 100.00,
            'start_date'         => '2026-10-01',
            'installment_number' => 6,
            'status'             => 'ATIVO',
        ]);

        // Cria a transação inicial (parcela 1)
        Transaction::create([
            'user_id'                  => $this->user->id,
            'account_card_id'          => $this->accountCard->id,
            'category_id'              => $this->category->id,
            'recurring_transaction_id' => $recurring->id,
            'description'              => 'Plano Academia 6x',
            'amount'                   => 100.00,
            'transaction_date'         => '2026-10-01',
            'is_recurring'             => true,
            'installment_number'       => 1,
            'status'                   => 'PAGO',
        ]);

        // Executa o job diretamente
        $job = new GenerateRecurringTransactionsJob($recurring->id);
        $job->handle();

        // Deve ter gerado as parcelas 2, 3, 4, 5, 6 (total de 6 transações)
        $this->assertDatabaseCount('transactions', 6);

        $this->assertDatabaseHas('transactions', [
            'recurring_transaction_id' => $recurring->id,
            'installment_number'       => 6,
            'transaction_date'         => '2027-03-01',
            'status'                   => 'A PAGAR',
        ]);
    }

    /** @test */
    public function test_job_generates_24_months_when_no_installment_number_specified()
    {
        $recurring = RecurringTransaction::create([
            'user_id'            => $this->user->id,
            'account_card_id'    => $this->accountCard->id,
            'category_id'        => $this->category->id,
            'description'        => 'Spotify Sem Fim',
            'amount'             => 21.90,
            'start_date'         => '2026-10-01',
            'installment_number' => null,
            'status'             => 'ATIVO',
        ]);

        Transaction::create([
            'user_id'                  => $this->user->id,
            'account_card_id'          => $this->accountCard->id,
            'category_id'              => $this->category->id,
            'recurring_transaction_id' => $recurring->id,
            'description'              => 'Spotify Sem Fim',
            'amount'                   => 21.90,
            'transaction_date'         => '2026-10-01',
            'is_recurring'             => true,
            'installment_number'       => 1,
            'status'                   => 'PAGO',
        ]);

        $job = new GenerateRecurringTransactionsJob($recurring->id);
        $job->handle();

        // Deve ter gerado 24 parcelas no total (1 inicial + 23 futuras)
        $this->assertDatabaseCount('transactions', 24);
    }

    /** @test */
    public function test_cancelling_recurring_transaction_deletes_future_transactions()
    {
        $today = Carbon::now()->format('Y-m-d');
        $futureDate1 = Carbon::now()->addMonth()->format('Y-m-d');
        $futureDate2 = Carbon::now()->addMonths(2)->format('Y-m-d');

        $recurring = RecurringTransaction::create([
            'user_id'            => $this->user->id,
            'account_card_id'    => $this->accountCard->id,
            'category_id'        => $this->category->id,
            'description'        => 'Assinatura TV',
            'amount'             => 80.00,
            'start_date'         => $today,
            'installment_number' => 12,
            'status'             => 'ATIVO',
        ]);

        // Transação de hoje (passado/atual)
        $txInitial = Transaction::create([
            'user_id'                  => $this->user->id,
            'account_card_id'          => $this->accountCard->id,
            'category_id'              => $this->category->id,
            'recurring_transaction_id' => $recurring->id,
            'description'              => 'Assinatura TV',
            'amount'                   => 80.00,
            'transaction_date'         => $today,
            'is_recurring'             => true,
            'installment_number'       => 1,
            'status'                   => 'PAGO',
        ]);

        // Transações futuras
        $txFuture1 = Transaction::create([
            'user_id'                  => $this->user->id,
            'account_card_id'          => $this->accountCard->id,
            'category_id'              => $this->category->id,
            'recurring_transaction_id' => $recurring->id,
            'description'              => 'Assinatura TV',
            'amount'                   => 80.00,
            'transaction_date'         => $futureDate1,
            'is_recurring'             => true,
            'installment_number'       => 2,
            'status'                   => 'A PAGAR',
        ]);

        $txFuture2 = Transaction::create([
            'user_id'                  => $this->user->id,
            'account_card_id'          => $this->accountCard->id,
            'category_id'              => $this->category->id,
            'recurring_transaction_id' => $recurring->id,
            'description'              => 'Assinatura TV',
            'amount'                   => 80.00,
            'transaction_date'         => $futureDate2,
            'is_recurring'             => true,
            'installment_number'       => 3,
            'status'                   => 'A PAGAR',
        ]);

        // Cancela a assinatura
        $this->recurringService->cancelRecurringTransaction($recurring->id, $this->user->id);

        $this->assertEquals('CANCELADO', $recurring->fresh()->status);
        $this->assertNotNull($recurring->fresh()->cancelled_at);

        // A transação atual permanece
        $this->assertDatabaseHas('transactions', ['id' => $txInitial->id, 'deleted_at' => null]);

        // As transações futuras são removidas
        $this->assertSoftDeleted('transactions', ['id' => $txFuture1->id]);
        $this->assertSoftDeleted('transactions', ['id' => $txFuture2->id]);
    }

    /** @test */
    public function test_user_can_cancel_recurring_transaction_via_http_route()
    {
        $recurring = RecurringTransaction::create([
            'user_id'            => $this->user->id,
            'account_card_id'    => $this->accountCard->id,
            'category_id'        => $this->category->id,
            'description'        => 'Revista Semanal',
            'amount'             => 15.00,
            'start_date'         => '2026-09-01',
            'installment_number' => 12,
            'status'             => 'ATIVO',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('recurring-transactions.cancel', $recurring->id));

        $response->assertRedirect(route('recurring-transactions.index'));
        $response->assertSessionHas('success');

        $this->assertEquals('CANCELADO', $recurring->fresh()->status);
    }
}
