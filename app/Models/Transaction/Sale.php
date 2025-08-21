<?php

namespace App\Models\Transaction;

use App\Models\User;
use App\Models\Promo;
use WendellAdriel\Lift\Lift;
use App\Models\Master\Branch;
use Illuminate\Database\Eloquent\Model;
use WendellAdriel\Lift\Attributes\Relations\HasMany;
use WendellAdriel\Lift\Attributes\Relations\BelongsTo;

#[BelongsTo(Branch::class)]
#[BelongsTo(User::class)]
class Sale extends Model
{
    use Lift;

    public function promos()
    {
        return $this->morphToMany(Promo::class, 'applicable', 'promotion_usages');
    }

    // Tambah calculateTotalPrice mirip Rent, adjust untuk Sale (no days)
    public function calculateTotalPrice()
    {
        $subtotal = $this->items->sum(fn($item) => $item->price * $item->quantity);
        $diskon = 0;

        if ($promo = $this->promos->first()) {
            if ($promo->type === 'percentage') {
                $diskon = $subtotal * ($promo->value / 100);
            } elseif ($promo->type === 'fixed_amount') {
                $diskon = $promo->value;
            } // ... add other types
        }

        $this->discount_amount = $diskon;
        return $subtotal - $diskon;
    }
    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }
    public function items()
    {
        return $this->hasMany(RentItem::class);
    }
}
