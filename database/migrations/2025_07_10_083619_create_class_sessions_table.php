<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules');
            $table->foreignId('school_day_id')->constrained('school_days');
            $table->foreignId('teacher_id')->constrained('teachers');
            $table->foreignId('subject_id')->constrained('subjects');
            $table->foreignId('section_id')->constrained('sections');
            $table->foreignId('class_period_id')->constrained('class_periods');

            $table->enum('status', [
                'scheduled',
                'completed',
                'cancelled',
            ])->default('scheduled');
            

            $table->integer('total_students_count')->nullable();
            $table->integer('present_students_count')->nullable();
            
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');
            
            $table->index(['school_day_id', 'section_id']);
            $table->index(['teacher_id', 'school_day_id']);
            $table->index(['status', 'school_day_id']);
            
            $table->unique(['schedule_id', 'school_day_id'], 'unique_session_per_day');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_sessions');
    }
};