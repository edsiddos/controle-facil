<?php

namespace App\Repositories\Eloquent;

use App\Models\Transaction;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Classe TransactionRepository
 * Abstração de banco de dados do Eloquent para a entidade de Transações.
 */
class TransactionRepository implements TransactionRepositoryInterface
{
    /**
     * Busca todas as transações de um usuário (com eager loading).
     *
     * @param int $userId ID do usuário logado.
     * @return Collection
     */
    public function getAllForUser(int $userId): Collection
    {
        return Transaction::with(['accountCard', 'category'])->forUser($userId)->get();
    }

    /**
     * Busca uma transação específica garantindo o vínculo com o usuário.
     *
     * @param int $id ID da transação.
     * @param int $userId ID do usuário dono.
     * @return Transaction|null
     */
    public function findForUser(int $id, int $userId): ?Transaction
    {
        return Transaction::forUser($userId)->find($id);
    }

    /**
     * Persiste uma nova transação no banco de dados.
     *
     * @param array $data Dados validados.
     * @return Transaction
     */
    public function create(array $data): Transaction
    {
        return Transaction::create($data);
    }

    /**
     * Atualiza o registro de uma transação.
     *
     * @param Transaction $transaction Instância da transação.
     * @param array $data Dados atualizados.
     * @return bool
     */
    public function update(Transaction $transaction, array $data): bool
    {
        return $transaction->update($data);
    }

    /**
     * Remove de forma lógica (Soft Delete) uma transação do banco.
     *
     * @param Transaction $transaction Instância da transação a deletar.
     * @return bool
     */
    public function delete(Transaction $transaction): bool
    {
        return $transaction->delete();
    }

    /**
     * Alimenta a estrutura de paginação e busca da WebTable no frontend.
     *
     * @param int $userId ID do dono dos registros.
     * @param int $limit Quantidade de registros por página.
     * @param int $offset Quantidade de registros pulados.
     * @param string|null $search Termo opcional para busca textual na descrição.
     * @return array Estrutura contendo os dados resultantes e a contagem total.
     */
    public function listWebTable(int $userId, int $limit, int $offset, ?string $search = null): array
    {
        $query = Transaction::forUser($userId)->with(['accountCard', 'category']);

        $query->when($search, function ($q) use ($search) {
            return $q->where('description', 'like', '%' . $search . '%');
        });

        $total = $query->count();

        $data = $query->skip($offset)
            ->take($limit)
            ->orderBy('transaction_date', 'desc')
            ->get();

        return [
            'data' => $data,
            'total' => $total,
        ];
    }
}
