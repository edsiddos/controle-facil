<?php

namespace App\Repositories\Eloquent;

use App\Models\InstallmentPurchase;
use App\Repositories\Contracts\InstallmentPurchaseRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
<<<<<<< HEAD
=======
use Carbon\Carbon;
>>>>>>> f2b1e5a9b5b21ad1f403020152ceca67e381a87c

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
<<<<<<< HEAD
=======
        $data['purchase_date'] = Carbon::createFromFormat('d/m/Y', $data['purchase_date'])->format('Y-m-d');
>>>>>>> f2b1e5a9b5b21ad1f403020152ceca67e381a87c
        return InstallmentPurchase::create($data);
    }

    public function findById(int $id, int $userId): ?InstallmentPurchase
    {
        return InstallmentPurchase::where('user_id', $userId)->where('id', $id)->first();
    }

    public function update(InstallmentPurchase $installmentPurchase, array $data): bool
    {
<<<<<<< HEAD
=======
        $data['purchase_date'] = Carbon::createFromFormat('d/m/Y', $data['purchase_date'])->format('Y-m-d');
>>>>>>> f2b1e5a9b5b21ad1f403020152ceca67e381a87c
        return $installmentPurchase->update($data);
    }

    public function delete(InstallmentPurchase $installmentPurchase): bool
    {
        return $installmentPurchase->delete();
    }

<<<<<<< HEAD
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
=======
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
        $query = InstallmentPurchase::forUser($userId);

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
>>>>>>> f2b1e5a9b5b21ad1f403020152ceca67e381a87c
