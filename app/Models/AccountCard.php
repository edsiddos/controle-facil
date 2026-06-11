<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountCard extends Model
{
    protected $table = 'account_cards';

    protected $fillable = [
        'user_id',
        'account_type_id',
        'name',
        'balance',
        'credit_limit',
        'available_limit',
        'closing_day',
        'due_day'
    ];

    /**
     * Relacionamento UM AccountCard (Conta/Cartão) tem UM User (Usuário)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento UM AccountCard (Conta/Cartão) tem UM AccountType (Tipo Conta/Cartão)
     */
    public function accountType(): BelongsTo
    {
        return $this->belongsTo(AccountType::class);
    }

    /**
     * Scope para buscar pelo usuário
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope para buscar pelo AccountType
     */
    public function scopeOfType(Builder $query, int $typeId): Builder
    {
        return $query->where('account_type_id', $typeId);
    }
}
