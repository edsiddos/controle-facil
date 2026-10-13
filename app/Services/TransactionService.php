<?php

namespace App\Services;

use App\DTOs\Admin\TransactionDTO;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Repositories\Contracts\RecurringTransactionRepositoryInterface;
use App\Jobs\GenerateRecurringTransactionsJob;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Class TransactionService
 * Camada intermediária que centraliza as regras de negócio de lançamentos financeiros.
 */
class TransactionService
{
    /**
     * Construtor injetando os repositórios necessários.
     */
    public function __construct(
        protected TransactionRepositoryInterface $repository,
        protected RecurringTransactionRepositoryInterface $recurringRepository
    ) {}

    /**
     * Trata as regras e delega a criação de transação ao repositório.
     *
     * @param int $userId ID do usuário executando a ação.
     * @param TransactionDTO|array $data Dados tratados do Form Request encapsulados em DTO ou array.
     * @return Transaction
     */
    public function registerTransaction(int $userId, TransactionDTO|array $data): Transaction
    {
        $dto = $data instanceof TransactionDTO ? $data : TransactionDTO::fromArray($data, $userId);
        $payload = $dto->toArray();
        $payload['user_id'] = $userId;

        return DB::transaction(function () use ($userId, $dto, $payload) {
            $transaction = $this->repository->create($payload);

            if ($transaction->is_recurring) {
                $startDate = $dto->transactionDate ?? ($transaction->getRawOriginal('transaction_date') ?? $transaction->transaction_date);

                $recurring = $this->recurringRepository->create([
                    'user_id'            => $userId,
                    'account_card_id'    => $transaction->account_card_id,
                    'category_id'        => $transaction->category_id,
                    'description'        => $transaction->description,
                    'amount'             => $transaction->amount,
                    'start_date'         => Carbon::parse($startDate)->format('Y-m-d'),
                    'installment_number' => $transaction->installment_number,
                    'status'             => 'ATIVO',
                ]);

                $this->repository->update($transaction, [
                    'recurring_transaction_id' => $recurring->id,
                ]);

                GenerateRecurringTransactionsJob::dispatch($recurring->id);
            }

            return $transaction;
        });
    }

    /**
     * Executa a atualização de uma transação existente com segurança de escopo.
     *
     * @param int $id ID da transação pretendida.
     * @param int $userId ID do usuário requisitante.
     * @param TransactionDTO|array $data Novos dados a serem inseridos via DTO ou array.
     * @return bool
     * @throws ModelNotFoundException
     */
    public function updateTransaction(int $id, int $userId, TransactionDTO|array $data): bool
    {
        $dto = $data instanceof TransactionDTO ? $data : TransactionDTO::fromArray($data, $userId);
        $transaction = $this->repository->findForUser($id, $userId);

        if (!$transaction) {
            throw new ModelNotFoundException("Transação não encontrada ou acesso negado.");
        }

        return $this->repository->update($transaction, $dto->toArray());
    }

    /**
     * Remove uma transação do sistema após verificação de propriedade.
     *
     * @param int $id ID da transação.
     * @param int $userId ID do usuário.
     * @return bool
     * @throws ModelNotFoundException
     */
    public function removeTransaction(int $id, int $userId): bool
    {
        $transaction = $this->repository->findForUser($id, $userId);

        if (!$transaction) {
            throw new ModelNotFoundException("Transação não encontrada ou acesso negado.");
        }

        return $this->repository->delete($transaction);
    }

    /**
     * Retorna a listagem estruturada para componentes de tabela reativos.
     *
     * @param int $userId ID do usuário.
     * @param int $limit Limite de registros.
     * @param int $offset Deslocamento (Skips).
     * @param string|null $search String de busca.
     * @return array
     */
    public function listWebTable(int $userId, int $limit, int $offset, ?string $search = null): array
    {
        if ($limit < 1) $limit = 1;
        if ($offset < 0) $offset = 0;

        return $this->repository->listWebTable($userId, $limit, $offset, $search);
    }
}
