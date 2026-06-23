<?php

namespace App\Repositories\Eloquent;

use App\Models\InstallmentPurchase;
use App\Repositories\Contracts\InstallmentPurchaseRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class InstallmentPurchaseRepository implements InstallmentPurchaseRepositoryInterface
{
    public function getAllPaginated(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return InstallmentPurchase::where('user_id', $userId)
            ->with(['category', 'accountCard'])
            ->orderBy('purchase_date', 'desc')
            ->paginate($perPage);
    }

    public function create(array $data): InstallmentPurchase
    {
        return InstallmentPurchase::create($data);
    }

    public function findById(int $id, int $userId): ?InstallmentPurchase
    {
        return InstallmentPurchase::where('user_id', $userId)->where('id', $id)->first();
    }

    public function update(InstallmentPurchase $installmentPurchase, array $data): bool
    {
        return $installmentPurchase->update($data);
    }

    public function delete(InstallmentPurchase $installmentPurchase): bool
    {
        return $installmentPurchase->delete();
    }

    public function listWebTable(int $userId): array
    {
        $purchases = $this->getAllPaginated($userId);

        return [
            'headers' => [
                ['text' => 'Descrição', 'value' => 'description'],
                ['text' => 'Categoria', 'value' => 'category_name'],
                ['text' => 'Conta/Cartão', 'value' => 'account_name'],
                ['text' => 'Valor Total', 'value' => 'total_amount'],
                ['text' => 'Parcelas', 'value' => 'installments_display'],
                ['text' => 'Data da Compra', 'value' => 'purchase_date'],
                ['text' => 'Ações', 'value' => 'actions']
            ],
            'body' => $purchases->through(fn($item) => [
                'id' => $item->id,
                'description' => $item->description,
                'category_name' => $item->category?->name ?? 'Sem Categoria',
                'account_name' => $item->accountCard?->name ?? 'Sem Conta',
                'total_amount' => 'R$ ' . number_format($item->total_amount, 2, ',', '.'),
                'installments_display' => $item->total_installments . 'x',
                'purchase_date' => $item->purchase_date->format('d/m/Y'),
            ])
        ];
    }
}