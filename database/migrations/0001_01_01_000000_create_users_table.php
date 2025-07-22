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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Personal Details
            $table->string('first_name');
            $table->string('father_name')->nullable();
            $table->string('last_name');
            $table->enum('gender', ['male', 'female']);
            $table->date('birth_date')->nullable();

            // Contact & Account
            $table->string('email')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->string('password')->nullable();

            // Role system
            $table->enum('user_type', ['admin', 'teacher', 'student'])->default('student');

            // Email verification system (custom)
            $table->timestamp('email_verified_at')->nullable();
            $table->string('email_verification_token', 64)->nullable();

            // Profile
            $table->string('image')->nullable(); // stored as path to image in storage/app

            // Activity tracking
            $table->timestamp('last_login')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
