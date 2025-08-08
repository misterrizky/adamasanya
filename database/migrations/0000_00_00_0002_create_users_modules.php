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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->index();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('password');
            $table->string('pin')->nullable();
            $table->string('avatar')->nullable();
            $table->string('google_id')->nullable();
            $table->foreignId('love_reacter_id')->nullable();
            $table->enum('st', ['pending','unverified','verified','suspend'])->default('pending');
            $table->timestamp('last_seen')->nullable();
            $table->timestamp('banned_at')->nullable();
            $table->integer('banned_by')->nullable();
            $table->integer('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->text('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('label');
            $table->longText('address');
            $table->foreignId('country_id')->nullable()->index();
            $table->foreignId('state_id')->nullable()->index();
            $table->foreignId('city_id')->nullable()->index();
            $table->foreignId('subdistrict_id')->nullable()->index();
            $table->foreignId('village_id')->nullable()->index();
            $table->string('postal_code')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->boolean('is_primary')->default(true);
            $table->longText('notes');
            $table->timestamps();
        });
        Schema::create('user_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index();
            $table->foreignId('bank_id')->nullable()->index();
            $table->string('account_number');
            $table->string('account_holder');
            $table->timestamps();
        });
        Schema::create('user_coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index();
            $table->foreignId('promo_id')->nullable()->index();
            $table->boolean('is_used');
            $table->timestamps();
        });
        Schema::create('user_families', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('type')->comment('Ayah, Ibu, Kakak, Adik, Suami, Istri');
            $table->string('name');
            $table->string('phone', 13);
            $table->string('tags')->nullable();
            $table->timestamps();
        });
        Schema::create('user_meters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('amount');
            $table->timestamps();
        });
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index();
            $table->enum('gender', ['pria','wanita'])->nullable();
            $table->string('nik', 16)->nullable();
            $table->date('bod')->nullable()->comment('Tanggal Lahir');
            $table->string('pob')->nullable()->comment('Tempat Lahir');
            $table->string('id_card')->nullable();
            $table->string('family_card')->nullable();
            $table->string('selfie')->nullable();
            $table->string('ig')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('wa', 16)->nullable();
            $table->timestamps();
        });
        Schema::create('user_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('name');
            $table->integer('created_by');
            $table->timestamps();
        });

        Schema::create('user_verify', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->index();
            $table->string('token');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('user_addresses');
        Schema::dropIfExists('user_banks');
        Schema::dropIfExists('user_coupons');
        Schema::dropIfExists('user_families');
        Schema::dropIfExists('user_tags');
        Schema::dropIfExists('user_verify');
    }
};
