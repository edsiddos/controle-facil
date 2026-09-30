<?php

namespace App\Services;

use App\Repositories\Contracts\RecurringTransactionRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Models\RecurringTransaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Class RecurringTransactionService
 * Camada de Serviço para orquestração de transações recorrentes e assinaturas.
 */
class RecurringTransactionService
{
    public function __construct(
        protected RecurringTransactionRepositoryInterface $repository,
        protected TransactionRepositoryInterface $transactionRepository
    ) {}

    /**
     * Retorna a listagem de assinaturas/planos para alimentaçao da WebTable.
     *
     * @param int $userId ID do usuário autenticado.
     * @param int $limit Limite de páginas.
     * @param int $offset Skips.
     * @param string|null $search Filtro de busca textual.
     * @return array
     */
    public function listWebTable(int $userId, int $limit, int $offset, ?string $search = null): array
    {
        if ($limit < 1) $limit = 1;
        if ($offset < 0) $offset = 0;

        return $this->repository->listWebTable($userId, $limit, $offset, $search);
    }

    /**
     * Informa o cancelamento de uma assinatura/plano recorrente e remove as futuras transações.
     *
     * @param int $id ID da transação recorrente.
     * @param int $userId ID do usuário.
     * @return bool
     * @throws ModelNotFoundException
     */
    public function cancelRecurringTransaction(int $id, int $userId): bool
    {
        $recurring = $this->repository->findForUser($id, $userId);

        if (!$recurring) {
            throw new ModelNotFoundException("Assinatura/Plano recorrente não encontrado ou acesso negado.");
        }

        if ($recurring->status === 'CANCELADO') {
            return true;
        }

        return DB::transaction(function () use ($recurring) {
            // 1. Atualiza status para CANCELADO
            $this->repository->update($recurring, [
                'status'       => 'CANCELADO',
                'cancelled_at' => Carbon::now(),
            ]);

            // 2. Remova as futuras transações (transações com data superior a hoje)
            $today = Carbon::now()->format('Y-m-d');
            $this->transactionRepository->deleteFutureTransactionsForRecurring($recurring->id, $today);

            return true;
        });
    }
}
