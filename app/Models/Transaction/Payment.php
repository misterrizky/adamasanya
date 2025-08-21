<?php

namespace App\Models\Transaction;

use WendellAdriel\Lift\Lift;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    use Lift;

    protected $fillable = [
        'payable_type', 'payable_id', 'user_id', 'merchant_id', 'order_id',
        'transaction_midtrans_id', 'gross_amount', 'currency', 'payment_type',
        'transaction_status', 'fraud_status', 'status_message', 'status_code',
        'signature_key', 'transaction_time', 'expiry_time', 'metadata',
        'va_numbers', 'transaction_type', 'settlement_time', 'issuer',
        'acquirer', 'merchant_cross_reference_id', 'bank', 'masked_card',
        'card_type', 'three_ds_version', 'eci', 'channel_response_code',
        'channel_response_message', 'approval_code', 'reference_id',
        'payment_code', 'store', 'payment_data', 'snap_token'
    ];

    protected $casts = [
        'gross_amount' => 'decimal:0',
        'transaction_time' => 'datetime',
        'expiry_time' => 'datetime',
        'settlement_time' => 'datetime',
        'payment_data' => 'array',
        'metadata' => 'array',
        'va_numbers' => 'array',
    ];

    public function transaction(): MorphTo
    {
        return $this->morphTo('transaction', 'payable_type', 'payable_id');
    }
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function isSuccessful(): bool
    {
        return in_array($this->transaction_status, ['settlement', 'capture']);
    }

    public function isPending(): bool
    {
        return $this->transaction_status === 'pending';
    }

    public function hasExpired(): bool
    {
        return $this->expiry_time && $this->expiry_time->isPast();
    }
}