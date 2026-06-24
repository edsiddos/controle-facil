<?php

namespace App\Services;

use App\Repositories\Contracts\AccountCardRepositoryInterface;
use App\Models\AccountCard;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class AccountCardService
 * Camada de serviço responsável por centralizar as regras de negócio
 * relacionadas a contas e cartões dos usuários.
 */
class AccountCardService
{
    /**
     * Instância do repositório de contas/cartões.
     */
    protected AccountCardRepositoryInterface $repository;

    /**
     * Construtor da classe com a injeção de dependência do repositório.
     *
     * @param AccountCardRepositoryInterface $repository
     */
    public function __construct(AccountCardRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Registra uma nova conta ou cartão para o usuário.
     *
     * @param int $userId ID do usuário que está criando a conta.
     * @param array $data Dados da conta/cartão (nome, tipo, limite, etc).
     * @return AccountCard O modelo criado e salvo no banco.
     */
    public function registerAccount(int $userId, array $data): AccountCard
    {
        // Vincula obrigatoriamente o registro ao usuário que fez a requisição
        $data['user_id'] = $userId;

        // Regra de negócio: Se o usuário informar um limite de crédito (é um cartão),
        // o limite disponível inicial deve ser exatamente igual ao limite total definido.
        if (isset($data['credit_limit'])) {
            $data['available_limit'] = $data['credit_limit'];
        }

        // Envia os dados tratados para o repositório persistir no banco de dados
        return $this->repository->create($data);
    }

    /**
     * Atualiza os dados de uma conta ou cartão existente.
     *
     * @param int $id ID da conta/cartão a ser atualizada.
     * @param int $userId ID do usuário dono do registro (garante segurança).
     * @param array $data Novos dados a serem aplicados.
     * @return bool True em caso de sucesso.
     * @throws ModelNotFoundException Se o registro não existir ou não pertencer ao usuário.
     */
    public function updateAccount(int $id, int $userId, array $data): bool
    {
        // Busca o registro garantindo que ele pertença ao usuário logado (evita que um usuário altere dados de outro)
        $accountCard = $this->repository->findForUser($id, $userId);

        if (!$accountCard) {
            throw new ModelNotFoundException("Conta ou cartão não encontrado.");
        }

        // Executa a atualização no repositório
        return $this->repository->update($accountCard, $data);
    }

    /**
     * Remove uma conta ou cartão do sistema.
     *
     * @param int $id ID da conta/cartão a ser excluída.
     * @param int $userId ID do usuário dono do registro (garante segurança).
     * @return bool True em caso de sucesso.
     * @throws ModelNotFoundException Se o registro não existir ou não pertencer ao usuário.
     */
    public function removeAccount(int $id, int $userId): bool
    {
        // Busca o registro garantindo o vínculo com o usuário antes de deletar
        $accountCard = $this->repository->findForUser($id, $userId);

        if (!$accountCard) {
            throw new ModelNotFoundException("Conta ou cartão não encontrado.");
        }

        // Executa a exclusão no repositório
        return $this->repository->delete($accountCard);
    }

    /**
     * Lista as contas/cartões com paginação e busca para uma estrutura de WebTable.
     *
     * @param int $userId ID do usuário dono das contas.
     * @param int $limit Quantidade de registros a retornar.
     * @param int $offset Quantidade de registros a pular.
     * @param string|null $search Termo de busca opcional (busca por nome).
     * @return array contendo os registros e o total para controle da paginação no front-end.
     */
    public function listWebTable(int $userId, int $limit, int $offset, ?string $search = null): array
    {
        // valida o menor valor aceito para limit e offset
        if ($limit < 1) {
            $limit = 1;
        }

        if ($offset < 0) {
            $offset = 0;
        }

        return $this->repository->listWebTable($userId, $limit, $offset, $search);
    }
}
