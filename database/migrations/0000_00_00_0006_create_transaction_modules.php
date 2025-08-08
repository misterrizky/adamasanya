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
        // create_carts_table
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            // Untuk user yang sudah login
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            // Untuk guest user (berdasarkan session)
            $table->string('session_id')->nullable()->index();
            // Cabang yang dipilih untuk penyewaan
            $table->foreignId('branch_id')->constrained();
            // Tanggal penyewaan
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            // Informasi tambahan
            $table->text('notes')->nullable();
            // Status keranjang
            $table->enum('status', ['active', 'converted', 'abandoned'])->default('active');
            $table->timestamps();
            // Index untuk performa
            $table->index(['user_id', 'session_id']);
        });
        // create_cart_items_table
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            // Relasi ke cart
            $table->foreignId('cart_id')->constrained()->onDelete('cascade');
            // Produk spesifik dari cabang tertentu
            $table->foreignId('product_branch_id')->constrained();
            // Kuantitas (biasanya 1 untuk produk elektronik)
            $table->integer('quantity')->default(1);
            // Harga per hari saat dimasukkan ke keranjang
            $table->decimal('rent_price_per_day', 12, 2);
            // Variasi produk
            $table->string('selected_color');
            $table->string('selected_storage');
            // Tanggal khusus untuk item ini (jika berbeda dari cart)
            $table->dateTime('item_start_date')->nullable();
            $table->dateTime('item_end_date')->nullable();
            $table->timestamps();
            // Index untuk performa
            $table->index(['cart_id', 'product_branch_id']);
        });
        // create_rents_table
        Schema::create('rents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained();
            $table->string('konsumen');
            $table->string('code')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->text('notes')->nullable();
            $table->integer('total_days');
            $table->integer('total_hour_late')->default(0);
            $table->decimal('total_repair_fee', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('deposit_amount', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2);
            $table->decimal('total_paid', 12, 2);
            $table->string('proof_of_collection')->nullable();
            $table->enum('type', ['online', 'offline']);
            $table->enum('payment_type', ['transfer', 'cash', 'qris', 'other']);
            $table->enum('status', ['pending', 'confirmed', 'on_rent', 'completed', 'canceled']);
            $table->enum('status_paid', ['pending', 'partial', 'completed', 'failed']);
            $table->timestamps();
        });

        // create_rent_items_table
        Schema::create('rent_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rent_id')->constrained();
            $table->foreignId('product_branch_id')->constrained();
            $table->integer('qty');
            $table->decimal('price', 12, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('repair_fee', 12, 2)->default(0);
            $table->decimal('penalty_fee', 12, 2)->default(0);
            $table->timestamps();
        });

        // create_sales_table
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained();
            $table->string('customer_name');
            $table->string('code')->unique();
            $table->dateTime('sale_date');
            $table->decimal('total_price', 12, 2);
            $table->decimal('total_paid', 12, 2);
            $table->enum('payment_type', ['transfer', 'cash', 'qris', 'other']);
            $table->enum('status', ['pending', 'completed', 'canceled']);
            $table->timestamps();
        });

        // create_sale_items_table
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained();
            $table->foreignId('product_branch_id')->constrained();
            $table->integer('qty');
            $table->decimal('price', 12, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
        Schema::create('signatures', function (Blueprint $table) {
            $table->id();
            $table->morphs('model');
            $table->string('uuid');
            $table->string('filename');
            $table->string('document_filename')->nullable();
            $table->boolean('certified')->default(false);
            $table->json('from_ips')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('rents');
        Schema::dropIfExists('rent_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('signatures');
    }
};
