<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\AccountCard;
use Illuminate\Foundation\Http\FormRequest;

class StoreAccountCardRequest extends FormRequest
{
    /**
     * Determina se o usuário tem autorização para fazer esta requisição.
     */
    public function authorize(): bool
    {
        // Retorna true assumindo que o controle de acesso principal já é feito por Middlewares de rota
        return true;
    }

    /**
     * Define as regras de validação que serão aplicadas aos dados da requisição.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // O nome é obrigatório, deve ser uma string válida e ter no máximo 255 caracteres
            'name' => 'required|string|max:255',

            // O tipo da conta é obrigatório e deve obrigatoriamente existir na coluna 'id' da tabela 'account_types'
            'account_type_id' => 'required|exists:account_types,id',

            // O saldo inicial é obrigatório e deve ser um valor numérico
            'balance' => 'required|numeric',

            // Validação Condicional: O limite de crédito é opcional (nullable), numérico, 
            // mas torna-se OBRIGATÓRIO se o 'account_type_id' for igual ao ID de Cartão de Crédito.
            // Usamos a constante mapeada no Model para evitar números mágicos (hardcoding).
            'credit_limit' => 'nullable|numeric|required_if:account_type_id,' . AccountCard::TYPE_CREDIT_CARD,
        ];
    }
}
