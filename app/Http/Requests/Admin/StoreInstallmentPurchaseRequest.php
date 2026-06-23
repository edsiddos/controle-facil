<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreInstallmentPurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:255'],
            'total_amount' => ['required', 'numeric', 'min:0.01'],
            'total_installments' => ['required', 'integer', 'min:2', 'max:120'],
            'category_id' => ['required', 'exists:categories,id'],
            'account_card_id' => ['required', 'exists:accounts_cards,id'],
            'purchase_date' => ['required', 'date'],
        ];
    }
}