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
        // create_products_table
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained();
            $table->foreignId('category_id')->constrained();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('code')->unique();
            $table->string('thumbnail');
            $table->text('description_rent');
            $table->timestamps();
        });

        // create_product_branch_table
        Schema::create('product_branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('product_id')->constrained();
            $table->string('color');
            $table->string('storage');
            $table->decimal('rent_price', 12, 2);
            $table->decimal('sale_price', 12, 2)->default(0);
            $table->string('icloud')->nullable();
            $table->string('imei')->nullable();
            $table->boolean('is_publish')->default(true);
            $table->integer('views')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_branches');
    }
};
