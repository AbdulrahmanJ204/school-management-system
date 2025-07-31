<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('school_days', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('semester_id')->constrained('semesters');
            $table->enum('type', ['study', 'exam', 'holiday']);
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->constrained('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('school_days');
    }

};
