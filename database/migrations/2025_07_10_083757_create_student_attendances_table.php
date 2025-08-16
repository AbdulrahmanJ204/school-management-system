<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('class_session_id')->constrained('class_sessions');
            $table->enum('status', ['Excused absence', 'Unexcused absence', 'Late']);
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');

            $table->index(['class_session_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_attendances');
    }
};
