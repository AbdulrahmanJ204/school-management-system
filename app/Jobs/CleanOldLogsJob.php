<?php

namespace App\Jobs;

use App\Services\LoggingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CleanOldLogsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $days;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 2;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(int $days = 90)
    {
        $this->days = $days;
    }

    /**
     * Execute the job.
     */
    public function handle(LoggingService $loggingService): void
    {
        Log::info("Starting CleanOldLogsJob for logs older than {$this->days} days");

        try {
            $deletedCount = $loggingService->cleanOldLogs($this->days);
            
            Log::info("CleanOldLogsJob completed successfully. Deleted {$deletedCount} old log entries");

        } catch (\Exception $e) {
            Log::error("CleanOldLogsJob failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("CleanOldLogsJob failed permanently: " . $exception->getMessage());
        Log::error("Stack trace: " . $exception->getTraceAsString());
    }
}
