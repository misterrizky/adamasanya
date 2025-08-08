<?php

namespace App\Models\Transaction;

use App\Models\User;
use WendellAdriel\Lift\Lift;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Cart extends Model
{
    use Lift;
    protected $fillable = [
        'user_id',
        'session_id',
        'productable_id',
        'productable_type',
        'quantity',
        'price',
        'type',
        'start_date',
        'end_date',
        'days',
    ];

    public function productable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
