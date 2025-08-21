<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CheckoutService;

class PaymentController extends Controller
{
    protected $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    public function checkout(Request $request)
    {
        $data = [
            'product_id' => $request->input('product_id'),
            'customer_id' => $request->input('customer_id'),
            'quantity' => $request->input('quantity'),
            'total_price' => $request->input('total_price'),
            'customer_name' => $request->input('name'),
            'customer_email' => $request->input('email'),
            'customer_phone' => $request->input('phone_number'),
        ];

        $result = $this->checkoutService->processCheckout($data);

        return response()->json([
            'snap_token' => $result['snap_token'],
            'transaction' => $result['transaction']
        ]);
    }
    public function handleWebhook(Request $request)
    {
        // Mengambil konfigurasi Server Key
        $serverKey = config('midtrans.server_key');

        // Validasi signature key dari Midtrans
        $signatureKey = hash("sha512",
            $request->order_id .
            $request->status_code .
            $request->gross_amount .
            $serverKey
        );

        if ($signatureKey !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature key'], 403);
        }

        // Cek status transaksi
        $transaction = Transaction::find($request->order_id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        if ($request->transaction_status == 'settlement' || $request->transaction_status == 'capture') {
            $transaction->status = 'confirmed'; // Status pembayaran berhasil
            $transaction->status_paid = 'completed'; // Status pembayaran berhasil
        } elseif ($request->transaction_status == 'cancel' || $request->transaction_status == 'expire') {
            $transaction->status_paid = 'failed'; // Status pembayaran gagal atau kadaluarsa
        } elseif ($request->transaction_status == 'pending') {
            $transaction->status = 'pending'; // Status menunggu pembayaran
            $transaction->status_paid = 'pending'; // Status menunggu pembayaran
        }

        $transaction->save();

        return response()->json(['message' => 'Webhook processed successfully']);
    }
}
