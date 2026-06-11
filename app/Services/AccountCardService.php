<?php

namespace App\Services;

use App\Repositories\Contracts\AccountCardRepositoryInterface;
use App\Models\AccountCard;
use Illuminate\Support\Collection;

class AccountCardService
{
    protected AccountCardRepositoryInterface $repository;

    public function __construct(AccountCardRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function listAccounts(int $userId): Collection
    {
        return $this->repository->getAllForUser($userId);
    }

    public function registerAccount(int $userId, array $data): AccountCard
    {
        $data['user_id'] = $userId;

        // Regra de negócio: Se for cartão, o limite disponível inicial é igual ao limite total
        if (isset($data['credit_limit'])) {
            $data['available_limit'] = $data['credit_limit'];
        }

        return $this->repository->create($data);
    }

    public function updateAccount(int $id, int $userId, array $data): bool
    {
        $accountCard = $this->repository->findForUser($id, $userId);
        if (!$accountCard) {
            throw new \Exception("Conta ou cartão não encontrado.");
        }

        return $this->repository->update($accountCard, $data);
    }

    public function removeAccount(int $id, int $userId): bool
    {
        $accountCard = $this->repository->findForUser($id, $userId);
        if (!$accountCard) {
            throw new \Exception("Conta ou cartão não encontrado.");
        }

        return $this->repository->delete($accountCard);
    }
}
