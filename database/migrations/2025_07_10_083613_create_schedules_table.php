<?php

use App\Enums\WeekDay;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_period_id')->constrained('class_periods');
            $table->foreignId('teacher_section_subject_id')->constrained('teacher_section_subjects');
            $table->foreignId('timetable_id')->constrained('timetables');
            $table->enum('week_day', [
                WeekDay::SUNDAY->value,
                WeekDay::MONDAY->value,
                WeekDay::TUESDAY->value,
                WeekDay::WEDNESDAY->value,
                WeekDay::THURSDAY->value,
                WeekDay::FRIDAY->value,
                WeekDay::SATURDAY->value,
            ]);            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
