<?php

namespace App\Services;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryService
{
    protected CategoryRepositoryInterface $repository;

    public function __construct(CategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function listCategories(int $userId): Collection
    {
        return $this->repository->getAllForUser($userId);
    }

    public function registerCategory(int $userId, array $data): Category
    {
        $data['user_id'] = $userId;
        return $this->repository->create($data);
    }

    public function updateCategory(int $id, int $userId, array $data): bool
    {
        $category = $this->repository->findForUser($id, $userId);

        if (!$category) {
            throw new ModelNotFoundException("Categoria não encontrada.");
        }

        // Segurança extra para impedir modificação de categorias globais do sistema
        if ($category->user_id === null) {
            throw new \Exception("Não é possível alterar uma categoria global do sistema.");
        }

        return $this->repository->update($category, $data);
    }

    public function removeCategory(int $id, int $userId): bool
    {
        $category = $this->repository->findForUser($id, $userId);

        if (!$category) {
            throw new ModelNotFoundException("Categoria não encontrada.");
        }

        if ($category->user_id === null) {
            throw new \Exception("Não é possível remover uma categoria global do sistema.");
        }

        return $this->repository->delete($category);
    }

    public function listWebTable(int $userId, int $limit, int $offset, ?string $search = null): array
    {
        if ($limit < 1) $limit = 1;
        if ($offset < 0) $offset = 0;

        return $this->repository->listWebTable($userId, $limit, $offset, $search);
    }
}
