<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->nullable()->constrained('subjects');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file');
            $table->unsignedBigInteger('size');
            $table->foreignId('school_day_id')->constrained('school_days');
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('files');
    }

};
