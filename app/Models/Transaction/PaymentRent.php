<?php

namespace App\Models\Transaction;

use WendellAdriel\Lift\Lift;
use Illuminate\Database\Eloquent\Model;

class PaymentRent extends Model
{
    use Lift;
    protected $fillable = [
        'rent_id',
        'user_id',
        'midtrans_order_id',
        'gross_amount',
        'transaction_status',
    ];
}
