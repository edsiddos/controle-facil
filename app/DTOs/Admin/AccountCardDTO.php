<?php

namespace App\DTOs\Admin;

use App\Http\Requests\Admin\StoreAccountCardRequest;

readonly class AccountCardDTO
{
    public function __construct(
        public string $name,
        public int $accountTypeId,
        public float $balance,
        public ?float $creditLimit = null,
        public ?float $availableLimit = null,
        public ?int $userId = null,
    ) {}

    public static function fromArray(array $data, ?int $userId = null): self
    {
        $creditLimit = isset($data['credit_limit']) ? (float) $data['credit_limit'] : null;
        $availableLimit = isset($data['available_limit']) ? (float) $data['available_limit'] : $creditLimit;

        return new self(
            name: trim($data['name']),
            accountTypeId: (int) $data['account_type_id'],
            balance: (float) $data['balance'],
            creditLimit: $creditLimit,
            availableLimit: $availableLimit,
            userId: $userId ?? ($data['user_id'] ?? null),
        );
    }

    public static function fromRequest(StoreAccountCardRequest $request, ?int $userId = null): self
    {
        return self::fromArray($request->validated(), $userId ?? $request->user()?->id);
    }

    public function toArray(): array
    {
        $data = [
            'name'            => $this->name,
            'account_type_id' => $this->accountTypeId,
            'balance'         => $this->balance,
        ];

        if ($this->userId !== null) {
            $data['user_id'] = $this->userId;
        }

        if ($this->creditLimit !== null) {
            $data['credit_limit'] = $this->creditLimit;
            $data['available_limit'] = $this->availableLimit ?? $this->creditLimit;
        }

        return $data;
    }
}
