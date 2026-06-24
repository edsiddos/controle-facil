<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Override;

/**
 * Classe StoreCategoryRequest
 * Camada de validação (Form Request) responsável por interceptar a requisição e validar
 * os dados enviados para o cadastro ou atualização de uma categoria.
 */
class StoreCategoryRequest extends FormRequest
{
    /**
     * Determina se o usuário atual está autorizado a fazer esta requisição.
     * @return bool Retorna verdadeiro se a requisição for autorizada.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Define as regras de validação que serão aplicadas aos dados da requisição.
     * @return array Matriz contendo os campos e suas respectivas regras de validação.
     */
    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:255'],
            'type'  => ['required', 'string', 'in:D,R'],
            'icon'  => ['nullable', 'string']
        ];
    }

    /**
     * Define as mensagens de erro customizadas caso alguma regra de validação falhe.
     * @return array Matriz associando os campos e regras às suas respectivas mensagens em português.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Informe o nome da categoria',
            'name.string' => 'Informe um nome de categoria válido',
            'name.max' => 'Nome da categoria muito grande.',
            'type.required' => 'Informe o campo tipo',
            'type.in' => 'Informe os tipo RECEITA ou DESPESA',
            'icon.string' => 'Ícone informado inválido'
        ];
    }
}