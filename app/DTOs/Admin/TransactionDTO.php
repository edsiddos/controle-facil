<?php

namespace App\DTOs\Admin;

use App\Http\Requests\Admin\StoreTransactionRequest;
use Carbon\Carbon;

readonly class TransactionDTO
{
    public function __construct(
        public int $accountCardId,
        public int $categoryId,
        public string $description,
        public float $amount,
        public string $transactionDate,
        public bool $isRecurring,
        public string $status,
        public ?int $installmentPurchaseId = null,
        public ?int $installmentNumber = null,
        public ?int $userId = null,
    ) {}

    public static function fromArray(array $data, ?int $userId = null): self
    {
        $rawDate = $data['transaction_date'];
        if (str_contains($rawDate, '/')) {
            $transactionDate = Carbon::createFromFormat('d/m/Y', $rawDate)->format('Y-m-d');
        } else {
            $transactionDate = Carbon::parse($rawDate)->format('Y-m-d');
        }

        return new self(
            accountCardId: (int) $data['account_card_id'],
            categoryId: (int) $data['category_id'],
            description: trim($data['description']),
            amount: (float) $data['amount'],
            transactionDate: $transactionDate,
            isRecurring: (bool) ($data['is_recurring'] ?? false),
            status: $data['status'],
            installmentPurchaseId: isset($data['installment_purchase_id']) ? (int) $data['installment_purchase_id'] : null,
            installmentNumber: isset($data['installment_number']) ? (int) $data['installment_number'] : null,
            userId: $userId ?? ($data['user_id'] ?? null),
        );
    }

    public static function fromRequest(StoreTransactionRequest $request, ?int $userId = null): self
    {
        return self::fromArray($request->validated(), $userId ?? $request->user()?->id);
    }

    public function toArray(): array
    {
        $data = [
            'account_card_id'          => $this->accountCardId,
            'category_id'              => $this->categoryId,
            'description'              => $this->description,
            'amount'                   => $this->amount,
            'transaction_date'         => $this->transactionDate,
            'is_recurring'             => $this->isRecurring,
            'status'                   => $this->status,
            'installment_purchase_id'  => $this->installmentPurchaseId,
            'installment_number'       => $this->installmentNumber,
        ];

        if ($this->userId !== null) {
            $data['user_id'] = $this->userId;
        }

        return $data;
    }
}
