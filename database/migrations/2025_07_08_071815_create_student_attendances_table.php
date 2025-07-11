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
        Schema::create('student_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('school_day_id')->constrained('school_days');
            $table->foreignId('class_period_id')->nullable()->constrained('class_periods');
            $table->enum('status', ['Excused absence', 'Unexcused absence', 'late']);
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');

            $table->index(['student_id', 'school_day_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_attendances');
    }
};
