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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke transaksi (bisa sewa atau beli)
            $table->foreignId('rent_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('sale_id')->nullable()->constrained()->onDelete('cascade');
            
            // Relasi ke produk dan cabang
            $table->foreignId('product_id')->constrained();
            $table->foreignId('branch_id')->constrained();
            
            // Informasi user
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Rating (1-5)
            $table->tinyInteger('rating')->unsigned()->between(1, 5);
            
            // Ulasan
            $table->text('review')->nullable();
            
            // Status moderasi
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            
            // Fitur tambahan
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('would_recommend')->default(true);
            
            $table->timestamps();
            
            // Constraint unik: satu user hanya bisa memberi satu rating per transaksi produk
            $table->unique(['user_id', 'rent_id', 'product_id'], 'unique_rent_rating');
            $table->unique(['user_id', 'sale_id', 'product_id'], 'unique_sale_rating');
            
            // Index untuk performa
            $table->index(['product_id', 'branch_id', 'status']);
        });
        Schema::create('rating_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rating_id')->constrained()->onDelete('cascade');
            
            // Path ke media (gambar/video)
            $table->string('media_path');
            
            // Tipe media: image/jpeg, video/mp4, dll
            $table->string('mime_type');
            
            // Urutan tampilan
            $table->integer('order')->default(0);
            
            $table->timestamps();
        });
        Schema::create('rating_helpfulness', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rating_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Apakah ulasan ini membantu? (1 = ya, 0 = tidak)
            $table->boolean('helpful');
            
            $table->timestamps();
            
            // Satu user hanya bisa vote sekali per rating
            $table->unique(['rating_id', 'user_id']);
        });
        Schema::create('rating_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rating_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_user_id')->constrained('users')->onDelete('cascade'); // Staff cabang
            
            // Tanggapan
            $table->text('response');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
        Schema::dropIfExists('rating_media');
        Schema::dropIfExists('rating_helpfulness');
        Schema::dropIfExists('rating_responses');
    }
};
