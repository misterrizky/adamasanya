<?php

namespace App\Models\Transaction;

use App\Models\User;
use App\Models\Promo;
use App\Models\Rating;
use WendellAdriel\Lift\Lift;
use App\Models\Master\Branch;
use App\Models\Transaction\PaymentRent;
use Illuminate\Database\Eloquent\Model;
use Creagia\LaravelSignPad\Contracts\CanBeSigned;
use WendellAdriel\Lift\Attributes\Relations\HasOne;
use WendellAdriel\Lift\Attributes\Relations\HasMany;
use Creagia\LaravelSignPad\Concerns\RequiresSignature;
use WendellAdriel\Lift\Attributes\Relations\BelongsTo;

#[BelongsTo(Branch::class)]
#[BelongsTo(User::class)]
#[HasOne(Rating::class)]
#[HasMany(RentItem::class)]
class Rent extends Model implements CanBeSigned
{
    use Lift, RequiresSignature;
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i'
        ];
    }
    public function getStatusAttribute(){
        $status = $this->attributes['status'] ?? null; // Get the raw status value
        
        if ($status == "confirmed") {
            return [
                'class' => 'badge-light-success',
                'text' => 'Pembayaran Diterima'
            ];
        } elseif ($status == "on_rent") {
            if($this->isDue()){
                return [
                    'class' => 'badge-light-warning',
                    'text' => 'Barang Belum Kembali'
                ];
            }else{
                return [
                    'class' => 'badge-light-success',
                    'text' => 'Sedang Berjalan'
                ];
            }
        } elseif ($status == "returned") {
            return [
                'class' => 'badge-light-success',
                'text' => 'Selesai'
            ];
        } elseif ($status == "canceled") {
            return [
                'class' => 'badge-light-dark',
                'text' => 'Dibatalkan'
            ];
        }

        return [
            'class' => 'badge-light-primary',
            'text' => 'Menunggu Konfirmasi'
        ];
    }
    public function getStatusPaymentAttribute(){
        if ($this->status_paid == "pending") {
            return [
                'class' => 'badge-light-danger',
                'text' => 'Belum Dibayar'
            ];
        } elseif ($this->status_paid == "completed") {
            return [
                'class' => 'badge-light-success',
                'text' => 'Sudah Dibayar'
            ];
        }

        return [
            'class' => 'badge-light-primary',
            'text' => 'Pending'
        ];
    }
    public function scopeDueTransactions($query, $status = 'on_rent', $daysBefore = 1)
    {
        return $query->where('status', $status)
            ->where(function($q) use ($daysBefore) {
                // Transaksi yang sudah lewat jatuh tempo
                $q->where('end_date', '<', now()->toDateString())
                  // Atau transaksi yang akan jatuh tempo dalam X hari
                  ->orWhereBetween('end_date', [
                      now()->toDateString(),
                      now()->addDays($daysBefore)->toDateString()
                  ]);
            });
    }
    public function isDue()
    {
        return $this->end_date < now()->toDateString();
    }
    public function isDueInDays($daysBefore = 1)
    {
        $dueDate = now()->addDays($daysBefore)->toDateString();
        return $this->end_date >= now()->toDateString() && 
               $this->end_date <= $dueDate;
    }
    public function items()
    {
        return $this->hasMany(RentItem::class);
    }
    public function payment()
    {
        return $this->hasOne(Payment::class, 'transaction_id', 'id');
    }
    public function calculateTotalPrice()
    {
        $subtotal = $this->items->sum(fn($item) => $item->price * $item->quantity);
        if ($this->promo) {
            if ($this->promo->type === 'percentage') {
                return $subtotal * (1 - $this->promo->value / 100);
            } elseif ($this->promo->type === 'fixed_amount') {
                return max(0, $subtotal - $this->promo->value);
            }
            // Add logic for buy_x_get_y and free_shipping
        }
        return $subtotal;
    }
}
