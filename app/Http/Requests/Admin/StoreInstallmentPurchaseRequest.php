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
            'account_card_id' => ['required', 'exists:account_cards,id'],
            'description' => ['required', 'string', 'max:255'],
            'total_amount' => ['required', 'numeric', 'min:0.01'],
            'total_installments' => ['required', 'integer', 'min:2', 'max:120'],
            'purchase_date' => ['required', 'date_format:d/m/Y'],
            'category_id' => ['required', 'exists:categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'account_card_id.required' => 'Informe o campo Conta / Cartão',
            'account_card_id.exists' => 'Conta / Cartão não reconhecido',
            'description.required' => 'Informe a Descrição',
            'description.max' => 'Tamanho do campo excedido',
            'total_amount.required' => 'Total da Compra',
            'total_amount.numeric' => 'Valor inválido',
            'total_amount.min' => 'Informe um valor diferente de 0.00',
            'total_installments.required' => 'Informe a quantidade parcelas',
            'total_installments.integer' => 'Quantidade parcelas inválida',
            'total_installments.min' => 'Quantidade de parcelas deve ser maior igual a 2',
            'total_installments.max' => 'Quantidade de parcelas dever ser menor que 120',
            'purchase_date.required' => 'Informe a data compra',
            'purchase_date.date_format' => 'Data Inválida',
            'category_id.required' => 'Informe a categoria da compra',
            'category_id.exists' => 'Categoria não reconhecida'
        ];
    }
}