<?php

namespace App\Services;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Classe CategoryService
 * Camada de serviço responsável por centralizar as regras de negócio relacionadas a categorias.
 */
class CategoryService
{
    protected CategoryRepositoryInterface $repository;

    /**
     * Construtor da classe.
     * @param CategoryRepositoryInterface $repository Instância do repositório de categorias via injeção de dependência.
     */
    public function __construct(CategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Lista todas as categorias acessíveis para um determinado usuário.
     * @param int $userId Identificador único do usuário.
     * @return Collection Retorna uma coleção contendo as categorias encontradas.
     */
    public function listCategories(int $userId): Collection
    {
        return $this->repository->getAllForUser($userId);
    }

    /**
     * Registra uma nova categoria vinculada ao usuário informado.
     * @param int $userId Identificador único do usuário dono da categoria.
     * @param array $data Dados da categoria a ser criada (ex: nome, tipo, ícone).
     * @return Category Retorna a instância do modelo Category criado.
     */
    public function registerCategory(int $userId, array $data): Category
    {
        $data['user_id'] = $userId;
        return $this->repository->create($data);
    }

    /**
     * Atualiza os dados de uma categoria específica de um usuário.
     * @param int $id Identificador único da categoria a ser modificada.
     * @param int $userId Identificador único do usuário que está solicitando a alteração.
     * @param array $data Dados atualizados a serem aplicados.
     * @throws ModelNotFoundException Lançada caso a categoria não seja encontrada para o usuário.
     * @throws \Exception Lançada se houver uma tentativa de alterar uma categoria global do sistema.
     * @return bool Retorna verdadeiro em caso de sucesso na atualização.
     */
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

    /**
     * Remove uma categoria específica de um usuário (soft delete).
     * @param int $id Identificador único da categoria a ser removida.
     * @param int $userId Identificador único do usuário que está solicitando a exclusão.
     * @throws ModelNotFoundException Lançada caso a categoria não seja encontrada para o usuário.
     * @throws \Exception Lançada se houver uma tentativa de remover uma categoria global do sistema.
     * @return bool Retorna verdadeiro em caso de sucesso na remoção.
     */
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

    /**
     * Obtém uma listagem paginada e filtrada de categorias para exibição em tabelas web.
     * @param int $userId Identificador único do usuário.
     * @param int $limit Quantidade máxima de registros a retornar (limite da página).
     * @param int $offset Quantidade de registros a pular (deslocamento da paginação).
     * @param string|null $search Termo de busca opcional para filtrar pelo nome da categoria.
     * @return array Retorna um array estruturado contendo os dados e o total de registros.
     */
    public function listWebTable(int $userId, int $limit, int $offset, ?string $search = null): array
    {
        if ($limit < 1) $limit = 1;
        if ($offset < 0) $offset = 0;

        return $this->repository->listWebTable($userId, $limit, $offset, $search);
    }
}