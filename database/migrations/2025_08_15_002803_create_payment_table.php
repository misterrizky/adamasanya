<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            // Common fields for all payment types
            $table->morphs('payable'); // Polymorphic relation for sales or rents
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict')->index();
            $table->string('merchant_id')->nullable();
            $table->string('order_id')->nullable()->index();
            $table->string('transaction_midtrans_id')->unique()->nullable();
            $table->decimal('gross_amount', 15, 0);
            $table->string('currency', 5)->default('IDR');
            $table->string('payment_type')->nullable()->comment('bank_transfer, qris, credit_card, dana, etc')->index();
            $table->string('transaction_status')->default('pending')->comment('pending, settlement, capture, etc')->index();
            $table->string('fraud_status')->nullable()->comment('accept, deny')->index();
            $table->string('status_message')->nullable();
            $table->string('status_code')->nullable();
            $table->string('signature_key')->nullable();
            $table->timestamp('transaction_time')->index();
            $table->timestamp('expiry_time')->nullable();
            $table->json('metadata')->nullable();
            // Fields for bank transfer payments
            $table->json('va_numbers')->nullable();
            // Fields for QRIS payments
            $table->string('transaction_type')->nullable()->comment('on-us, off-us, etc');
            $table->timestamp('settlement_time')->nullable();
            $table->string('issuer')->nullable()->comment('gopay, dana, etc');
            $table->string('acquirer')->nullable()->comment('gopay, dana, etc');
            $table->string('merchant_cross_reference_id')->nullable();
            // Fields for credit card payments
            $table->string('bank')->nullable()->comment('mega, bca, etc');
            $table->string('masked_card')->nullable()->comment('481111-1114');
            $table->string('card_type')->nullable()->comment('credit, debit');
            $table->string('three_ds_version')->nullable()->comment('2, 1');
            $table->string('eci')->nullable()->comment('05, 06, etc');
            $table->string('channel_response_code')->nullable()->comment('00');
            $table->string('channel_response_message')->nullable()->comment('Approved');
            $table->string('approval_code')->nullable();
            // Fields for e-wallet payments (DANA, etc)
            $table->string('reference_id')->nullable()->comment('For DANA/OVO/etc');
            $table->string('payment_code')->nullable()->comment('For store payments');
            $table->string('store')->nullable()->comment('For convenience store payments');
            // Other fields
            $table->json('payment_data')->nullable()->comment('paid_amount, remaining_amount, deposit_amount, shipping_price');
            $table->string('snap_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
