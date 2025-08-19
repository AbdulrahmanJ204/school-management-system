<?php

namespace App\Jobs;

use App\Services\LoggingService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateDailyLogReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ?Carbon $date;
    protected bool $forceRegenerate;

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
    public function __construct(?Carbon $date = null, bool $forceRegenerate = false)
    {
        $this->date = $date ?? Carbon::yesterday();
        $this->forceRegenerate = $forceRegenerate;
    }

    /**
     * Execute the job.
     */
    public function handle(LoggingService $loggingService): void
    {
        $dateString = $this->date->format('Y-m-d');
        Log::info("Starting GenerateDailyLogReportJob for date: {$dateString}");

        try {
            $report = $loggingService->generateDailyReport($this->date, $this->forceRegenerate);
            
            Log::info("Daily log report generated successfully for {$dateString}");
            Log::info("Report ID: {$report->id}, Total logs: {$report->total_logs}");
            
            if ($report->pdf_path) {
                Log::info("PDF report: {$report->pdf_path}");
            }
            
            if ($report->excel_path) {
                Log::info("Excel report: {$report->excel_path}");
            }

        } catch (\Exception $e) {
            Log::error("GenerateDailyLogReportJob failed for {$dateString}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $dateString = $this->date->format('Y-m-d');
        Log::error("GenerateDailyLogReportJob failed permanently for {$dateString}: " . $exception->getMessage());
        Log::error("Stack trace: " . $exception->getTraceAsString());
    }
}
