<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_period_id')->constrained('class_periods');
            $table->foreignId('school_day_id')->constrained('school_days');
            $table->foreignId('teacher_id')->constrained('teachers');
            $table->enum('status', ['Excused absence', 'Unexcused absence', 'late']);
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_attendances');
    }
};
