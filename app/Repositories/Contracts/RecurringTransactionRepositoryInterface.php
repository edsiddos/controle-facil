<?php

namespace App\Repositories\Contracts;

use App\Models\RecurringTransaction;
use Illuminate\Support\Collection;

interface RecurringTransactionRepositoryInterface
{
    public function getAllForUser(int $userId): Collection;
    public function findForUser(int $id, int $userId): ?RecurringTransaction;
    public function create(array $data): RecurringTransaction;
    public function update(RecurringTransaction $recurringTransaction, array $data): bool;
    public function delete(RecurringTransaction $recurringTransaction): bool;
    public function listWebTable(int $userId, int $limit, int $offset, ?string $search = null): array;
}
