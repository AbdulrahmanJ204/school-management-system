<?php

use App\Http\Middleware\LogErrors;
use App\Http\Middleware\UserTypeMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(LogErrors::class);
        $middleware->alias([
            'user_type' => UserTypeMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withSchedule(function (Schedule $schedule) {
        // Daily log report generation and email sending at 4:00 AM
        $schedule->job(new \App\Jobs\GenerateDailyLogReportJob())
                 ->name('generate-daily-log-report')
                 ->dailyAt('04:00')
                 ->withoutOverlapping()
                 ->onFailure(function () {
                     \Log::error('Failed to generate daily log report');
                 });

        // Send daily reports after generation
        $schedule->call(function () {
            $report = \App\Models\DailyLogReport::latest('report_date')->first();
            if ($report) {
                \App\Jobs\SendDailyLogReportJob::dispatch($report);
            }
        })->name('send-daily-log-report')
          ->dailyAt('04:05')
          ->withoutOverlapping()
          ->onFailure(function () {
              \Log::error('Failed to send daily log report');
          });

        // Clean old logs after generation
        $schedule->job(new \App\Jobs\CleanOldLogsJob(90))
                 ->name('clean-old-logs')
                 ->dailyAt('04:10')
                 ->withoutOverlapping()
                 ->onFailure(function () {
                    \Log::error('Failed to clean old logs');
                });
    })->create();
