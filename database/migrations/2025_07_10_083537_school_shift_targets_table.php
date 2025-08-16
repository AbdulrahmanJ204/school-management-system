<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_shift_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_shift_id')->constrained('school_shifts');
            $table->foreignId('section_id')->nullable()->constrained('sections');
            $table->foreignId('grade_id')->constrained('grades');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('school_shift_targets');
    }
};
