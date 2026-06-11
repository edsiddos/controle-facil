<?php

namespace App\Repositories\Eloquent;

use App\Models\AccountCard;
use App\Repositories\Contracts\AccountCardRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Classe AccountCardRepository
 * * Camada de abstração para manipulação dos dados dos cartões de conta (AccountCard).
 * Implementa a interface correspondente para garantir o desacoplamento do ORM Eloquent.
 */
class AccountCardRepository implements AccountCardRepositoryInterface
{
    /**
     * Busca todos os cartões vinculados a um usuário específico.
     *
     * @param int $userId ID do usuário dono dos cartões.
     * @return Collection Lista de cartões do usuário.
     */
    public function getAllForUser(int $userId): Collection
    {
        // Utiliza "Eager Loading" (with) para carregar o tipo de conta relacionado,
        // prevenindo o problema de performance conhecido como query N+1.
        return AccountCard::with('accountType')->forUser($userId)->get();
    }

    /**
     * Busca um cartão específico de um usuário específico.
     *
     * @param int $id ID do cartão que está sendo buscado.
     * @param int $userId ID do usuário que requisitou a busca.
     * @return AccountCard|null Retorna o objeto do cartão ou null caso não encontre.
     */
    public function findForUser(int $id, int $userId): ?AccountCard
    {
        // O escopo `forUser` garante que um usuário não consiga visualizar o cartão
        // de outro usuário, mesmo que adivinhe o ID do cartão.
        return AccountCard::forUser($userId)->find($id);
    }

    /**
     * Cria e persiste um novo registro de cartão no banco de dados.
     *
     * @param array $data Dados validados para a criação do cartão.
     * @return AccountCard Instância do cartão recém-criado.
     */
    public function create(array $data): AccountCard
    {
        return AccountCard::create($data);
    }

    /**
     * Atualiza os dados de um cartão existente.
     *
     * @param AccountCard $accountCard Instância do modelo do cartão a ser atualizado.
     * @param array $data Dados a serem atualizados.
     * @return bool Retorna true se a atualização foi bem-sucedida, false caso contrário.
     */
    public function update(AccountCard $accountCard, array $data): bool
    {
        return $accountCard->update($data);
    }

    /**
     * Remove um cartão do banco de dados.
     *
     * @param AccountCard $accountCard Instância do modelo do cartão a ser excluído.
     * @return bool Retorna true se a exclusão foi bem-sucedida, false caso contrário.
     */
    public function delete(AccountCard $accountCard): bool
    {
        return $accountCard->delete();
    }
}
