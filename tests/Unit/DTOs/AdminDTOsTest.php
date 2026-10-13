<?php

namespace Tests\Unit\DTOs;

use App\DTOs\Admin\AccountCardDTO;
use App\DTOs\Admin\CategoryDTO;
use App\DTOs\Admin\InstallmentPurchaseDTO;
use App\DTOs\Admin\TransactionDTO;
use App\Http\Requests\Admin\StoreAccountCardRequest;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\StoreInstallmentPurchaseRequest;
use App\Http\Requests\Admin\StoreTransactionRequest;
use Tests\TestCase;

class AdminDTOsTest extends TestCase
{
    public function test_category_dto_from_array_and_to_array()
    {
        $dto = CategoryDTO::fromArray([
            'name' => ' Alimentação ',
            'type' => 'D',
            'icon' => 'utensils',
        ], 10);

        $this->assertEquals('Alimentação', $dto->name);
        $this->assertEquals('D', $dto->type);
        $this->assertEquals('utensils', $dto->icon);
        $this->assertEquals(10, $dto->userId);

        $array = $dto->toArray();
        $this->assertEquals([
            'name' => 'Alimentação',
            'type' => 'D',
            'icon' => 'utensils',
            'user_id' => 10,
        ], $array);
    }

    public function test_account_card_dto_calculates_available_limit()
    {
        $dto = AccountCardDTO::fromArray([
            'name' => ' Nubank ',
            'account_type_id' => 2,
            'balance' => 150.50,
            'credit_limit' => 5000.00,
        ], 5);

        $this->assertEquals('Nubank', $dto->name);
        $this->assertEquals(2, $dto->accountTypeId);
        $this->assertEquals(150.50, $dto->balance);
        $this->assertEquals(5000.00, $dto->creditLimit);
        $this->assertEquals(5000.00, $dto->availableLimit);

        $array = $dto->toArray();
        $this->assertEquals(5000.00, $array['available_limit']);
    }

    public function test_installment_purchase_dto_formats_date()
    {
        $dto = InstallmentPurchaseDTO::fromArray([
            'account_card_id' => 1,
            'description' => ' Notebook ',
            'total_amount' => 3000.00,
            'total_installments' => 10,
            'purchase_date' => '15/10/2026',
            'category_id' => 3,
        ], 1);

        $this->assertEquals('Notebook', $dto->description);
        $this->assertEquals('2026-10-15', $dto->purchaseDate);
        $this->assertEquals(3000.00, $dto->totalAmount);
        $this->assertEquals(10, $dto->totalInstallments);
    }

    public function test_transaction_dto_from_array()
    {
        $dto = TransactionDTO::fromArray([
            'account_card_id' => 2,
            'category_id' => 4,
            'description' => ' Mercado ',
            'amount' => 250.75,
            'transaction_date' => '06/10/2026',
            'is_recurring' => true,
            'status' => 'PAGO',
        ], 1);

        $this->assertEquals('Mercado', $dto->description);
        $this->assertEquals('2026-10-06', $dto->transactionDate);
        $this->assertTrue($dto->isRecurring);
        $this->assertEquals('PAGO', $dto->status);
    }
}
