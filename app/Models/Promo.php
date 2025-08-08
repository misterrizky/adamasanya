<?php

namespace App\Models;

use WendellAdriel\Lift\Lift;
use App\Models\Master\Branch;
use App\Models\Master\Category;
use App\Models\Transaction\Rent;
use App\Models\Transaction\Sale;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use WendellAdriel\Lift\Attributes\Relations\HasMany;

#[HasMany(PromotionUsage::class)]
class Promo extends Model
{
    use Lift, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'slug',
        'description',
        'thumbnail',
        'type',
        'value',
        'buy_quantity',
        'get_quantity',
        'free_product_id',
        'min_order_amount',
        'max_uses',
        'max_uses_per_user',
        'start_date',
        'end_date',
        'is_active',
        'scope',
        'day_restriction',
        'applicable_for',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'type' => 'string',
        'scope' => 'string',
        'day_restriction' => 'string',
        'applicable_for' => 'string',
    ];

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'promo_branches', 'promo_id', 'branch_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'promo_categories', 'promo_id', 'category_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'promo_products', 'promo_id', 'product_id');
    }

    public function freeProduct()
    {
        return $this->belongsTo(Product::class, 'free_product_id');
    }
}
