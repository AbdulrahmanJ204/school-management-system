<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\DailyLogReport;
use App\Notifications\DailyLogReportNotification;

class TestEmail2 extends Command
{
    protected $signature = 'test:email2';
    protected $description = 'Send a test email to verify email configuration';

    public function handle()
    {

        // Get all users with admin role and owner permission
        $adminUsers = User::where('user_type','admin')
        ->whereHas('roles', function ($query) {
            $query->where('name', 'owner');
        })->get();

        $this->info("Found {$adminUsers->count()} admin users with owner permission");

        $sentCount = 0;

        $report = DailyLogReport::first();

        foreach ($adminUsers as $admin) {
            try {
                $this->info("Sending daily report to admin: {$admin->email}");
                $admin->notify(new DailyLogReportNotification($report));
                $sentCount++;
                $this->info("Successfully sent daily report to admin: {$admin->email}");
            } catch (\Exception $e) {
                $this->error("Failed to send daily report to admin {$admin->id}: " . $e->getMessage());
            }
        }

        $this->info("Total emails sent: {$sentCount}");
    }
}
