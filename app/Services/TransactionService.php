<?php

namespace App\Services;

use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class TransactionService
 * Camada intermediária que centraliza as regras de negócio de lançamentos financeiros.
 */
class TransactionService
{
    protected TransactionRepositoryInterface $repository;

    /**
     * Construtor injetando a interface do repositório correspondente.
     *
     * @param TransactionRepositoryInterface $repository
     */
    public function __construct(TransactionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Trata as regras e delega a criação de transação ao repositório.
     *
     * @param int $userId ID do usuário executando a ação.
     * @param array $data Dados tratados do Form Request.
     * @return Transaction
     */
    public function registerTransaction(int $userId, array $data): Transaction
    {
        // Vincula obrigatoriamente a movimentação ao usuário logado
        $data['user_id'] = $userId;

        // Regra de negócio implícita: Se não for recorrente, forçar nulo/zero para consistência
        if (empty($data['is_recurring'])) {
            $data['is_recurring'] = false;
        }

        return $this->repository->create($data);
    }

    /**
     * Executa a atualização de uma transação existente com segurança de escopo.
     *
     * @param int $id ID da transação pretendida.
     * @param int $userId ID do usuário requisitante.
     * @param array $data Novos dados a serem inseridos.
     * @return bool
     * @throws ModelNotFoundException
     */
    public function updateTransaction(int $id, int $userId, array $data): bool
    {
        $transaction = $this->repository->findForUser($id, $userId);

        if (!$transaction) {
            throw new ModelNotFoundException("Transação não encontrada ou acesso negado.");
        }

        return $this->repository->update($transaction, $data);
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
