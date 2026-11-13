<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Database\Factories\WalletFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'name',
    'type',
    'current_balance',
    'is_excluded_from_sts',
    'account_number',
    'color',
    'icon',
    'is_active',
])]
class Wallet extends Model
{
    /** @use HasFactory<WalletFactory> */
    use BelongsToUser, HasFactory;

    protected function casts(): array
    {
        return [
            'current_balance' => 'decimal:2',
            'is_excluded_from_sts' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
