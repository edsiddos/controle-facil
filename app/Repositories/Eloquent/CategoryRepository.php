<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Support\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAllForUser(int $userId): Collection
    {
        return Category::forUser($userId)->get();
    }

    public function findForUser(int $id, int $userId): ?Category
    {
        return Category::forUser($userId)->find($id);
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    public function delete(Category $category): bool
    {
        return $category->delete();
    }

    public function listWebTable(int $userId, int $limit, int $offset, ?string $search = null): array
    {
        $query = Category::forUser($userId);

        $query->when($search, function ($q) use ($search) {
            return $q->where('name', 'like', '%' . $search . '%');
        });

        $total = $query->count();

        $data = $query->skip($offset)
            ->take($limit)
            ->get();

        $data->transform(function ($item) {
            $item->type = match ($item->type) {
                'D' => 'Despesa',
                'R' => 'Receita',
                default => $item->type
            };

            return $item;
        });

        return [
            'data' => $data,
            'total' => $total
        ];
    }
}
