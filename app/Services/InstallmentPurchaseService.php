<?php

namespace App\Services;

use App\Models\InstallmentPurchase;
use App\Repositories\Contracts\InstallmentPurchaseRepositoryInterface;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InstallmentPurchaseService
{

    public function __construct(protected InstallmentPurchaseRepositoryInterface $repository) {}

    public function storePurchase(array $data, int $userId): InstallmentPurchase
    {
        return DB::transaction(function () use ($data, $userId) {
            $data['user_id'] = $userId;

            // 1. Cria a compra mãe
            $purchase = $this->repository->create($data);

            // 2. Desmembra as parcelas na tabela de transações
            $installmentAmount = round($purchase->total_amount / $purchase->total_installments, 2);
            $baseDate = Carbon::parse($purchase->purchase_date);

            for ($i = 1; $i <= $purchase->total_installments; $i++) {
                // Ajusta os centavos na última parcela, se houver diferença de arredondamento
                if ($i === $purchase->total_installments) {
                    $totalGenerated = $installmentAmount * ($purchase->total_installments - 1);
                    $installmentAmount = $purchase->total_amount - $totalGenerated;
                }

                Transaction::create([
                    'user_id' => $userId,
                    'account_card_id' => $purchase->account_card_id,
                    'category_id' => $purchase->category_id,
                    'installment_purchase_id' => $purchase->id,
                    'description' => "{$purchase->description} ({$i}/{$purchase->total_installments})",
                    'amount' => $installmentAmount,
                    'transaction_date' => $baseDate->copy()->addMonths($i - 1)->format('Y-m-d'),
                    'status' => 'A PAGAR',
                    'is_recurring' => false
                ]);
            }

            return $purchase;
        });
    }

    public function updatePurchase(int $id, int $userId, array $data): bool
    {
        $purchase = $this->repository->findById($id, $userId);
        if (!$purchase) return false;

        return DB::transaction(function () use ($purchase, $data) {
            return $this->repository->update($purchase, $data);
        });
    }

    public function deletePurchase(int $id, int $userId): bool
    {
        $purchase = $this->repository->findById($id, $userId);
        if (!$purchase) return false;

        return DB::transaction(function () use ($purchase) {
            return $this->repository->delete($purchase);
        });
    }

    public function findForUser(int $id, int $userId): ?InstallmentPurchase
    {
        return $this->repository->findById($id, $userId);
    }

    /**
     * Lista as compras parceladas com paginação e busca para uma estrutura de WebTable.
     *
     * @param int $userId ID do usuário dono do parcelamento.
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
