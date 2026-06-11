<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountType extends Model
{
    protected $table = 'account_types';

    protected $fillable = ['name'];

    /**
     * Relacionamento UM AccountType (Tipo Conta/Cartão) tem Muitos AccountCards (Conta/Cartão)
     */
    public function accountCards(): HasMany {
        return $this->hasMany(AccountCard::class);
    }
}
