<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

class QueueMonitor extends Command
{
    protected $signature = 'queue:monitor {--refresh=5 : Refresh interval in seconds}';
    protected $description = 'Monitor queue status and statistics';

    public function handle()
    {
        $refreshInterval = $this->option('refresh');
        
        $this->info('📊 School Queue Monitor');
        $this->info('Press Ctrl+C to stop monitoring');
        $this->info("Refreshing every {$refreshInterval} seconds...\n");

        while (true) {
            $this->displayQueueStatus();
            
            if ($refreshInterval > 0) {
                sleep($refreshInterval);
                $this->output->write("\033[2J\033[1;1H"); // Clear screen
            } else {
                break;
            }
        }
    }

    private function displayQueueStatus()
    {
        $this->info('🕐 ' . now()->format('Y-m-d H:i:s'));
        $this->info(str_repeat('─', 50));

        // Queue Statistics
        $this->displayQueueStats();
        
        // Recent Jobs
        $this->displayRecentJobs();
        
        // Failed Jobs
        $this->displayFailedJobs();
        
        $this->info(str_repeat('─', 50));
    }

    private function displayQueueStats()
    {
        $this->info('📈 Queue Statistics:');
        
        // Pending jobs
        $pendingJobs = DB::table('jobs')->count();
        $this->info("   Pending Jobs: {$pendingJobs}");
        
        // Failed jobs
        $failedJobs = DB::table('failed_jobs')->count();
        $this->info("   Failed Jobs: {$failedJobs}");
        
        // Recent jobs (last hour)
        $recentJobs = DB::table('jobs')
            ->where('created_at', '>=', now()->subHour())
            ->count();
        $this->info("   Jobs Created (Last Hour): {$recentJobs}");
        
        $this->info('');
    }

    private function displayRecentJobs()
    {
        $this->info('🔄 Recent Jobs:');
        
        $recentJobs = DB::table('jobs')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'queue', 'created_at', 'payload']);
        
        if ($recentJobs->isEmpty()) {
            $this->info('   No recent jobs');
        } else {
            foreach ($recentJobs as $job) {
                $payload = json_decode($job->payload, true);
                $jobClass = $payload['displayName'] ?? 'Unknown';
                $createdTime = \Carbon\Carbon::parse($job->created_at)->diffForHumans();
                
                $this->info("   ID: {$job->id} | Queue: {$job->queue} | Job: {$jobClass} | Created: {$createdTime}");
            }
        }
        
        $this->info('');
    }

    private function displayFailedJobs()
    {
        $this->info('❌ Recent Failed Jobs:');
        
        $failedJobs = DB::table('failed_jobs')
            ->orderBy('failed_at', 'desc')
            ->limit(3)
            ->get(['id', 'queue', 'failed_at', 'exception']);
        
        if ($failedJobs->isEmpty()) {
            $this->info('   No failed jobs');
        } else {
            foreach ($failedJobs as $job) {
                $failedTime = \Carbon\Carbon::parse($job->failed_at)->diffForHumans();
                $exception = substr($job->exception, 0, 100) . '...';
                
                $this->info("   ID: {$job->id} | Queue: {$job->queue} | Failed: {$failedTime}");
                $this->info("   Error: {$exception}");
                $this->info('');
            }
        }
    }
}
