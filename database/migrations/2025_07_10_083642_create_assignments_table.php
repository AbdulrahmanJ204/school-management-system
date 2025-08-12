<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_session_id')->constrained('class_sessions');
            $table->enum('type', ['homework', 'oral', 'quiz', 'project']);
            $table->string('title');
            $table->text('description');
            $table->string('photo')->nullable();
            $table->foreignId('subject_id')->constrained('subjects');
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->constrained('users');

            $table->index(['class_session_id', 'subject_id']);
            $table->index(['class_session_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
