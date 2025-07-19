<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->foreignId('school_day_id')->constrained('school_days');
            $table->string('photo')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('news');
    }
};
