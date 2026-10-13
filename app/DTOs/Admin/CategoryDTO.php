<?php

namespace App\DTOs\Admin;

use App\Http\Requests\Admin\StoreCategoryRequest;

readonly class CategoryDTO
{
    public function __construct(
        public string $name,
        public string $type,
        public ?string $icon = null,
        public ?int $userId = null,
    ) {}

    public static function fromArray(array $data, ?int $userId = null): self
    {
        return new self(
            name: trim($data['name']),
            type: $data['type'],
            icon: $data['icon'] ?? null,
            userId: $userId ?? ($data['user_id'] ?? null),
        );
    }

    public static function fromRequest(StoreCategoryRequest $request, ?int $userId = null): self
    {
        return self::fromArray($request->validated(), $userId ?? $request->user()?->id);
    }

    public function toArray(): array
    {
        $data = [
            'name' => $this->name,
            'type' => $this->type,
            'icon' => $this->icon,
        ];

        if ($this->userId !== null) {
            $data['user_id'] = $this->userId;
        }

        return $data;
    }
}
