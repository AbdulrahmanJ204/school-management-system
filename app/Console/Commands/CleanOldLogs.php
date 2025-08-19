<?php

namespace App\Console\Commands;

use App\Services\LoggingService;
use Illuminate\Console\Command;

class CleanOldLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:clean 
                            {--days=90 : Number of days to keep logs (default: 90)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean old activity and error logs';

    /**
     * Execute the console command.
     */
    public function handle(LoggingService $loggingService)
    {
        $days = (int) $this->option('days');
        
        if ($days < 1) {
            $this->error("Days must be at least 1");
            return 1;
        }

        $this->info("Cleaning logs older than {$days} days...");

        try {
            $deletedCount = $loggingService->cleanOldLogs($days);
            
            $this->info("✅ Successfully cleaned {$deletedCount} old log records");
            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Failed to clean old logs: " . $e->getMessage());
            return 1;
        }
    }
}
