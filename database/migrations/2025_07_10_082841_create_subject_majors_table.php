<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('subject_majors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_id')->constrained('grades');
            $table->string('name');
            $table->string('code');
            $table->integer('success_rate');
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('subject_majors');
    }
};
