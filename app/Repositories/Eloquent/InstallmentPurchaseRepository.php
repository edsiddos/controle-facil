<?php

namespace App\Repositories\Eloquent;

use App\Models\InstallmentPurchase;
use App\Repositories\Contracts\InstallmentPurchaseRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

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
        $data['purchase_date'] = Carbon::createFromFormat('d/m/Y', $data['purchase_date'])->format('Y-m-d');
        return InstallmentPurchase::create($data);
    }

    public function findById(int $id, int $userId): ?InstallmentPurchase
    {
        return InstallmentPurchase::where('user_id', $userId)->where('id', $id)->first();
    }

    public function update(InstallmentPurchase $installmentPurchase, array $data): bool
    {
        $data['purchase_date'] = Carbon::createFromFormat('d/m/Y', $data['purchase_date'])->format('Y-m-d');
        return $installmentPurchase->update($data);
    }

    public function delete(InstallmentPurchase $installmentPurchase): bool
    {
        return $installmentPurchase->delete();
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
