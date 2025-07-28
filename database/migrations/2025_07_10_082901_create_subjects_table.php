<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('subject_major_id')->constrained('subject_majors');
            $table->string('code');
            $table->integer('full_mark');
            $table->integer('homework_percentage');
            $table->integer('oral_percentage');
            $table->integer('activity_percentage');
            $table->integer('quiz_percentage');
            $table->integer('exam_percentage');
            $table->integer('num_class_period');
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('subjects');
    }
};
