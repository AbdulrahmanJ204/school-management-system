<?php

namespace App\Jobs;

use App\Models\DailyLogReport;
use App\Models\User;
use App\Notifications\DailyLogReportNotification;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendDailyLogReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected DailyLogReport $report;
    protected ?Carbon $date;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(DailyLogReport $report, ?Carbon $date = null)
    {
        $this->report = $report;
        $this->date = $date;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Starting SendDailyLogReportJob for report ID: {$this->report->id}");

        try {
            // Get all users with admin role and owner permission
            $adminUsers = User::where('user_type', 'admin')
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'owner');
                })->get();

            Log::info("Found {$adminUsers->count()} admin users with owner permission");

            $sentCount = 0;
            $failedCount = 0;

            foreach ($adminUsers as $admin) {
                try {
                    Log::info("Sending daily report to admin: {$admin->email}");
                    
                    // Send notification without ShouldQueue to avoid double queuing
                    $notification = new DailyLogReportNotification($this->report);
                    $admin->notify($notification);
                    
                    $sentCount++;
                    Log::info("Successfully sent daily report to admin: {$admin->email}");
                    
                } catch (\Exception $e) {
                    $failedCount++;
                    Log::error("Failed to send daily report to admin {$admin->id}: " . $e->getMessage());
                }
            }

            Log::info("SendDailyLogReportJob completed. Sent: {$sentCount}, Failed: {$failedCount}");

        } catch (\Exception $e) {
            Log::error("SendDailyLogReportJob failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SendDailyLogReportJob failed permanently: " . $exception->getMessage());
        Log::error("Stack trace: " . $exception->getTraceAsString());
    }
}
