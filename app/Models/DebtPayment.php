<?php

namespace App\Models;

use Database\Factories\DebtPaymentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'debt_receivable_id',
    'transaction_id',
    'paid_amount',
    'payment_date',
    'note',
])]
class DebtPayment extends Model
{
    /** @use HasFactory<DebtPaymentFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'paid_amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    public function debtReceivable(): BelongsTo
    {
        return $this->belongsTo(DebtReceivable::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
