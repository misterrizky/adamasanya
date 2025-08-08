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
        Schema::table('payment_rents', function (Blueprint $table) {
            $table->string('snap_token')->nullable()->after('payment_data');
        });

        Schema::table('payment_sales', function (Blueprint $table) {
            $table->string('snap_token')->nullable()->after('payment_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_rents', function (Blueprint $table) {
            $table->dropColumn('snap_token');
        });

        Schema::table('payment_sales', function (Blueprint $table) {
            $table->dropColumn('snap_token');
        });
    }
};
