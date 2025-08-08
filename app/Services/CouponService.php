<?php

namespace App\Services;

use App\Models\Promo;
use App\Models\PromotionUsage;
use App\Models\Transaction\Rent;
use App\Models\Transaction\Sale;
use App\Models\Transaction\PaymentRent;
use App\Models\Transaction\PaymentSale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CouponService
{
    public function validateCoupon(Promo $coupon, $transaction): array
    {
        $errors = [];
        // Check coupon expiration
        if ($coupon->end_at && now()->gt($coupon->end_at)) {
            $errors[] = 'Kupon sudah kadaluarsa.';
        }

        // Check minimum rent days (for Rent transactions only)
        if ($transaction instanceof Rent && $coupon->min_rent && $transaction->getOriginal('total_days') < $coupon->min_rent) {
            $errors[] = 'Jumlah hari sewa tidak memenuhi syarat kupon.';
        }

        // Check branch compatibility
        if ($coupon->branch_id && $coupon->branch_id !== $transaction->branch_id) {
            $errors[] = 'Kupon tidak berlaku untuk cabang ini.';
        }

        // Check maximum usage
        if ($coupon->max_usage && $coupon->promotionUsages->count() >= $coupon->max_usage) {
            $errors[] = 'Kupon telah mencapai batas penggunaan.';
        }

        // Check if transaction already has a settled payment
        $payment = $transaction instanceof Rent ? $transaction->paymentRent : $transaction->paymentSale;
        if ($payment && in_array($payment->transaction_status, ['settlement', 'capture'])) {
            $errors[] = 'Kupon tidak dapat diterapkan pada transaksi yang sudah memiliki pembayaran.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    public function calculateDiscount($transaction, ?Promo $coupon): array
    {
        $subtotal = $transaction instanceof Rent
            ? $transaction->rentItems->sum('subtotal')
            : $transaction->saleItems->sum('subtotal');
        $deposit = $transaction->deposit_amount ?? 0;
        $biaya_layanan = 4000;
        $diskon = 0;
        $total_days = $transaction instanceof Rent ? $transaction->getOriginal('total_days') : 1;

        if ($coupon) {
            if ($coupon->type === 'fixed_amount' && $transaction instanceof Rent) { // Free days for Rent only
                $kelipatan = floor($total_days / $coupon->min_rent);
                $hariGratis = $kelipatan * $coupon->value;
                $diskon = $transaction->rentItems->sum(fn($item) => $hariGratis * $item->productBranch->rent_price);
                $total_days += $hariGratis;
            } elseif ($coupon->type === 'percentage') { // Percentage discount for both Rent and Sale
                $diskon = $subtotal * ($coupon->value / 100);
            }
        }

        $grandtotal = $subtotal + $biaya_layanan - $diskon + $deposit;

        return compact('subtotal', 'diskon', 'grandtotal', 'total_days');
    }

    public function applyCoupon($code, string $couponCode): array
    {
        $transaction = Rent::with(['user', 'rentItems.productBranch.product', 'branch', 'paymentRent'])
            ->where('code', $code)
            ->first() 
            ?? Sale::with(['user', 'saleItems.productBranch.product', 'branch', 'paymentSale'])
                ->where('code', $code)
                ->firstOrFail();
        
        $coupon = Promo::where('code', $couponCode)->firstOrFail();
        $validation = $this->validateCoupon($coupon, $transaction);

        if (!$validation['valid']) {
            throw new \Exception(implode(' ', $validation['errors']));
        }

        DB::beginTransaction();
        try {
            $totals = $this->calculateDiscount($transaction, $coupon);

            $updateData = [
                'discount_amount' => $totals['diskon'],
                'total_price' => $totals['grandtotal'],
            ];

            if ($transaction instanceof Rent) {
                $updateData['total_days'] = $totals['total_days'];
                $updateData['end_date'] = $coupon->type === 'fixed_amount'
                    ? Carbon::parse($transaction->getOriginal('end_date'))->addDays(floor($transaction->getOriginal('total_days') / $coupon->min_rent) * $coupon->value)
                    : $transaction->end_date;
            }

            $transaction->castAndUpdate($updateData);
            $cekUsage = PromotionUsage::where('promo_id', $coupon->id)
                ->where('user_id', $transaction->user_id)
                ->first();
            if ($cekUsage) {
                $cekUsage->castAndUpdate([
                    'discount_amount' => $totals['diskon'],
                ]);
            }else{
                PromotionUsage::castAndCreate([
                    'promo_id' => $coupon->id,
                    'user_id' => $transaction->user_id,
                    'applicable_type' => get_class($transaction),
                    'applicable_id' => $transaction->id,
                    'discount_amount' => $totals['diskon'],
                ]);
            }
            // $coupon->increment('usage_count');

            // Generate new Midtrans Snap Token
            $midtransService = app(MidtransService::class);
            $snapToken = $midtransService->createSnapToken($transaction);

            // Update or create payment record
            $paymentModel = $transaction instanceof Rent ? PaymentRent::class : PaymentSale::class;
            $payment = $paymentModel::where([
                ($transaction instanceof Rent ? 'rent_id' : 'sale_id') => $transaction->id,
                'user_id' => $transaction->user_id
            ])->first();
            $payment->castAndUpdate(
                [
                    'midtrans_order_id' => $midtransService->generateOrderId($transaction),
                    'gross_amount' => $totals['grandtotal'],
                    'transaction_status' => 'pending',
                    'snap_token' => $snapToken // Store snap_token in payment
                ]
            );

            DB::commit();

            $message = $coupon->type === 'fixed_amount' && $transaction instanceof Rent
                ? "Kupon berhasil diterapkan! Anda mendapatkan " . ($totals['total_days'] - $transaction->getOriginal('total_days')) . " hari gratis."
                : "Kupon berhasil diterapkan! Anda mendapatkan diskon {$coupon->value}%.";

            return [
                'success' => true,
                'message' => $message,
                'totals' => $totals,
                'snap_token' => $snapToken,
                'payment' => $payment
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to apply coupon', [
                'coupon_code' => $couponCode,
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function resetCoupon($transaction): array
    {
        $payment = $transaction instanceof Rent ? $transaction->paymentRent : $transaction->paymentSale;
        if ($payment && in_array($payment->transaction_status, ['settlement', 'capture'])) {
            throw new \Exception('Tidak dapat mereset kupon pada transaksi yang sudah memiliki pembayaran.');
        }

        DB::beginTransaction();
        try {
            $totals = $this->calculateDiscount($transaction, null);

            $updateData = [
                'discount_amount' => 0,
                'total_price' => $totals['grandtotal'],
            ];

            if ($transaction instanceof Rent) {
                $updateData['total_days'] = $transaction->getOriginal('total_days');
                $updateData['end_date'] = $transaction->getOriginal('end_date');
            }

            $transaction->castAndUpdate($updateData);
            
            if ($transaction->discount_amount > 0) {
                PromotionUsage::where('applicable_id', $transaction->id)
                ->where('applicable_type', get_class($transaction))
                ->where('user_id', $transaction->user_id)
                ->delete();
            }

            // Generate new Midtrans Snap Token
            $midtransService = app(MidtransService::class);
            $snapToken = $midtransService->createSnapToken($transaction);

            // Update or create payment record
            $paymentModel = $transaction instanceof Rent ? PaymentRent::class : PaymentSale::class;
            $payment = $paymentModel::updateOrCreate(
                [
                    ($transaction instanceof Rent ? 'rent_id' : 'sale_id') => $transaction->id,
                    'user_id' => $transaction->user_id
                ],
                [
                    'midtrans_order_id' => $midtransService->generateOrderId($transaction),
                    'gross_amount' => $totals['grandtotal'],
                    'transaction_status' => 'pending',
                    'snap_token' => $snapToken
                ]
            );

            DB::commit();
            return [
                'success' => true,
                'totals' => $totals,
                'snap_token' => $snapToken,
                'payment' => $payment
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reset coupon', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}