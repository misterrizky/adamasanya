<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Services\MidtransService;
use App\Models\Transaction\Payment;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function handleWebhook(Request $request)
    {
        try {
            $rawInput = $request->getContent();
            if (empty($rawInput)) {
                throw new Exception('Empty webhook payload received');
            }

            // Decode JSON to array
            $notification = json_decode($rawInput, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON payload: ' . json_last_error_msg());
            }

            $this->midtransService->handleNotification($notification);  // Pass array
            return response()->json(['status' => 'success'], 200);
        } catch (Exception $e) {
            Log::error('Midtrans webhook failed', [
                'request_body' => $request->getContent() ?: 'empty',
                'headers' => $request->headers->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Webhook processing failed: ' . $e->getMessage(),
            ], 400);
        }
    }

    public function checkStatus(Payment $payment)
    {
        try {
            $status = \Midtrans\Transaction::status($payment->order_id);
            return view('admin.payment.status', compact('status', 'payment'));
        } catch (Exception $e) {
            Log::error('Midtrans status check failed', [
                'payment_id' => $payment->id,
                'order_id' => $payment->order_id,
                'error' => $e->getMessage(),
            ]);
            return back()->with('error', 'Failed to check payment status');
        }
    }
}