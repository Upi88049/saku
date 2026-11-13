<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Carbon\Carbon;
use Database\Factories\DebtReceivableFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'source_transaction_id',
    'type',
    'contact_name',
    'contact_phone',
    'total_amount',
    'remaining_amount',
    'due_date',
    'status',
    'note',
])]
class DebtReceivable extends Model
{
    /** @use HasFactory<DebtReceivableFactory> */
    use BelongsToUser, HasFactory;

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
            'due_date' => 'date',
        ];
    }

    public function payments(): HasMany
    {
        return $this->hasMany(DebtPayment::class);
    }

    public function sourceTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'source_transaction_id');
    }

    public function getDueStatusAttribute(): array
    {
        if ($this->status === 'settled') {
            return [
                'label' => 'Lunas',
                'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
                'badge' => 'settled',
            ];
        }

        if (! $this->due_date) {
            return [
                'label' => 'Tanpa tenggat',
                'class' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                'badge' => 'none',
            ];
        }

        $today = Carbon::today();
        $due = Carbon::parse($this->due_date)->startOfDay();
        $diffDays = $today->diffInDays($due, false);

        if ($diffDays < 0) {
            return [
                'label' => 'Terlambat '.abs((int) $diffDays).' hari',
                'class' => 'bg-[#FDE1DF] text-[#9E2019] dark:bg-rose-950/60 dark:text-rose-300',
                'badge' => 'overdue',
            ];
        }

        if ($diffDays <= 3) {
            return [
                'label' => $diffDays == 0 ? 'Hari ini' : $diffDays.' hari lagi',
                'class' => 'bg-[#FFF1CC] text-[#7A5600] dark:bg-amber-950/60 dark:text-amber-300',
                'badge' => 'warning',
            ];
        }

        return [
            'label' => $diffDays.' hari lagi',
            'class' => 'bg-[#DDF5EC] text-[#0B6B52] dark:bg-emerald-950/60 dark:text-emerald-300',
            'badge' => 'safe',
        ];
    }

    public function getWhatsAppReminderUrl(): string
    {
        $amountFormatted = 'Rp '.number_format($this->remaining_amount, 0, ',', '.');
        $user = $this->user;
        $paymentInfo = $user?->payment_info ? "\n\nPembayaran dapat ditransfer ke: ".$user->payment_info : '';

        $message = "Halo {$this->contact_name}, mengingatkan kembali mengenai catatan {$this->note} sebesar {$amountFormatted}.{$paymentInfo}\n\nTerima kasih banyak! 🙏";

        $phone = preg_replace('/[^0-9]/', '', (string) $this->contact_phone);
        if ($phone && str_starts_with($phone, '0')) {
            $phone = '62'.substr($phone, 1);
        }

        return 'https://wa.me/'.($phone ?: '').'?text='.rawurlencode($message);
    }
}
