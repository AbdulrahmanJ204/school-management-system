<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_active');
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_shifts');
    }
};
