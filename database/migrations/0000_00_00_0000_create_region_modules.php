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
        Schema::connection($this->connection)->create('countries', function (Blueprint $table) {
            $table->id();
			$table->string('iso2', 2);
			$table->string('name');
			$table->tinyInteger('status')->default(1);
			$table->string('phone_code', 5);
			$table->string('iso3', 3);
			$table->string('region');
			$table->string('subregion');
        });
        Schema::connection($this->connection)->create('states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->string('country_code',3);
            $table->string('name');
            $table->timestamps();
        });
        Schema::connection($this->connection)->create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->foreignId('state_id')->constrained()->cascadeOnDelete();
            $table->string('code')->nullable();
            $table->string('name');
            $table->string('type')->nullable();
            $table->string('country_code',3);
            $table->timestamps();
        });
        Schema::connection($this->connection)->create('subdistricts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->string('full_code');
            $table->string('name');
            $table->timestamps();
        });
        Schema::connection($this->connection)->create('villages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subdistrict_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->string('full_code');
            $table->string('name');
            $table->string('poscode');
            $table->timestamps();
        });
        Schema::connection($this->connection)->create('currencies', function (Blueprint $table) {
            $table->id();
			$table->foreignId('country_id');
			$table->string('name');
			$table->string('code');
			$table->tinyInteger('precision')->default(2);
			$table->string('symbol');
			$table->string('symbol_native');
			$table->tinyInteger('symbol_first')->default(1);
			$table->string('decimal_mark', 1)->default('.');
			$table->string('thousands_separator', 1)->default(',');
        });
        Schema::connection($this->connection)->create('languages', function (Blueprint $table) {
            $table->id();
			$table->char('code', 2);
			$table->string('name');
			$table->string('name_native');
			$table->char('dir', 3);
        });
        Schema::connection($this->connection)->create('timezones', function (Blueprint $table) {
            $table->id();
			$table->foreignId('country_id');
			$table->string('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
        Schema::dropIfExists('states');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('subdistricts');
        Schema::dropIfExists('villages');
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('languages');
        Schema::dropIfExists('timezones');
    }
};
