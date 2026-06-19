<?php

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Support\Collection;

interface CategoryRepositoryInterface
{
    public function getAllForUser(int $userId): Collection;
    public function findForUser(int $id, int $userId): ?Category;
    public function create(array $data): Category;
    public function update(Category $category, array $data): bool;
    public function delete(Category $category): bool;
    public function listWebTable(int $userId, int $limit, int $offset, ?string $search = null): array;
}