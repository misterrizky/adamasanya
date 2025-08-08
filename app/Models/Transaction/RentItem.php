<?php

namespace App\Models\Transaction;

use WendellAdriel\Lift\Lift;
use App\Models\ProductBranch;
use Illuminate\Database\Eloquent\Model;
use WendellAdriel\Lift\Attributes\Relations\HasMany;
use WendellAdriel\Lift\Attributes\Relations\BelongsTo;

#[BelongsTo(Rent::class)]
#[BelongsTo(ProductBranch::class)]
class RentItem extends Model
{
    use Lift;
}
