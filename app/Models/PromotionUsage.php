<?php

namespace App\Models;

use WendellAdriel\Lift\Lift;
use Illuminate\Database\Eloquent\Model;

class PromotionUsage extends Model
{
    use Lift;
    protected $fillable = [
        'user_id', 'promo_id', 'applicable_type', 'applicable_id', 'discount_amount'
    ];
    public function usages()
    {
        return $this->hasMany(PromotionUsage::class);
    }
}
