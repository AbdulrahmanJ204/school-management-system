<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @return void
     */
    public function up(): void
    {
        Schema::create('file_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->nullable()->constrained('sections');
            $table->foreignId('grade_id')->nullable()->constrained('grades');
            $table->foreignId('file_id')->constrained('files');
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->constrained('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('file_targets');
    }
};
