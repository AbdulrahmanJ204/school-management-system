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
        Schema::create('daily_log_reports', function (Blueprint $table) {
            $table->id();
            $table->date('report_date');
            $table->integer('total_logs')->default(0);
            $table->string('pdf_path')->nullable();
            $table->string('excel_path')->nullable();
            $table->timestamps();
            
            $table->unique('report_date');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_log_reports');
    }
};
