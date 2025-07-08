<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedules_id')->constrained('schedules');
            $table->foreignId('school_day_id')->constrained('school_days');
            $table->enum('type', ['homework', 'oral']);
            $table->string('title');
            $table->text('description');
            $table->string('photo');
            $table->foreignId('subject_id')->constrained('subjects');
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');

            $table->index(['school_day_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
