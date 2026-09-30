<?php

namespace App\Repositories\Eloquent;

use App\Models\RecurringTransaction;
use App\Repositories\Contracts\RecurringTransactionRepositoryInterface;
use Illuminate\Support\Collection;

class RecurringTransactionRepository implements RecurringTransactionRepositoryInterface
{
    public function getAllForUser(int $userId): Collection
    {
        return RecurringTransaction::with(['accountCard', 'category'])->forUser($userId)->get();
    }

    public function findForUser(int $id, int $userId): ?RecurringTransaction
    {
        return RecurringTransaction::forUser($userId)->find($id);
    }

    public function create(array $data): RecurringTransaction
    {
        return RecurringTransaction::create($data);
    }

    public function update(RecurringTransaction $recurringTransaction, array $data): bool
    {
        return $recurringTransaction->update($data);
    }

    public function delete(RecurringTransaction $recurringTransaction): bool
    {
        return $recurringTransaction->delete();
    }

    public function listWebTable(int $userId, int $limit, int $offset, ?string $search = null): array
    {
        $query = RecurringTransaction::forUser($userId)->with(['accountCard', 'category']);

        $query->when($search, function ($q) use ($search) {
            return $q->where('description', 'like', '%' . $search . '%');
        });

        $total = $query->count();

        $data = $query->skip($offset)
            ->take($limit)
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'data' => $data,
            'total' => $total,
        ];
    }
}
