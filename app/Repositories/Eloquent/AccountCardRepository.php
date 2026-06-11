<?php
namespace App\Repositories\Eloquent;

use App\Models\AccountCard;
use App\Repositories\Contracts\AccountCardRepositoryInterface;
use Illuminate\Support\Collection;

class AccountCardRepository implements AccountCardRepositoryInterface
{
    public function getAllForUser(int $userId): Collection
    {
        return AccountCard::with('accountType')->forUser($userId)->get();
    }

    public function findForUser(int $id, int $userId): ?AccountCard
    {
        return AccountCard::forUser($userId)->find($id);
    }

    public function create(array $data): AccountCard
    {
        return AccountCard::create($data);
    }

    public function update(AccountCard $accountCard, array $data): bool
    {
        return $accountCard->update($data);
    }

    public function delete(AccountCard $accountCard): bool
    {
        return $accountCard->delete();
    }
}