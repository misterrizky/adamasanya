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
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('st', ['a', 'i'])->default('a')->comment('a=active, i=inactive');
            $table->timestamps();
        });
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category');
            $table->string('thumbnail');
            $table->enum('st', ['a', 'i'])->default('a')->comment('a=active, i=inactive');
            $table->timestamps();
        });
        Schema::create('blood_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('category');
            $table->string('ig');
            $table->string('gmaps');
            $table->longText('address');
            $table->string('phone');
            $table->foreignId('country_id')->nullable()->index();
            $table->foreignId('state_id')->nullable()->index();
            $table->foreignId('city_id')->nullable()->index();
            $table->foreignId('subdistrict_id')->nullable()->index();
            $table->foreignId('village_id')->nullable()->index();
            $table->string('postal_code')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->string('percentage')->nullable()->comment('persentase');
            $table->enum('is_hq', ['y', 'n'])->default('n');
            $table->enum('st', ['a', 'i'])->default('a')->comment('a=active, i=inactive');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('branch_schedules', function (Blueprint $table) {
            $table->id();
            // FK relasi ke tabel branches
            $table->foreignId('branch_id')->constrained();
            // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
            $table->tinyInteger('day_of_week')->comment('0=Sunday, 6=Saturday');
            // Gunakan time, bukan integer
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            // Gunakan boolean untuk kejelasan
            $table->boolean('is_open')->default(false);
            $table->timestamps();
        });
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('thumbnail');
            $table->enum('st', ['a', 'i'])->default('a')->comment('a=active, i=inactive');
            $table->timestamps();
        });
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('thumbnail');
            $table->enum('st', ['a', 'i'])->default('a')->comment('a=active, i=inactive');
            $table->timestamps();
        });
        Schema::create('faq_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faq_category_id')->constrained();
            $table->string('question');
            $table->text('answer');
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('professions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        
        Schema::create('religions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('school_levels', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('reason')->nullable();
            $table->morphs('model');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banks');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('blood_types');
        Schema::dropIfExists('branches');
        Schema::dropIfExists('branch_schedules');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('faq_categories');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('professions');
        Schema::dropIfExists('religions');
        Schema::dropIfExists('school_levels');
        Schema::dropIfExists('statuses');
    }
};
