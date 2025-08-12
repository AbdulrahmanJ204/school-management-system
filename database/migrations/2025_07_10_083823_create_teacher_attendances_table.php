<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_session_id')->constrained('class_sessions');
            $table->foreignId('teacher_id')->constrained('teachers');
            $table->enum('status', ['Excused absence', 'Unexcused absence', 'Late']);
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_attendances');
    }
};
