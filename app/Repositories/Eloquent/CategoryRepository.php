<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Classe CategoryRepository
 * Repositório Eloquent responsável pela abstração do acesso ao banco de dados da entidade Category.
 */
class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * Busca todas as categorias associadas ao usuário (incluindo as globais).
     * @param int $userId Identificador único do usuário.
     * @return Collection Coleção com as categorias retornadas do banco.
     */
    public function getAllForUser(int $userId): Collection
    {
        return Category::forUser($userId)->get();
    }

    /**
     * Localiza uma categoria específica vinculada ao usuário ou que seja global.
     * @param int $id Identificador único da categoria.
     * @param int $userId Identificador único do usuário.
     * @return Category|null Retorna o modelo Category encontrado ou nulo se não existir.
     */
    public function findForUser(int $id, int $userId): ?Category
    {
        return Category::forUser($userId)->find($id);
    }

    /**
     * Insere uma nova categoria no banco de dados.
     * @param array $data Dados a serem persistidos no novo registro.
     * @return Category Instância do modelo Category recém-criado.
     */
    public function create(array $data): Category
    {
        return Category::create($data);
    }

    /**
     * Atualiza os campos de uma instância ativa da categoria no banco de dados.
     * @param Category $category Instância da model que sofrerá as alterações.
     * @param array $data Dados atualizados que serão aplicados à model.
     * @return bool Verdadeiro se a atualização foi realizada com sucesso.
     */
    public function update(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    /**
     * Remove o registro da categoria do banco de dados (respeitando soft deletes).
     * @param Category $category Instância da model a ser deletada.
     * @return bool Verdadeiro se a exclusão foi realizada com sucesso.
     */
    public function delete(Category $category): bool
    {
        return $category->delete();
    }

    /**
     * Consulta, filtra, pagina e formata as categorias para renderização de tabelas na web.
     * @param int $userId Identificador do usuário para delimitar o escopo da busca.
     * @param int $limit Limite de registros por página.
     * @param int $offset Deslocamento inicial da consulta.
     * @param string|null $search Termo opcional para busca textual no nome da categoria.
     * @return array Retorna uma estrutura com 'data' (coleção formatada) e 'total' (contagem geral).
     */
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