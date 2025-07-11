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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_period_id')->constrained('class_periods');
            $table->foreignId('teacher_section_subject_id')->constrained('teacher_section_subjects');
            $table->foreignId('timetable_id')->constrained('time_tables');
            $table->integer('week_day');
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
