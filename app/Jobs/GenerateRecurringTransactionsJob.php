<?php

namespace App\Jobs;

use App\Models\RecurringTransaction;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class GenerateRecurringTransactionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Cria uma nova instância da tarefa em fila.
     *
     * @param int $recurringTransactionId ID do registro na tabela recurring_transactions.
     */
    public function __construct(
        public int $recurringTransactionId
    ) {}

    /**
     * Executa o agendamento de parcelas/meses futuros da assinatura/plano.
     */
    public function handle(): void
    {
        $recurring = RecurringTransaction::find($this->recurringTransactionId);

        if (!$recurring || $recurring->status === 'CANCELADO') {
            return;
        }

        // Caso informe fim do plano (installment_number número de parcelas) não exceder esta data.
        // Caso não informe a data fim do plano, gere para o período de dois anos (24 meses).
        $totalMonths = $recurring->installment_number ? (int) $recurring->installment_number : 24;

        if ($totalMonths <= 1) {
            return;
        }

        // Carbon parse de start_date (formato no DB é Y-m-d)
        $startDate = Carbon::parse($recurring->getRawOriginal('start_date') ?? $recurring->start_date);

        DB::transaction(function () use ($recurring, $totalMonths, $startDate) {
            for ($i = 2; $i <= $totalMonths; $i++) {
                $nextDate = $startDate->copy()->addMonths($i - 1)->format('Y-m-d');

                $exists = Transaction::where('recurring_transaction_id', $recurring->id)
                    ->where('installment_number', $i)
                    ->exists();

                if (!$exists) {
                    Transaction::create([
                        'user_id'                  => $recurring->user_id,
                        'account_card_id'          => $recurring->account_card_id,
                        'category_id'              => $recurring->category_id,
                        'recurring_transaction_id' => $recurring->id,
                        'description'              => $recurring->description,
                        'amount'                   => $recurring->amount,
                        'transaction_date'         => $nextDate,
                        'is_recurring'             => true,
                        'installment_number'       => $i,
                        'status'                   => 'A PAGAR',
                    ]);
                }
            }
        });
    }
}
