<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('school_day_id')->constrained('school_days');
            $table->foreignId('subject_id')->nullable()->constrained('subjects');
            $table->enum('note_type', ['dictation', 'quiz', 'homework', 'general'])->default('general');
            $table->string('note');
            $table->integer('marks')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->constrained('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_notes');
    }
};
