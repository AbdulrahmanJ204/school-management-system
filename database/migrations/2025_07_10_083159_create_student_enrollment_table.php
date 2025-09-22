<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('sections');
            $table->foreignId('grade_id')->constrained('grades');
            $table->foreignId('semester_id')->constrained('semesters');
            $table->foreignId('year_id')->constrained('years');
            $table->decimal('last_year_gpa', 5, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->constrained('users');

            $table->index(['student_id', 'semester_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_enrollments');
    }
};
