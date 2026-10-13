<?php

namespace App\DTOs\Admin;

use App\Http\Requests\Admin\StoreInstallmentPurchaseRequest;
use Carbon\Carbon;

readonly class InstallmentPurchaseDTO
{
    public function __construct(
        public int $accountCardId,
        public string $description,
        public float $totalAmount,
        public int $totalInstallments,
        public string $purchaseDate,
        public int $categoryId,
        public ?int $userId = null,
    ) {}

    public static function fromArray(array $data, ?int $userId = null): self
    {
        $rawDate = $data['purchase_date'];
        if (str_contains($rawDate, '/')) {
            $purchaseDate = Carbon::createFromFormat('d/m/Y', $rawDate)->format('Y-m-d');
        } else {
            $purchaseDate = Carbon::parse($rawDate)->format('Y-m-d');
        }

        return new self(
            accountCardId: (int) $data['account_card_id'],
            description: trim($data['description']),
            totalAmount: (float) $data['total_amount'],
            totalInstallments: (int) $data['total_installments'],
            purchaseDate: $purchaseDate,
            categoryId: (int) $data['category_id'],
            userId: $userId ?? ($data['user_id'] ?? null),
        );
    }

    public static function fromRequest(StoreInstallmentPurchaseRequest $request, ?int $userId = null): self
    {
        return self::fromArray($request->validated(), $userId ?? $request->user()?->id);
    }

    public function toArray(): array
    {
        $data = [
            'account_card_id'    => $this->accountCardId,
            'description'        => $this->description,
            'total_amount'       => $this->totalAmount,
            'total_installments' => $this->totalInstallments,
            'purchase_date'      => $this->purchaseDate,
            'category_id'        => $this->categoryId,
        ];

        if ($this->userId !== null) {
            $data['user_id'] = $this->userId;
        }

        return $data;
    }
}
