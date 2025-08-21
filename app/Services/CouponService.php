<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Promo;
use App\Models\PromotionUsage;
use App\Models\Transaction\Rent;
use App\Models\Transaction\Sale;
use App\Services\MidtransService;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction\Payment;
use Illuminate\Support\Facades\Log;

class CouponService
{
    public function validateCoupon(Promo $coupon, $transaction): array
    {
        $errors = [];

        if (!$coupon->is_active) {
            $errors[] = 'Kupon tidak aktif.';
        }

        if ($coupon->end_date && now()->gt($coupon->end_date)) {
            $errors[] = 'Kupon sudah kadaluarsa.';
        }

        if ($coupon->start_date && now()->lt($coupon->start_date)) {
            $errors[] = 'Kupon belum aktif.';
        }

        // if ($transaction instanceof Rent && $coupon->min_order_amount) {
        //     $totalDays = $transaction->items->sum('duration_days');
        //     if ($totalDays < $coupon->min_order_amount) {
        //         $errors[] = 'Jumlah hari sewa tidak memenuhi syarat kupon.';
        //     }
        // }

        if ($coupon->branches()->count() && !$coupon->branches()->where('branch_id', $transaction->branch_id)->exists()) {
            $errors[] = 'Kupon tidak berlaku untuk cabang ini.';
        }

        if ($coupon->max_uses && $coupon->promotionUsages()->count() >= $coupon->max_uses) {
            $errors[] = 'Kupon telah mencapai batas penggunaan.';
        }

        if ($coupon->max_uses_per_user && $coupon->promotionUsages()->where('user_id', $transaction->user_id)->count() >= $coupon->max_uses_per_user) {
            $errors[] = 'Anda telah mencapai batas penggunaan kupon ini.';
        }
        if ($coupon->day_restriction) {
            $startDate = Carbon::parse($transaction->start_date);
            $dayOfWeek = $startDate->dayOfWeek;

            // Definisikan weekend (Jumat, Sabtu, Minggu) dan weekday (Senin-Kamis)
            $isWeekend = in_array($dayOfWeek, [Carbon::FRIDAY, Carbon::SATURDAY, Carbon::SUNDAY]);
            $isWeekday = !$isWeekend;

            // Cek restriksi hari
            $invalidDay = false;
            $allowedDays = '';

            if ($coupon->day_restriction === 'weekend') {
                // Kupon TIDAK BOLEH dipakai di weekend
                $invalidDay = $isWeekday;
                $allowedDays = 'Jumat, Sabtu, Minggu';
            } elseif ($coupon->day_restriction === 'weekday') {
                $invalidDay = $isWeekend;
                // Kupon TIDAK BOLEH dipakai di weekday
                $allowedDays = 'Senin - Kamis';
            }

            // Jika hari transaksi termasuk yang dilarang
            if ($invalidDay) {
                $errors[] = 'Kupon tidak dapat digunakan pada hari ini. Hanya berlaku pada: ' . $allowedDays;
            }
        }

        if ($coupon->applicable_for && $coupon->applicable_for !== 'both') {
            $transactionType = $transaction instanceof Rent ? 'rent' : 'sale';
            if ($coupon->applicable_for !== $transactionType) {
                $errors[] = 'Kupon hanya berlaku untuk ' . ($coupon->applicable_for === 'rent' ? 'penyewaan' : 'pembelian');
            }
        }

        $payment = $transaction->payment;
        if ($payment && $payment->isSuccessful()) {
            $errors[] = 'Kupon tidak dapat diterapkan pada transaksi yang sudah dibayar.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    public function calculateDiscount($transaction, ?Promo $coupon = null): array
    {
        $subtotal = $transaction->items->sum('subtotal');
        $deposit = $transaction->deposit_amount ?? 0;
        $serviceFee = $subtotal * 0.008; // 0.8% service fee
        $stampDuty = $transaction->ematerai_fee ?? 10000;
        $discount = 0;
        $totalDays = $transaction instanceof Rent ? $transaction->items->sum('duration_days') : 1;

        if ($coupon) {
            $validation = $this->validateCoupon($coupon, $transaction);
            if (!$validation['valid']) {
                throw new \Exception(implode(' ', $validation['errors']));
            }

            $discount = match ($coupon->type) {
                'fixed_amount' => $this->calculateFixedAmountDiscount($coupon, $transaction),
                'percentage' => $subtotal * ($coupon->value / 100),
                'buy_x_get_y' => $this->calculateBuyXGetYDiscount($coupon, $transaction),
                'free_shipping' => 0, // Placeholder for future shipping logic
                default => 0,
            };
        }

        $grandTotal = $subtotal - $discount + $serviceFee + $stampDuty + $deposit;

        return [
            'subtotal' => $subtotal,
            'biaya_layanan' => $serviceFee,
            'biaya_materai' => $stampDuty,
            'diskon' => $discount,
            'deposit' => $deposit,
            'grandtotal' => $grandTotal,
            'total_days' => $totalDays,
        ];
    }

    protected function calculateFixedAmountDiscount(Promo $coupon, $transaction): float
    {
        if ($transaction instanceof Rent) {
            $totalDays = $transaction->items->sum('duration_days');
            $multiplier = floor($totalDays / $coupon->min_order_amount);
            $freeDays = $multiplier * $coupon->value;
            $discount = $transaction->items->sum(fn($item) => $freeDays * $item->price * $item->quantity);
            return $discount;
        }
        return 0;
    }

    protected function calculateBuyXGetYDiscount(Promo $coupon, $transaction): float
    {
        // Implement buy X get Y logic
        return 0; // Placeholder
    }

    public function applyCoupon($transaction, string $couponCode, ?float $paidAmount = null): array
    {
        DB::beginTransaction();
        try {
            $coupon = Promo::where('code', $couponCode)->firstOrFail();
            $validation = $this->validateCoupon($coupon, $transaction);

            if (!$validation['valid']) {
                throw new \Exception(implode(' ', $validation['errors']));
            }

            PromotionUsage::create([
                'promo_id' => $coupon->id,
                'applicable_id' => $transaction->id,
                'applicable_type' => get_class($transaction),
                'discount_amount' => $this->calculateDiscount($transaction, $coupon)['diskon'],
                'user_id' => $transaction->user_id,
            ]);

            $totals = $this->calculateDiscount($transaction, $coupon);
            $transaction->castAndUpdate([
                'promo_id' => $coupon->id,
                'total_amount' => $totals['grandtotal'],
            ]);

            $midtransService = app(MidtransService::class);
            $snapToken = $midtransService->createSnapToken($transaction, $paidAmount ?? $totals['grandtotal']);

            $payment = $transaction->payment ?? new Payment();
            $payment->fill([
                'payable_type' => get_class($transaction),
                'payable_id' => $transaction->id,
                'user_id' => $transaction->user_id,
                'order_id' => $midtransService->generateOrderId($transaction),
                'gross_amount' => $totals['grandtotal'],
                'snap_token' => $snapToken,
                'transaction_time' => now(),
                'currency' => 'IDR',
                'transaction_status' => 'pending',
            ])->save();

            DB::commit();
            return [
                'success' => true,
                'totals' => $totals,
                'snap_token' => $snapToken,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Apply coupon failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function resetCoupon($transaction, ?float $paidAmount = null): array
    {
        DB::beginTransaction();
        try {
            $transaction->promos()->detach();
            PromotionUsage::where('applicable_id', $transaction->id)
                ->where('applicable_type', get_class($transaction))
                ->delete();

            $totals = $this->calculateDiscount($transaction, null);
            $transaction->update([
                'promo_id' => null,
                'total_amount' => $totals['grandtotal'],
            ]);

            $midtransService = app(MidtransService::class);
            $snapToken = $midtransService->createSnapToken($transaction, $paidAmount ?? $totals['grandtotal']);

            if ($payment = $transaction->payment) {
                $payment->update([
                    'snap_token' => $snapToken,
                    'gross_amount' => $totals['grandtotal'],
                    'transaction_time' => now(),
                ]);
            }

            if ($transaction->paid_amount > 0 && $transaction->promo_id) {
                $transaction->user->wallet->deposit($transaction->paid_amount, ['description' => 'Coupon reset refund']);
            }

            DB::commit();
            return [
                'success' => true,
                'totals' => $totals,
                'snap_token' => $snapToken,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reset coupon failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}