<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Transaction extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'account_card_id',
        'category_id',
        'installment_purchase_id',
        'description',
        'amount',
        'transaction_date',
        'is_recurring',
        'installment_number',
        'status'
    ];

    protected function casts(): array
    {
        return ['transaction_date' => 'datetime:d/m/Y'];
    }

    /**
     * RELACIONAMENTO: Uma transação pertence a um Usuário.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * RELACIONAMENTO: Uma transação pertence a uma Conta ou Cartão de Crédito.
     */
    public function accountCard(): BelongsTo
    {
        return $this->belongsTo(AccountCard::class, 'account_card_id');
    }

    /**
     * RELACIONAMENTO: Uma transação pertence a uma Categoria.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * RELACIONAMENTO: Uma transação pode estar vinculada a uma compra parcelada ("mãe").
     * Será nulo se a transação for um gasto à vista ou uma receita isolada.
     */
    public function installmentPurchase(): BelongsTo
    {
        return $this->belongsTo(InstallmentPurchase::class, 'installment_purchase_id');
    }

    /**
     * SCOPE LOCAL: Filtra as transações pertencentes apenas ao usuário autenticado.
     * Mantém o padrão arquitetural utilizado para segurança de dados multi-tenant.
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * SCOPE LOCAL: Filtra transações por status (ex: 'pending', 'paid').
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * SCOPE LOCAL: Filtra transações por tipo (ex: 'expense', 'income').
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }
}
