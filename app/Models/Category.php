<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $table = 'categories';

    protected $fillable = [
        'user_id',
        'name',
        'type'   // Ex: 'expense' ou 'income'
    ];

    /**
     * Relacionamento: Uma categoria pertence a um usuário (ou nulo se for global).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope local para buscar categorias visíveis ao usuário logado (Globais + Próprias)
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
                ->orWhereNull('user_id');
        });
    }
}
