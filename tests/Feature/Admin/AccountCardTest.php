<?php

namespace Tests\Feature\Admin;

use App\Models\AccountCard;
use App\Models\AccountType;
use App\Models\User;
use App\Services\AccountCardService;
use App\Repositories\Eloquent\AccountCardRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountCardTest extends TestCase
{
    use RefreshDatabase;

    private AccountCardService $service;
    private User $user;
    private AccountType $type;

    protected function setUp(): void
    {
        parent::setUp();

        // Inicializa o serviço injetando o repositório manualmente
        $repository = new AccountCardRepository();
        $this->service = new AccountCardService($repository);

        // Cria dados básicos necessários no banco de dados para o teste
        $this->user = User::create([
            'name' => 'Usuário Teste',
            'email' => 'teste@exemplo.com',
            'password' => bcrypt('password'),
        ]);

        $this->type = AccountType::create([
            'name' => 'Cartão de Crédito'
        ]);
    }

    /** @test */
    public function test_service_can_register_an_account_and_apply_business_rules()
    {
        // Dados de entrada que simulariam o formulário (sem passar por rota)
        $data = [
            'name' => 'Cartão Inter',
            'account_type_id' => $this->type->id,
            'balance' => 0.00,
            'credit_limit' => 2500.00
        ];

        // Executa o método do Serviço diretamente
        $result = $this->service->registerAccount($this->user->id, $data);

        // Verifica o retorno do objeto criado
        $this->assertInstanceOf(AccountCard::class, $result);
        $this->assertEquals('Cartão Inter', $result->name);

        // Valida se a regra de negócio do Serviço (limite disponível = limite total) funcionou
        $this->assertEquals(2500.00, $result->available_limit);

        // Garante que o registo foi gravado com sucesso na tabela
        $this->assertDatabaseHas('account_cards', [
            'user_id' => $this->user->id,
            'name' => 'Cartão Inter',
            'account_type_id' => $this->type->id
        ]);
    }

    /** @test */
    public function test_model_local_scope_filters_correct_user_records()
    {
        // Cria uma conta para o usuário atual
        AccountCard::create([
            'user_id' => $this->user->id,
            'account_type_id' => $this->type->id,
            'name' => 'Conta do Usuário Correto',
            'balance' => 100.00
        ]);

        // Cria outro usuário e associa uma conta a ele
        $outroUser = User::create([
            'name' => 'Outro Usuário',
            'email' => 'outro@exemplo.com',
            'password' => bcrypt('password'),
        ]);

        AccountCard::create([
            'user_id' => $outroUser->id,
            'account_type_id' => $this->type->id,
            'name' => 'Conta de Outro Usuário',
            'balance' => 500.00
        ]);

        // Executa diretamente o Scope local definido no Model
        $contasFiltradas = AccountCard::forUser($this->user->id)->get();

        // Valida se o Scope isolou corretamente os registos (deve retornar apenas 1)
        $this->assertCount(1, $contasFiltradas);
        $this->assertEquals('Conta do Usuário Correto', $contasFiltradas->first()->name);
    }
}
