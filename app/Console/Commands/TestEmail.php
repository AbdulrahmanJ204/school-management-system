<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    protected $signature = 'test:email {email}';
    protected $description = 'Send a test email to verify email configuration';

    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Sending test email to: {$email}");
        
        try {
            Mail::raw('This is a test email from your Laravel school application. Email system is working!', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email - Laravel School App');
            });
            
            $this->info('✅ Test email sent successfully!');
            $this->info('📧 Check storage/logs/laravel.log for email content (if using log driver)');
            
        } catch (\Exception $e) {
            $this->error('❌ Failed to send email: ' . $e->getMessage());
        }
    }
}
