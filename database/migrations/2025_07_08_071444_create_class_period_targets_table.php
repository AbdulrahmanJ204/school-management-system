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
        Schema::create('class_period_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_period_id')->constrained('class_periods');
            $table->foreignId('section_id')->nullable()->constrained('sections');
            $table->foreignId('grade_id')->constrained('grades');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_period_targets');
    }
};
