<?php

use Illuminate\Support\Facades\Schema;
use LevelUp\Experience\Models\Activity;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->integer('level')->unique();
            $table->integer('next_level_experience')->nullable()->index();
            $table->timestamps();
        });
        Schema::create(config('level-up.table'), function (Blueprint $table) {
            $table->id();
            $table->foreignId(config('level-up.user.foreign_key'))->constrained(config('level-up.user.users_table'));
            $table->foreignId('level_id')->constrained();
            $table->integer('experience_points')->default(0)->index();
            $table->timestamps();
        });
        Schema::create('experience_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId(config('level-up.user.foreign_key'))->constrained(config('level-up.user.users_table'));
            $table->integer('points')->index();
            $table->boolean('levelled_up')->default(false);
            $table->integer('level_to')->nullable();
            $table->enum('type', ['add', 'remove', 'reset', 'level_up']);
            $table->string('reason')->nullable();
            $table->timestamps();
        });
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_secret')->default(false);
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
        Schema::create('achievement_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId(column: config('level-up.user.foreign_key'))->constrained(config('level-up.user.users_table'));
            $table->foreignId(column: 'achievement_id')->constrained();
            $table->integer(column: 'progress')->nullable()->index();
            $table->timestamps();
        });
        Schema::create('streak_activities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });
        Schema::create('streaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId(column: config('level-up.user.foreign_key'))->constrained()->onDelete('cascade');
            $table->foreignId(column: 'activity_id')->constrained('streak_activities')->onDelete('cascade');
            $table->integer(column: 'count')->default(1);
            $table->timestamp(column: 'activity_at');
            $table->timestamp('frozen_until')->nullable();
            $table->timestamps();
        });
        Schema::create(table: 'streak_histories', callback: function (Blueprint $table) {
            $table->id();
            $table->foreignId(column: config(key: 'level-up.user.foreign_key'))->constrained(table: config(key: 'level-up.user.users_table'))->cascadeOnDelete();
            $table->foreignIdFor(model: Activity::class)->constrained(table: 'streak_activities');
            $table->integer(column: 'count')->default(value: 1);
            $table->timestamp(column: 'started_at');
            $table->timestamp(column: 'ended_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('levels');
        Schema::dropIfExists(config('level-up.table'));
        Schema::dropIfExists('experience_audits');
        Schema::dropIfExists('achievements');
        Schema::dropIfExists('achievement_user');
        Schema::dropIfExists('streak_activities');
        Schema::dropIfExists('streaks');
        Schema::dropIfExists('streak_histories');
    }
};
