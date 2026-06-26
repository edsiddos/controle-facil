<?php

namespace App\Repositories\Contracts;

use App\Models\Transaction;
use Illuminate\Support\Collection;

interface TransactionRepositoryInterface
{
    public function getAllForUser(int $userId): Collection;
    public function findForUser(int $id, int $userId): ?Transaction;
    public function create(array $data): Transaction;
    public function update(Transaction $transaction, array $data): bool;
    public function delete(Transaction $transaction): bool;
    public function listWebTable(int $userId, int $limit, int $offset, ?string $search = null): array;
}
