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
        // 1. School & CBT Settings
        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();
            $table->string('school_name')->default('SMA NEGERI 1 UNGGULAN');
            $table->string('school_address')->default('Jl. Pendidikan No. 45, Telp: (021) 778899, Email: info@sman1unggulan.sch.id');
            $table->string('headmaster_name')->default('Drs. H. Ahmad Dahlan, M.Pd.');
            $table->string('headmaster_nip')->default('19720514 199803 1 004');
            $table->string('committee_name')->default('Panitia Ujian Sekolah Berbasis Komputer (CBT)');
            $table->string('academic_year')->default('2026/2027');
            $table->string('semester')->default('Ganjil');
            $table->integer('default_duration')->default(120); // 120 minutes default
            $table->string('logo_kiri')->nullable(); // Kemdikbud / Tut Wuri Handayani
            $table->string('logo_kanan')->nullable(); // School logo
            $table->timestamps();
        });

        // 2. Subjects (Mata Pelajaran)
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('education_level')->default('SMA/MA/SMK/MAK/Sederajat');
            $table->string('category')->default('Mata Pelajaran Wajib');
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 3. Exams (Jadwal & Paket Ujian)
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->string('title'); // e.g. "Bahasa Inggris (Paket 1)"
            $table->string('category')->default('UTS'); // UTS, UAS, Simulasi TKA, Asesmen Sumatif
            $table->string('education_level')->default('SMA/MA/SMK/MAK/Sederajat');
            $table->integer('duration_minutes')->default(120); // Customizable by admin!
            $table->decimal('passing_grade', 5, 2)->default(75.00); // KKM
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->enum('status', ['draft', 'active', 'completed'])->default('draft');
            $table->boolean('shuffle_questions')->default(true);
            $table->boolean('shuffle_options')->default(true);
            $table->integer('max_violations')->default(3); // Auto-lock after 3 violations
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 4. Tokens
        Schema::create('tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->nullable()->constrained('exams')->nullOnDelete();
            $table->string('token_code', 10);
            $table->dateTime('valid_until'); // Active for 10 minutes from creation
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Questions (Bank Soal)
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->longText('stimulus')->nullable(); // Passage / Reading Text / Context
            $table->longText('question_text');
            $table->string('question_image')->nullable();
            $table->string('audio_file')->nullable();
            $table->text('option_a');
            $table->text('option_b');
            $table->text('option_c');
            $table->text('option_d');
            $table->text('option_e')->nullable();
            $table->enum('correct_answer', ['A', 'B', 'C', 'D', 'E']);
            $table->decimal('score_weight', 5, 2)->default(1.00);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 6. Exam Attempts (Sesi Pengerjaan Siswa)
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('token_used')->nullable();
            $table->json('question_order')->nullable(); // Saved shuffled array of question IDs
            $table->dateTime('started_at');
            $table->integer('duration_minutes');
            $table->dateTime('end_at'); // Strict server-side end time
            $table->dateTime('finished_at')->nullable();
            $table->enum('status', ['ongoing', 'finished', 'locked'])->default('ongoing');
            $table->integer('violations_count')->default(0);
            $table->dateTime('last_heartbeat')->nullable();
            $table->decimal('score', 6, 2)->default(0.00);
            $table->integer('correct_count')->default(0);
            $table->integer('incorrect_count')->default(0);
            $table->integer('unanswered_count')->default(0);
            $table->timestamps();

            $table->unique(['exam_id', 'user_id']);
        });

        // 7. Exam Answers (Jawaban Siswa)
        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('exam_attempts')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->string('selected_option', 5)->nullable(); // A, B, C, D, E or null
            $table->boolean('is_doubtful')->default(false); // Ragu-ragu
            $table->boolean('is_correct')->default(false);
            $table->timestamps();

            $table->unique(['attempt_id', 'question_id']);
        });

        // 8. Exam Violations (Log Pelanggaran / Peringatan Anti-Curang)
        Schema::create('exam_violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('exam_attempts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('violation_type'); // 'tab_switch', 'window_blur', 'devtools_opened', 'copy_attempt', 'print_screen'
            $table->text('details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_violations');
        Schema::dropIfExists('exam_answers');
        Schema::dropIfExists('exam_attempts');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('tokens');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('school_settings');
    }
};
