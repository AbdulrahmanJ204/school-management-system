<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_app_updates', function (Blueprint $table) {
            $table->id();
            $table->string('version');
            $table->string('platform');
            $table->string('url');
            $table->text('change_log')->nullable();
            $table->boolean('is_force_update');
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_app_updates');
    }
};
