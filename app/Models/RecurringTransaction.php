<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecurringTransaction extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'account_card_id',
        'category_id',
        'description',
        'amount',
        'start_date',
        'installment_number',
        'status',
        'cancelled_at',
    ];

    /**
     * Conversão de tipos de atributos.
     */
    protected function casts(): array
    {
        return [
            'start_date'   => 'date:d/m/Y',
            'cancelled_at' => 'datetime:d/m/Y H:i',
            'amount'       => 'decimal:2',
        ];
    }

    /**
     * RELACIONAMENTO: A transação recorrente pertence a um Usuário.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * RELACIONAMENTO: Pertence a uma Conta/Cartão.
     */
    public function accountCard(): BelongsTo
    {
        return $this->belongsTo(AccountCard::class, 'account_card_id');
    }

    /**
     * RELACIONAMENTO: Pertence a uma Categoria.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * RELACIONAMENTO: Possui muitas transações geradas.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'recurring_transaction_id');
    }

    /**
     * SCOPE LOCAL: Filtra registros do usuário logado (Isolamento Multi-tenant).
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * SCOPE LOCAL: Filtra apenas assinaturas/planos ativos.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'ATIVO');
    }
}
