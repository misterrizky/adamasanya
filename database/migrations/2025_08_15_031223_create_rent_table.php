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
        Schema::create('rents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->foreignId('promo_id')->nullable()->constrained('promos')->onDelete('set null');
            $table->string('code')->unique()->index();
            $table->enum('status', ['pending', 'confirmed', 'active', 'completed', 'cancelled', 'overdue'])->default('pending')->index();
            $table->date('start_date')->index();
            $table->date('end_date')->index();
            $table->time('pickup_time')->nullable();
            $table->time('return_time')->nullable();
            $table->decimal('total_amount', 15, 0)->default(0);
            $table->decimal('deposit_amount', 15, 0)->default(0);
            $table->decimal('ematerai_fee', 15, 0)->default(10000);
            $table->decimal('paid_amount', 15, 0)->default(0);
            $table->text('notes')->nullable();
            $table->string('pickup_signature')->nullable();
            $table->string('pickup_ematerai_id')->nullable();
            $table->string('return_signature')->nullable();
            $table->string('return_ematerai_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('rent_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rent_id')->index();
            $table->foreignId('product_branch_id')->index();
            $table->integer('quantity')->default(1);
            $table->decimal('price', 15, 0);
            $table->integer('duration_days')->default(1);
            $table->decimal('subtotal', 15, 0)->default(0);
            $table->text('notes')->nullable();
            $table->text('damage_report')->nullable();
            $table->decimal('damage_fee', 15, 0)->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rents');
        Schema::dropIfExists('rent_items');
    }
};
