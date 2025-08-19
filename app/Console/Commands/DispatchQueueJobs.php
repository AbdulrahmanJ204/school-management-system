<?php

namespace App\Console\Commands;

use App\Jobs\CleanOldLogsJob;
use App\Jobs\GenerateDailyLogReportJob;
use App\Jobs\SendDailyLogReportJob;
use App\Models\DailyLogReport;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DispatchQueueJobs extends Command
{
    protected $signature = 'queue:dispatch 
                            {job : The job to dispatch (generate-report, send-report, clean-logs)}
                            {--date= : Date for report jobs (Y-m-d format)}
                            {--days=90 : Days for clean-logs job}';

    protected $description = 'Dispatch queue jobs for testing and manual execution';

    public function handle()
    {
        $jobType = $this->argument('job');
        
        $this->info("🚀 Dispatching {$jobType} job...");

        try {
            switch ($jobType) {
                case 'generate-report':
                    $this->dispatchGenerateReportJob();
                    break;
                    
                case 'send-report':
                    $this->dispatchSendReportJob();
                    break;
                    
                case 'clean-logs':
                    $this->dispatchCleanLogsJob();
                    break;
                    
                default:
                    $this->error("Unknown job type: {$jobType}");
                    $this->info("Available jobs: generate-report, send-report, clean-logs");
                    return 1;
            }
            
            $this->info("✅ Job dispatched successfully!");
            $this->info("💡 Run 'php artisan queue:work' to process the job");
            
        } catch (\Exception $e) {
            $this->error("❌ Failed to dispatch job: " . $e->getMessage());
            return 1;
        }
    }

    private function dispatchGenerateReportJob()
    {
        $dateOption = $this->option('date');
        
        if ($dateOption) {
            $date = Carbon::createFromFormat('Y-m-d', $dateOption);
            $this->info("Generating report for date: {$date->format('Y-m-d')}");
        } else {
            $date = Carbon::yesterday();
            $this->info("Generating report for yesterday: {$date->format('Y-m-d')}");
        }
        
        GenerateDailyLogReportJob::dispatch($date);
    }

    private function dispatchSendReportJob()
    {
        $dateOption = $this->option('date');
        
        if ($dateOption) {
            $date = Carbon::createFromFormat('Y-m-d', $dateOption);
            $report = DailyLogReport::where('report_date', $date->format('Y-m-d'))->first();
            
            if (!$report) {
                $this->error("No report found for date: {$dateOption}");
                $this->info("Generate a report first using: php artisan queue:dispatch generate-report --date={$dateOption}");
                return;
            }
        } else {
            $report = DailyLogReport::latest('report_date')->first();
            
            if (!$report) {
                $this->error("No reports found in database");
                $this->info("Generate a report first using: php artisan queue:dispatch generate-report");
                return;
            }
        }
        
        $this->info("Sending report for date: {$report->report_date->format('Y-m-d')}");
        SendDailyLogReportJob::dispatch($report);
    }

    private function dispatchCleanLogsJob()
    {
        $days = (int) $this->option('days');
        $this->info("Cleaning logs older than {$days} days");
        
        CleanOldLogsJob::dispatch($days);
    }
}
