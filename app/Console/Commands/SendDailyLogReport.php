<?php

namespace App\Console\Commands;

use App\Services\LoggingService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendDailyLogReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:send-daily-report 
                            {--date= : The date to generate report for (Y-m-d format, defaults to yesterday)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate daily log report and send via email to administrators with owner role';

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

        $this->info("Generating and sending daily log report for {$date->format('Y-m-d')}...");

        try {
            $result = $loggingService->generateAndSendDailyReport($date);
            
            if ($result['success']) {
                $this->info("✅ Daily log report generated and sent successfully!");
                $this->info("📊 Total logs: {$result['report']->total_logs}");
                $this->info("📧 Sent to {$result['sent_count']} administrators");
                
                if ($result['report']->pdf_path) {
                    $this->info("📄 PDF report: {$result['report']->pdf_path}");
                }
                
                if ($result['report']->excel_path) {
                    $this->info("📊 Excel report: {$result['report']->excel_path}");
                }
                
                return 0;
            } else {
                $this->error("❌ Failed to generate and send daily report: " . $result['error']);
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("❌ Failed to generate and send daily log report: " . $e->getMessage());
            return 1;
        }
    }
}
