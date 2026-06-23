<?php

namespace App\Repositories\Contracts;

use App\Models\InstallmentPurchase;
use Illuminate\Pagination\LengthAwarePaginator;

interface InstallmentPurchaseRepositoryInterface
{
    public function getAllPaginated(int $userId, int $perPage = 15): LengthAwarePaginator;
    public function create(array $data): InstallmentPurchase;
    public function findById(int $id, int $userId): ?InstallmentPurchase;
    public function update(InstallmentPurchase $installmentPurchase, array $data): bool;
    public function delete(InstallmentPurchase $installmentPurchase): bool;
    public function listWebTable(int $userId, int $limit, int $offset, ?string $search = null): array;
}