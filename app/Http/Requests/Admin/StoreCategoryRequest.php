<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:255'],
            'type'  => ['required', 'string', 'in:D,R']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Informe o nome da categoria',
            'name.string' => 'Informe um nome de categoria válido',
            'name.max' => 'Nome da categoria muito grande.',
            'type.required' => 'Informe o campo tipo',
            'type.in' => 'Informe os tipo RECEITA ou DESPESA'
        ];
    }
}
