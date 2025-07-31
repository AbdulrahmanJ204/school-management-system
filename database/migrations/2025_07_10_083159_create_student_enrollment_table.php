<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('section_id')->constrained('sections');
            $table->foreignId('grade_id')->constrained('grades');
            $table->foreignId('semester_id')->constrained('semesters');
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->constrained('users');

            $table->index(['student_id', 'semester_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_enrollments');
    }
};
