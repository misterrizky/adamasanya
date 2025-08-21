<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use Midtrans\Snap;
use App\Models\User;
use Midtrans\Config;
use Midtrans\Notification;
use App\Models\Transaction\Rent;
use App\Models\Transaction\Sale;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class MidtransService
{
    protected string $serverKey;
    protected bool $isProduction;
    protected bool $isSanitized;
    protected bool $is3ds;

    public function __construct()
    {
        $this->serverKey = env('MIDTRANS_SERVER_KEY');
        $this->isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        $this->isSanitized = env('MIDTRANS_SANITIZED', true);
        $this->is3ds = env('MIDTRANS_3DS', true);

        Config::$serverKey = $this->serverKey;
        Config::$isProduction = $this->isProduction;
        Config::$isSanitized = $this->isSanitized;
        Config::$is3ds = $this->is3ds;
    }

    public function createSnapToken($transaction, ?float $paidAmount = null, bool $isPelunasan = false): string
    {
        $grossAmount = $paidAmount ?? $transaction->total_amount;
        if (!$isPelunasan) {
            $minimumAmount = $transaction->total_amount * 0.5;
            if ($grossAmount < $minimumAmount || $grossAmount > $transaction->total_amount) {
                throw new \App\Exceptions\InvalidPaymentAmountException(
                    'Jumlah pembayaran tidak valid (min 50%, max total).'
                );
            }
        } else {
            // Untuk pelunasan, validasi hanya bahwa grossAmount <= total_amount
            if ($grossAmount <= 0 || $grossAmount > $transaction->total_amount) {
                throw new \App\Exceptions\InvalidPaymentAmountException(
                    'Jumlah pelunasan tidak valid.'
                );
            }
        }

        $params = [
            'transaction_details' => [
                'order_id' => $this->generateOrderId($transaction),
                'gross_amount' => (float) $grossAmount,
            ],
            'item_details' => $this->mapItemsToDetails($transaction, $grossAmount),
            'customer_details' => $this->getCustomerDetails($transaction),
            'callbacks' => [
                'finish' => route('consumer.transaction.view', ['code' => $transaction->code]),
                'error' => route('consumer.transaction.failed', ['code' => $transaction->code]),
                'pending' => route('consumer.transaction.view', ['code' => $transaction->code]),
            ],
        ];

        $itemSum = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $params['item_details']));
        if (abs($itemSum - $grossAmount) > 0.01) {
            Log::error('Item sum mismatch', ['sum' => $itemSum, 'gross' => $grossAmount]);
            throw new Exception('Jumlah item tidak sesuai dengan gross amount.');
        }

        try {
            Log::debug('Midtrans Snap Token Params', $params); // Tambahkan ini
            return Snap::getSnapToken($params);
        } catch (Exception $e) {
            Log::error('Failed to generate snap token', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString() // Tambahkan trace
            ]);
            throw new Exception('Gagal menghasilkan snap token: ' . $e->getMessage());
        }
    }
    public function createPelunasanToken(Rent $rent): string
    {
        $payment = $rent->payments[0];
        $paymentData = $payment->payment_data;
        $remaining = $paymentData['remaining_amount'];
        if ($remaining <= 0) {
            throw new Exception('Transaksi sudah lunas.');
        }
        $data = [
            'paid_amount' => $paymentData['paid_amount'] + $remaining, // Default full payment
            'remaining_amount' => 0,
            'deposit_amount' => $rent->deposit_amount,
            'service_fee' => $rent->total_amount * 0.8 / 100, // 0.8% service fee
            'ematerai_fee' => 10000,
        ];
        Payment::castAndCreate([
            'payable_type' => get_class($rent),
            'payable_id' => $rent->id,
            'user_id' => Auth::id(),
            'merchant_id' => env('MIDTRANS_MERCHANT_ID', 'default_merchant'),
            'order_id' => $this->generateOrderId($rent),
            'gross_amount' => $rent->total_amount,
            'currency' => 'IDR',
            'transaction_status' => 'pending',
            'transaction_time' => now(),
            'payment_data' => json_encode($data),
            'snap_token' => $this->createSnapToken($rent, $remaining, true)
        ]);
        return $this->createSnapToken($rent, $remaining, true);
    }
    public function createExtendToken($transaction, ?float $paidAmount = null, bool $isPelunasan = false): string
    {
        $grossAmount = $paidAmount ?? $transaction->total_amount;
        if (!$isPelunasan) {
            $minimumAmount = $transaction->total_amount * 0.5;
            if ($grossAmount < $minimumAmount || $grossAmount > $transaction->total_amount) {
                throw new \App\Exceptions\InvalidPaymentAmountException(
                    'Jumlah pembayaran tidak valid (min 50%, max total).'
                );
            }
        } else {
            // Untuk pelunasan, validasi hanya bahwa grossAmount <= total_amount
            if ($grossAmount <= 0 || $grossAmount > $transaction->total_amount) {
                throw new \App\Exceptions\InvalidPaymentAmountException(
                    'Jumlah pelunasan tidak valid.'
                );
            }
        }

        $params = [
            'transaction_details' => [
                'order_id' => $this->generateOrderId($transaction),
                'gross_amount' => (float) $grossAmount,
            ],
            'item_details' => $this->mapItemsToDetailsExtend($transaction, $grossAmount),
            'customer_details' => $this->getCustomerDetails($transaction),
            'callbacks' => [
                'finish' => route('consumer.transaction.view', ['code' => $transaction->code]),
                'error' => route('consumer.transaction.failed', ['code' => $transaction->code]),
                'pending' => route('consumer.transaction.view', ['code' => $transaction->code]),
            ],
        ];

        $itemSum = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $params['item_details']));
        if (abs($itemSum - $grossAmount) > 0.01) {
            Log::error('Item sum mismatch', ['sum' => $itemSum, 'gross' => $grossAmount]);
            throw new Exception('Jumlah item tidak sesuai dengan gross amount.');
        }

        try {
            Log::debug('Midtrans Snap Token Params', $params); // Tambahkan ini
            return Snap::getSnapToken($params);
        } catch (Exception $e) {
            Log::error('Failed to generate snap token', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString() // Tambahkan trace
            ]);
            throw new Exception('Gagal menghasilkan snap token: ' . $e->getMessage());
        }
    }

    public function handleNotification(array $notification): void
    {
        DB::beginTransaction();
        try {
            $orderId = $notification['order_id'] ?? null;
            if (!$orderId) {
                throw new Exception('Invalid notification: order_id not found');
            }

            $transaction = $this->getTransaction($orderId);
            if (!$transaction) {
                Log::error('Transaction not found for order_id: ' . $orderId);
                throw new Exception('Transaksi tidak ditemukan.');
            }

            if (!$this->isSignatureKeyVerified($notification)) {
                Log::error('Invalid signature key for order_id: ' . $orderId);
                throw new Exception('Signature key tidak valid.');
            }

            $status = $this->getStatus($notification);
            $payment = $this->getPayment($transaction, $orderId);
            if (!$payment) {
                Log::error('Payment not found for order_id: ' . $orderId);
                throw new Exception('Pembayaran tidak ditemukan.');
            }

            $payment->update([
                'transaction_status' => $status,
                'payment_type' => $notification['payment_type'] ?? null,
                'fraud_status' => $notification['fraud_status'] ?? 'accept',
                'status_code' => $notification['status_code'] ?? null,
                'status_message' => $notification['status_message'] ?? null,
                'transaction_midtrans_id' => $notification['transaction_id'] ?? null,
                'settlement_time' => isset($notification['settlement_time']) ? Carbon::parse($notification['settlement_time']) : null,
                'metadata' => $notification,
            ]);
            if ($status === 'settlement' && $transaction->status !== 'confirmed') {
                // Log::error('Get Payment Data', (array) $payment->payment_data);
                $paymentData = is_array($payment->payment_data) 
                    ? $payment->payment_data 
                    : json_decode($payment->payment_data, true);

                if (isset($paymentData['type']) && $paymentData['type'] === 'extension') {
                    $rent = Rent::find($paymentData['rent_id']);
                    if ($rent) {
                        $rent->applyExtension($paymentData['days']);
                    }
                } else {
                    $transaction->update(['status' => 'confirmed']);
                }

                $rizal = User::find(2);
                $total = $transaction->items->sum('subtotal');
                if ($transaction->branch_id != 1) {
                    $hasilCabang = $total * 0.3;
                    $hasilPusat = $total * 0.7;
                    $rizal->deposit($hasilPusat);
                    $cabang = User::role('Cabang')->where('branch_id', $transaction->branch_id)->first();
                    $cabang->deposit($hasilCabang);
                } else {
                    $hasilPusat = $total;
                    $rizal->deposit($hasilPusat);
                }

                $points = $total > 0 ? intval($total / 1000) : 0;
                $transaction->user->addPoints($points);
                activity()->causedBy($transaction->user)->log("Transaction {$transaction->code} confirmed via Midtrans.");
            }
            $transaction->updateStatusAfterPayment();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Midtrans notification handling failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    protected function mapItemsToDetails($transaction, $grossAmount): array
    {
        try {
            $itemDetails = [];
            $isPartial = $grossAmount < $transaction->total_amount;

            if ($isPartial) {
                // Untuk partial: Simple item agar sum match gross_amount
                $itemDetails[] = [
                    'id' => 'partial_payment_' . $transaction->id,
                    'price' => (float) $grossAmount,
                    'quantity' => 1,
                    'name' => 'Pembayaran Parsial untuk ' . ($transaction instanceof Rent ? 'Rental' : 'Pembelian') . ' #' . $transaction->code,
                ];
            } else {
                // Untuk full: Detail lengkap seperti sebelumnya
                $subtotal = $transaction->items->sum('subtotal');
                $serviceFee = $subtotal * 0.008;
                $stampDuty = $transaction->ematerai_fee ?? 10000;

                foreach ($transaction->items as $item) {
                    $itemDetails[] = [
                        'id' => $item->product_branch_id,
                        'price' => (float) $item->price,
                        'quantity' => $item->quantity * ($transaction instanceof Rent ? $item->duration_days : 1),
                        'name' => $item->productBranch->product->name,
                    ];
                }

                if ($transaction->deposit_amount > 0) {
                    $itemDetails[] = [
                        'id' => 'deposit',
                        'price' => (float) $transaction->deposit_amount,
                        'quantity' => 1,
                        'name' => 'Deposit',
                    ];
                }

                if ($transaction->promo_id) {
                    $couponService = app(CouponService::class);
                    $totals = $couponService->calculateDiscount($transaction, $transaction->promo);
                    $itemDetails[] = [
                        'id' => 'discount',
                        'price' => -(float) $totals['diskon'],
                        'quantity' => 1,
                        'name' => 'Diskon',
                    ];
                }

                $itemDetails[] = [
                    'id' => 'service_fee',
                    'price' => (float) $serviceFee,
                    'quantity' => 1,
                    'name' => 'Biaya Layanan',
                ];

                $itemDetails[] = [
                    'id' => 'stamp_duty',
                    'price' => (float) $stampDuty,
                    'quantity' => 1,
                    'name' => 'Biaya Materai',
                ];
            }

            return $itemDetails;
        } catch (Exception $e) {
            Log::error('Failed to map items', [
                'error' => $e->getMessage(),
                'transaction' => $transaction->id
            ]);
            // Fallback: Selalu match gross_amount
            return [[
                'id' => 'fallback',
                'price' => (float) $grossAmount,
                'quantity' => 1,
                'name' => 'Pembayaran untuk Transaksi #' . $transaction->code
            ]];
        }
    }
    protected function mapItemsToDetailsExtend($transaction, $grossAmount): array
    {
        try {
            $itemDetails = [];
            $isPartial = $grossAmount < $transaction->total_amount;

            if ($isPartial) {
                // Untuk partial: Simple item agar sum match gross_amount
                $itemDetails[] = [
                    'id' => 'partial_payment_' . $transaction->id,
                    'price' => (float) $grossAmount,
                    'quantity' => 1,
                    'name' => 'Pembayaran Parsial untuk ' . ($transaction instanceof Rent ? 'Rental' : 'Pembelian') . ' #' . $transaction->code,
                ];
            } else {
                // Untuk full: Detail lengkap seperti sebelumnya
                $subtotal = $transaction->items->sum('subtotal');
                $serviceFee = $subtotal * 0.008;

                foreach ($transaction->items as $item) {
                    $itemDetails[] = [
                        'id' => $item->product_branch_id,
                        'price' => (float) $item->price,
                        'quantity' => $item->quantity * ($transaction instanceof Rent ? $item->duration_days : 1),
                        'name' => $item->productBranch->product->name,
                    ];
                }

                $itemDetails[] = [
                    'id' => 'service_fee',
                    'price' => (float) $serviceFee,
                    'quantity' => 1,
                    'name' => 'Biaya Layanan',
                ];
            }

            return $itemDetails;
        } catch (Exception $e) {
            Log::error('Failed to map items', [
                'error' => $e->getMessage(),
                'transaction' => $transaction->id
            ]);
            // Fallback: Selalu match gross_amount
            return [[
                'id' => 'fallback',
                'price' => (float) $grossAmount,
                'quantity' => 1,
                'name' => 'Pembayaran untuk Transaksi #' . $transaction->code
            ]];
        }
    }

    protected function getPayment($transaction, string $orderId)
    {
        return Payment::where('payable_type', get_class($transaction))
            ->where('payable_id', $transaction->id)
            ->where('order_id', $orderId)
            ->latest() // Get the most recent payment if there are multiple
            ->first();
    }

    protected function isSignatureKeyVerified(array $notification): bool
    {
        $orderId = $notification['order_id'] ?? '';
        $statusCode = $notification['status_code'] ?? '';
        $grossAmount = $notification['gross_amount'] ?? '';
        $signatureKey = $notification['signature_key'] ?? '';

        $localSignatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);
        return hash_equals($localSignatureKey, $signatureKey);
    }

    protected function getTransaction(string $orderId)
    {
        return Rent::whereHas('payments', fn($q) => $q->where('order_id', $orderId))
            ->first() ?? Sale::whereHas('payments', fn($q) => $q->where('order_id', $orderId))
            ->first();
    }

    protected function getStatus(array $notification): string
    {
        $transactionStatus = $notification['transaction_status'] ?? '';
        $fraudStatus = $notification['fraud_status'] ?? 'accept';

        return match ($transactionStatus) {
            'capture' => $fraudStatus === 'accept' ? 'settlement' : 'pending',
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

    protected function getCustomerDetails($transaction): array
    {
        $address = $transaction->user->userAddress;
        return [
            'first_name' => $transaction->user->name,
            'email' => $transaction->user->email,
            'phone' => $transaction->user->showFormattedPhoneNumber($transaction->user->phone),
            'billing_address' => [
                'address' => $address->address ?? 'N/A',
                'city' => $address->city->name ?? 'N/A',
                'postal_code' => $address->village->poscode ?? '00000',
                'country_code' => 'IDN',
            ],
        ];
    }
}