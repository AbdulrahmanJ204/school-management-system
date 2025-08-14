<?php

use App\Enums\ClassPeriodType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->time('start_time');
            $table->time('end_time');
            $table->foreignId('school_shift_id')->constrained('school_shifts');
            $table->integer('period_order');
            $table->enum('type', ClassPeriodType::values())->default(ClassPeriodType::STUDY->value);
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');

            $table->unique(['school_shift_id', 'period_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_periods');
    }
};
