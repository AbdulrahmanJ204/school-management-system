<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('news_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained('news');
            $table->foreignId('grade_id')->nullable()->constrained('grades');
            $table->foreignId('section_id')->nullable()->constrained('sections');
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('news_targets');
    }
};
