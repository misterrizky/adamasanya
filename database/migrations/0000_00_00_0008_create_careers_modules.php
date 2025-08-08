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
        Schema::create('careers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('benefits')->nullable(); // Benefit bekerja di perusahaan
            $table->text('culture')->nullable(); // Budaya perusahaan
            $table->timestamps();
        });
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->string('position'); // Posisi pekerjaan
            $table->string('slug')->unique();
            $table->foreignId('branch_id')->constrained(); // Cabang yang membuka lowongan
            $table->enum('type', ['full-time', 'part-time', 'contract', 'internship', 'freelance']);
            $table->text('description'); // Deskripsi pekerjaan
            $table->text('responsibilities'); // Tanggung jawab
            $table->text('requirements'); // Persyaratan
            $table->decimal('min_salary', 12, 2)->nullable();
            $table->decimal('max_salary', 12, 2)->nullable();
            $table->enum('salary_type', ['monthly', 'hourly', 'project']);
            $table->boolean('is_remote')->default(false);
            $table->date('posted_at');
            $table->date('closed_at');
            $table->integer('open_positions')->default(1);
            $table->enum('status', ['draft', 'published', 'closed'])->default('draft');
            $table->timestamps();
            // Index untuk pencarian
            $table->index(['position', 'type', 'status', 'branch_id']);
            $table->fulltext(['position', 'description']);
        });
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacancy_id')->constrained();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->text('cover_letter')->nullable();
            $table->string('resume_path'); // Path ke file CV
            $table->string('portofolio_path')->nullable();
            $table->string('source')->nullable(); // Sumber info lowongan
            $table->enum('status', [
                'submitted', 
                'reviewed', 
                'interviewing', 
                'offer', 
                'rejected', 
                'hired'
            ])->default('submitted');
            $table->timestamps();
            
            // Index untuk tracking
            $table->index(['vacancy_id', 'status', 'email', 'phone']);
            $table->fulltext(['full_name', 'cover_letter']);
        });
        Schema::create('application_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained();
            $table->text('content');
            $table->foreignId('author_id')->constrained('users'); // HR/user yang membuat catatan
            $table->boolean('is_private')->default(true); // Catatan internal
            $table->timestamps();
        });
        Schema::create('application_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained();
            $table->enum('status', [
                'submitted', 
                'reviewed', 
                'interviewing', 
                'offer', 
                'rejected', 
                'hired'
            ]);
            $table->text('notes')->nullable();
            $table->foreignId('changed_by')->constrained('users'); // HR/user yang mengubah status
            $table->timestamps();
        });
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained();
            $table->dateTime('scheduled_at');
            $table->string('location')->nullable();
            $table->string('meeting_url')->nullable(); // Untuk wawancara online
            $table->text('notes')->nullable();
            $table->enum('type', ['phone', 'video', 'in-person']);
            $table->enum('status', ['scheduled', 'completed', 'canceled'])->default('scheduled');
            $table->foreignId('interviewer_id')->constrained('users'); // Pewawancara
            $table->timestamps();
            $table->index(['scheduled_at', 'status']);
        });
        Schema::create('hires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained();
            $table->date('join_date');
            $table->decimal('salary', 12, 2);
            $table->enum('salary_type', ['monthly', 'hourly', 'project']);
            $table->text('offer_details')->nullable();
            $table->date('offer_sent_at');
            $table->date('offer_accepted_at')->nullable();
            $table->foreignId('hired_by')->constrained('users'); // HR yang memproses hiring
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('careers');
    }
};
