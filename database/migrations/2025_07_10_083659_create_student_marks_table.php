<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects');
            $table->foreignId('enrollment_id')->constrained('student_enrollments');
            $table->integer('homework')->nullable();
            $table->integer('oral')->nullable();
            $table->integer('activity')->nullable();
            $table->integer('quiz')->nullable();
            $table->integer('exam')->nullable();
            $table->integer('total')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');

            $table->index(['enrollment_id', 'subject_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_marks');
    }
};
