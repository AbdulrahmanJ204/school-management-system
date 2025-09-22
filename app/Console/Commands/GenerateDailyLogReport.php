<?php

namespace App\Console\Commands;

use App\Services\LoggingService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateDailyLogReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:generate-daily-report 
                            {--date= : The date to generate report for (Y-m-d format, defaults to yesterday)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate daily log report with PDF and Excel files';

    /**
     * Execute the console command.
     */
    public function handle(LoggingService $loggingService)
    {
        $dateOption = $this->option('date');
        
        if ($dateOption) {
            try {
                $date = Carbon::createFromFormat('Y-m-d', $dateOption);
            } catch (\Exception $e) {
                $this->error("Invalid date format. Please use Y-m-d format (e.g., 2025-01-15)");
                return 1;
            }
        } else {
            $date = Carbon::yesterday();
        }

        // Check if report already exists
        $existingReport = \App\Models\DailyLogReport::where('report_date', $date->format('Y-m-d'))->first();
        
        if ($existingReport) {
            $this->warn("⚠️  Report for {$date->format('Y-m-d')} already exists. Deleting old report and generating new one...");
        }

        $this->info("Generating daily log report for {$date->format('Y-m-d')}...");

        try {
            $report = $loggingService->generateDailyReport($date);
            
            $this->info("✅ Daily log report generated successfully!");
            $this->info("📊 Total logs: {$report->total_logs}");
            
            if ($report->pdf_path) {
                $this->info("📄 PDF report: {$report->pdf_path}");
            }
            
            if ($report->excel_path) {
                $this->info("📊 Excel report: {$report->excel_path}");
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Failed to generate daily log report: " . $e->getMessage());
            return 1;
        }
    }
}
