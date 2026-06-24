<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class InstallmentPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_card_id',
        'category_id',
        'description',
        'total_amount',
        'total_installments',
        'purchase_date',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'datetime:d/m/Y',
            'total_amount' => 'decimal:2',
            'total_installments' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function accountCard(): BelongsTo
    {
        return $this->belongsTo(AccountCard::class, 'account_card_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'installment_purchase_id');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
}
