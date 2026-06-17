<?php

namespace App\Repositories\Contracts;

use App\Models\AccountCard;
use Illuminate\Support\Collection;

interface AccountCardRepositoryInterface
{
    public function getAllForUser(int $userId): Collection;
    public function findForUser(int $id, int $userId): ?AccountCard;
    public function create(array $data): AccountCard;
    public function update(AccountCard $accountCard, array $data): bool;
    public function delete(AccountCard $accountCard): bool;
    public function listWebTable(int $userId, int $limit, int $offset, ?string $search = null): array;
}
