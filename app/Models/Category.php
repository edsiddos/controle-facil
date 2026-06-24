<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Classe Category
 * Modelo do Eloquent que representa a tabela 'categories' e encapsula as interações com a entidade.
 */
class Category extends Model
{
    use SoftDeletes;

    protected $table = 'categories';

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'icon'
    ];

    /**
     * Relacionamento: Uma categoria pertence a um usuário (ou nulo se for global).
     * @return BelongsTo Definição da relação de pertença com o modelo User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope local para buscar categorias visíveis ao usuário logado (Globais + Próprias)
     * @param Builder $query Objeto construtor de consultas do Eloquent (injetado automaticamente).
     * @param int $userId Identificador do usuário atual para filtrar os registros.
     * @return Builder Construtor de consultas modificado com a cláusula condicional aplicada.
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
                ->orWhereNull('user_id');
        });
    }
}