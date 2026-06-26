<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determina se o usuário tem autorização para fazer esta requisição.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Define as regras de validação aplicadas aos dados de movimentação.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'account_card_id'          => 'required|exists:account_cards,id',
            'category_id'              => 'required|exists:categories,id',
            'installment_purchase_id'  => 'nullable|integer',
            'description'              => 'required|string|max:255',
            'amount'                   => 'required|numeric|min:0.01',
            'transaction_date'         => 'required|date',
            'is_recurring'             => 'required|boolean',
            'installment_number'       => 'nullable|integer|min:1',
            'status'                   => 'required|in:A PAGAR,PAGO',
        ];
    }

    /**
     * Define as mensagens personalizadas para os erros de validação.
     */
    public function messages(): array
    {
        return [
            'account_card_id.required' => 'Informe a conta ou cartão correspondente.',
            'account_card_id.exists'   => 'A conta ou cartão selecionado é inválido.',
            'category_id.required'     => 'Selecione uma categoria para a transação.',
            'category_id.exists'       => 'A categoria selecionada não existe.',
            'description.required'     => 'Informe uma descrição para a transação.',
            'description.max'          => 'A descrição não pode ter mais que 255 caracteres.',
            'amount.required'          => 'O valor da transação deve ser informado.',
            'amount.numeric'           => 'O valor informado deve ser um número válido.',
            'ammout.min'               => 'O valor deve ser maior',
            'transaction_date.required' => 'Informe a data da transação.',
            'transaction_date.date'    => 'A data informada não está em um formato válido.',
            'is_recurring.required'    => 'Informe se a transação é uma assinatura/recorrente.',
            'status.required'          => 'Defina o status da transação.',
            'status.in'                => 'O status selecionado é inválido (Permitido: A PAGAR ou PAGO).'
        ];
    }
}
