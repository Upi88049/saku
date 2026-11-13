<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Database\Factories\BudgetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'category_id',
    'monthly_limit',
    'alloc_percentage',
    'month',
    'year',
])]
class Budget extends Model
{
    /** @use HasFactory<BudgetFactory> */
    use BelongsToUser, HasFactory;

    protected function casts(): array
    {
        return [
            'monthly_limit' => 'decimal:2',
            'alloc_percentage' => 'decimal:2',
            'month' => 'integer',
            'year' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
