<?php

namespace App\Models\Transaction;

use WendellAdriel\Lift\Lift;
use App\Models\ProductBranch;
use App\Models\Transaction\Rent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use WendellAdriel\Lift\Attributes\Relations\BelongsTo;

#[BelongsTo(Rent::class)]
#[BelongsTo(ProductBranch::class)]
class RentItem extends Model
{
    use Lift, SoftDeletes;

    protected $fillable = [
        'rent_id', 'product_branch_id', 'quantity', 'price', 'duration_days',
        'subtotal', 'notes', 'damage_report', 'damage_fee'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:0',
        'duration_days' => 'integer',
        'subtotal' => 'decimal:0',
        'damage_fee' => 'decimal:0'
    ];

    public function calculateSubtotal(): float
    {
        $subtotal = $this->price * $this->quantity * $this->duration_days;
        $this->subtotal = $subtotal;
        $this->save();
        return $subtotal;
    }
}