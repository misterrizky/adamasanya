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
#[HasMany(SaleItem::class)]
class Sale extends Model
{
    use Lift;

    public function promos()
    {
        return $this->belongsToMany(Promo::class, 'promo_sales');
    }
}
