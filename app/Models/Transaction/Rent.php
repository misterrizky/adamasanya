<?php

namespace App\Models\Transaction;

use App\Models\User;
use App\Models\Promo;
use App\Models\Rating;
use WendellAdriel\Lift\Lift;
use App\Models\Master\Branch;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\LogOptions;
use App\Models\Transaction\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Creagia\LaravelSignPad\Contracts\CanBeSigned;
use WendellAdriel\Lift\Attributes\Relations\HasOne;
use WendellAdriel\Lift\Attributes\Relations\HasMany;
use Creagia\LaravelSignPad\Concerns\RequiresSignature;
use WendellAdriel\Lift\Attributes\Relations\BelongsTo;
use WendellAdriel\Lift\Attributes\Relations\MorphMany;

#[BelongsTo(User::class)]
#[BelongsTo(Promo::class)]
#[BelongsTo(Branch::class)]
#[HasMany(RentItem::class)]
class Rent extends Model implements CanBeSigned
{
    use Lift, SoftDeletes, RequiresSignature;

    protected $fillable = [
        'user_id', 'branch_id', 'promo_id', 'code', 'status', 'start_date',
        'end_date', 'pickup_time', 'return_time', 'total_amount',
        'deposit_amount', 'ematerai_fee', 'notes',
        'pickup_signature', 'pickup_ematerai_id', 'return_signature',
        'return_ematerai_id'
    ];

    protected $casts = [
        'status' => 'string',
        'start_date' => 'date',
        'end_date' => 'date',
        'pickup_time' => 'datetime:H:i',
        'return_time' => 'datetime:H:i',
        'total_amount' => 'decimal:0',
        'deposit_amount' => 'decimal:0',
        'ematerai_fee' => 'decimal:0'
    ];

    // Relasi ke Promo via promotion_usages (polymorphic)
    public function promos()
    {
        return $this->morphToMany(Promo::class, 'applicable', 'promotion_usages');
    }

    public function scopeDueTransactions($query, string $status = 'active', int $daysBefore = 1)
    {
        return $query->where('status', $status)
            ->where(function ($q) use ($daysBefore) {
                $q->where('end_date', '<', now()->toDateString())
                    ->orWhereBetween('end_date', [
                        now()->toDateString(),
                        now()->addDays($daysBefore)->toDateString()
                    ]);
            });
    }

    public function isDue(): bool
    {
        return $this->end_date->isBefore(now());
    }

    public function isDueInDays(int $daysBefore = 1): bool
    {
        $dueDate = now()->addDays($daysBefore);
        return $this->end_date->isBetween(now(), $dueDate);
    }

    public function items()
    {
        return $this->hasMany(RentItem::class);
    }
    public function rating()
    {
        return $this->morphOne(Rating::class, 'rateable');
    }
    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function calculateTotalPrice(): float
    {
        $subtotal = $this->items->sum(fn($item) => $item->subtotal);
        $serviceFee = $subtotal * 0.008; // 0.8% service fee
        $discount = 0;

        if ($promo = $this->promo) {
            $discount = match ($promo->type) {
                'fixed_amount' => $this->calculateFixedAmountDiscount($promo),
                'percentage' => $subtotal * ($promo->value / 100),
                'buy_x_get_y' => $this->calculateBuyXGetYDiscount($promo),
                'free_shipping' => 0,
                default => 0,
            };
        }

        $total = $subtotal - $discount + $serviceFee + $this->ematerai_fee + $this->deposit_amount;
        $this->total_amount = round($total);
        $this->save();

        return $total;
    }

    protected function calculateFixedAmountDiscount(Promo $promo): float
    {
        $multiplier = floor($this->items->sum('duration_days') / $promo->min_order_amount);
        $freeDays = $multiplier * $promo->value;
        $discount = $this->items->sum(fn($item) => $freeDays * $item->price * $item->quantity);
        $this->items()->update(['duration_days' => DB::raw("duration_days + $freeDays")]);
        $this->end_date = $this->end_date->addDays($freeDays);
        $this->save();
        return $discount;
    }

    protected function calculateBuyXGetYDiscount(Promo $promo): float
    {
        return 0;
    }

    public function calculateExtensionFee(int $days): float
    {
        // Validasi hari perpanjangan
        if ($days <= 0) {
            throw new \Exception('Hari perpanjangan harus lebih dari 0');
        }

        // Validasi waktu: perpanjangan hanya jika sewa BELUM berakhir
        if (now() >= $this->end_date) {
            throw new \Exception('Perpanjangan hanya dapat dilakukan sebelum sewa berakhir');
        }

        // Hitung biaya perpanjangan
        $extensionFee = $this->items->sum(function ($item) use ($days) {
            return $item->price * $item->quantity * $days;
        });

        $serviceFee = $extensionFee * 0.008; // Biaya layanan 0.8%
        $totalFee = $extensionFee + $serviceFee;

        return $totalFee;
    }

    public function applyExtension(int $days): void
    {
        // Asumsi: validasi sudah dilakukan sebelumnya
        $extensionFee = $this->items->sum(function ($item) use ($days) {
            return $item->price * $item->quantity * $days;
        });

        $serviceFee = $extensionFee * 0.008; // Biaya layanan 0.8%
        $totalFee = $extensionFee + $serviceFee;

        // Update data
        $this->end_date = $this->end_date->addDays($days);
        $this->total_amount += $totalFee;
        $this->save();
    }

    public function dailyRate(): float
    {
        return $this->items->sum(fn($item) => $item->price * $item->quantity);
    }
    
    public function getPaidAmountAttribute(): float
    {
        return $this->payments()
            ->whereIn('transaction_status', ['settlement', 'capture'])
            ->sum('gross_amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->total_amount - $this->paid_amount);
    }

    public function isFullyPaid(): bool
    {
        // return $this->payments->last()->payment_data['remaining_amount'] <= 0;
        return $this->payments->last()->payment_data['remaining_amount'] <= 0 || $this->payments->last()->transaction_status == "pending";
    }

    public function updateStatusAfterPayment()
    {
        if ($this->isFullyPaid() && $this->status === 'pending') {
            $this->update(['status' => 'confirmed']);
            // $this->user->notify(new \NotificationChannels\WebPush\WebPushMessage(
            //     'Pelunasan sukses! Transaksi #' . $this->code . ' siap diproses.'
            // ));
            activity()->performedOn($this)->log(
                'Pelunasan selesai oleh user ' . $this->user->name
            );
        }
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->code = 'RENT-' . now()->format('Ymd') . '-' . str_pad((self::max('id') ?? 0) + 1, 3, '0', STR_PAD_LEFT);
        });
    }
}