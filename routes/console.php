<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule daily log report generation
Artisan::command('logs:schedule-daily-report', function () {
    $this->info('Scheduling daily log report generation...');
    
    // This command will be called by the scheduler
    Artisan::call('logs:generate-daily-report');
    
    $this->info('Daily log report generation completed.');
})->purpose('Generate daily log report (scheduled task)');

// Schedule daily log report generation and email sending
Artisan::command('logs:schedule-send-daily-report', function () {
    $this->info('Scheduling daily log report generation and email sending...');
    
    // This command will be called by the scheduler
    Artisan::call('logs:send-daily-report');
    
    $this->info('Daily log report generation and email sending completed.');
})->purpose('Generate and send daily log report via email (scheduled task)');

// Schedule log cleanup
Artisan::command('logs:schedule-cleanup', function () {
    $this->info('Scheduling log cleanup...');
    
    // Clean logs older than 90 days
    Artisan::call('logs:clean', ['--days' => 90]);
    
    $this->info('Log cleanup completed.');
})->purpose('Clean old logs (scheduled task)');
