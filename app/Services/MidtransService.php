<?php

namespace App\Services;

use App\Models\Transaction\Rent;
use App\Models\Transaction\Sale;
use App\Models\Transaction\Payment;
use Exception;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;

class MidtransService
{
    protected string $serverKey;
    protected bool $isProduction;
    protected bool $isSanitized;
    protected bool $is3ds;

    public function __construct()
    {
        $this->serverKey = env('MIDTRANS_SERVER_KEY');
        $this->isProduction = env('MIDTRANS_IS_PRODUCTION');
        $this->isSanitized = env('MIDTRANS_SANITIZED');
        $this->is3ds = env('MIDTRANS_3DS', true);

        Config::$serverKey = $this->serverKey;
        Config::$isProduction = $this->isProduction;
        Config::$isSanitized = $this->isSanitized;
        Config::$is3ds = $this->is3ds;
    }

    public function createSnapToken($transaction): string
    {
        $params = [
            'transaction_details' => [
                'order_id' => $this->generateOrderId($transaction),
                'gross_amount' => (int) $transaction->total_price,
            ],
            'item_details' => $this->mapItemsToDetails($transaction),
            'customer_details' => $this->getCustomerDetails($transaction),
            'callbacks' => [
                'finish' => route('consumer.transaction.view', ['code' => $transaction->code]),
                'error' => route('consumer.transaction.failed', ['code' => $transaction->code]),
                'pending' => route('consumer.transaction.view', ['code' => $transaction->code])
            ]
        ];

        try {
            return Snap::getSnapToken($params);
        } catch (Exception $e) {
            throw new Exception('Failed to generate snap token: ' . $e->getMessage());
        }
    }

    public function handleNotification()
    {
        try {
            $notification = new Notification();
            
            if (!$this->isSignatureKeyVerified($notification)) {
                throw new Exception('Invalid signature key');
            }

            $transaction = $this->getTransaction($notification->order_id);
            if (!$transaction) {
                throw new Exception('Transaction not found');
            }

            $payment = $this->getPayment($transaction, $notification->order_id);
            if (!$payment) {
                throw new Exception('Payment record not found');
            }

            $status = $this->getStatus($notification);
            
            $payment->update([
                'transaction_status' => $status,
                'transaction_time' => $notification->transaction_time ?? now(),
                'payment_type' => $notification->payment_type,
                'fraud_status' => $notification->fraud_status ?? null,
            ]);

            if ($status === 'settlement' || $status === 'capture') {
                $transaction->update(['status_paid' => 'paid']);
            } elseif ($status === 'expire' || $status === 'cancel' || $status === 'deny') {
                $transaction->update(['status_paid' => 'failed']);
            }

            return [
                'success' => true,
                'transaction' => $transaction,
                'payment' => $payment
            ];
        } catch (Exception $e) {
            \Log::error('Midtrans notification error: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function isSignatureKeyVerified(Notification $notification): bool
    {
        $localSignatureKey = hash('sha512',
            $notification->order_id . 
            $notification->status_code . 
            $notification->gross_amount . 
            $this->serverKey
        );

        return $localSignatureKey === $notification->signature_key;
    }

    protected function getTransaction(string $orderId)
    {
        $rent = Rent::whereHas('payment', fn($q) => $q->where('midtrans_order_id', $orderId))->first();
        if ($rent) {
            return $rent;
        }

        return Sale::whereHas('payment', fn($q) => $q->where('midtrans_order_id', $orderId))->first();
    }

    protected function getPayment($transaction, string $orderId)
    {
        if ($transaction instanceof Rent) {
            return Payment::where('rent_id', $transaction->id)
                ->where('midtrans_order_id', $orderId)
                ->first();
        }

        return Payment::where('sale_id', $transaction->id)
            ->where('midtrans_order_id', $orderId)
            ->first();
    }

    protected function getStatus(Notification $notification): string
    {
        $transactionStatus = $notification->transaction_status;
        $fraudStatus = $notification->fraud_status;

        return match ($transactionStatus) {
            'capture' => ($fraudStatus == 'accept') ? 'settlement' : 'pending',
            'settlement' => 'settlement',
            'deny' => 'deny',
            'cancel' => 'cancel',
            'expire' => 'expire',
            'pending' => 'pending',
            default => 'unknown',
        };
    }

    public function generateOrderId($transaction): string
    {
        $prefix = $transaction instanceof Rent ? 'RENT' : 'SALE';
        return "{$prefix}-{$transaction->id}-" . now()->timestamp;
    }

    protected function mapItemsToDetails($transaction): array
    {
        if ($transaction instanceof Rent) {
            return $transaction->rentItems->map(function ($item) {
                return [
                    'id' => $item->productBranch->id,
                    'price' => (int) $item->productBranch->rent_price,
                    'quantity' => $item->qty,
                    'name' => $item->productBranch->product->name,
                ];
            })->toArray();
        }

        return $transaction->saleItems->map(function ($item) {
            return [
                'id' => $item->productBranch->id,
                'price' => (int) $item->productBranch->sale_price,
                'quantity' => $item->qty,
                'name' => $item->productBranch->product->name,
            ];
        })->toArray();
    }

    protected function getCustomerDetails($transaction): array
    {
        return [
            'first_name' => $transaction->user->name,
            'email' => $transaction->user->email,
            'phone' => $transaction->user->phone,
            'billing_address' => [
                'address' => $transaction->user->userAddress->address ?? '',
                'city' => $transaction->user->userAddress->city->name ?? '',
                'postal_code' => $transaction->user->userAddress->village->poscode ?? '',
                'country_code' => 'IDN'
            ]
        ];
    }
}