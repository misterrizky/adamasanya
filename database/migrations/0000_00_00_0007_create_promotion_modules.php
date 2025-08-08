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
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            // Tipe Promo: percentage, fixed_amount, buy_x_get_y, free_shipping
            $table->enum('type', ['percentage', 'fixed_amount', 'buy_x_get_y', 'free_shipping']);
            // Nilai promo: untuk percentage (10 = 10%), fixed_amount (nominal)
            $table->decimal('value', 10, 2)->nullable();
            // Untuk tipe buy_x_get_y
            $table->integer('buy_quantity')->nullable();
            $table->integer('get_quantity')->nullable();
            $table->foreignId('free_product_id')->nullable()->constrained('products');
            // Batasan penggunaan
            $table->decimal('min_order_amount', 12, 2)->nullable();
            $table->integer('max_uses')->nullable();
            $table->integer('max_uses_per_user')->nullable();
            // Periode promo
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            // Status
            $table->boolean('is_active')->default(true);
            // Scope aplikasi: all, products, categories, branches
            $table->enum('scope', ['all', 'products', 'categories', 'branches'])->default('all');
            $table->enum('day_restriction', ['all', 'weekday', 'weekend'])->default('all');
            $table->enum('applicable_for', ['all', 'rent', 'sale'])->default('all');
            $table->timestamps();
            $table->softDeletes();
            // Index untuk pencarian cepat
            $table->index(['code', 'is_active', 'start_date', 'end_date']);
        });
        Schema::create('promo_branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['promo_id', 'branch_id']);
        });
        Schema::create('promo_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['promo_id', 'category_id']);
        });
        Schema::create('promo_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['promo_id', 'product_id']);
        });
        Schema::create('promotion_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->morphs('applicable'); // Akan membuat: applicable_id dan applicable_type
            $table->decimal('discount_amount', 12, 2);
            $table->timestamps();
            // Index untuk laporan
            $table->index(['promo_id', 'user_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promos');
        Schema::dropIfExists('promo_branches');
        Schema::dropIfExists('promo_categories');
        Schema::dropIfExists('promo_products');
        Schema::dropIfExists('promotion_usages');
    }
};
