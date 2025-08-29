<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ActivityLog;
use App\Models\ErrorLog;
use App\Models\DailyLogReport;
use Carbon\Carbon;

class LoggingSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding logging system...');

        // Create sample activity logs
        $this->createSampleActivityLogs();

        // Create sample error logs
        $this->createSampleErrorLogs();

        // Create sample daily reports
        $this->createSampleDailyReports();

        $this->command->info('Logging system seeded successfully!');
    }

    /**
     * Create sample activity logs.
     */
    private function createSampleActivityLogs(): void
    {
        $actions = ['created', 'updated', 'deleted', 'viewed', 'exported'];
        $tables = ['users', 'students', 'teachers', 'grades', 'subjects', 'assignments'];
        $userTypes = ['admin', 'teacher', 'student'];

        for ($i = 0; $i < 50; $i++) {
            ActivityLog::create([
                'user_id' => rand(1, 10),
                'user_type' => $userTypes[array_rand($userTypes)],
                'action' => $actions[array_rand($actions)],
                'table_name' => $tables[array_rand($tables)],
                'record_id' => rand(1, 100),
                'changes' => $this->getSampleChanges(),
                'ip_address' => $this->getRandomIp(),
                'user_agent' => $this->getRandomUserAgent(),
                'created_at' => Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
            ]);
        }
    }

    /**
     * Create sample error logs.
     */
    private function createSampleErrorLogs(): void
    {
        $errorCodes = [500, 404, 403, 422, 401];
        $files = [
            'app/Http/Controllers/UserController.php',
            'app/Http/Controllers/StudentController.php',
            'app/Services/AuthService.php',
            'app/Models/User.php',
        ];
        $methods = ['GET', 'POST', 'PUT', 'DELETE'];

        for ($i = 0; $i < 20; $i++) {
            ErrorLog::create([
                'user_id' => rand(1, 10),
                'code' => $errorCodes[array_rand($errorCodes)],
                'file' => $files[array_rand($files)],
                'line' => rand(1, 200),
                'message' => $this->getSampleErrorMessage(),
                'trace' => $this->getSampleStackTrace(),
                'url' => $this->getSampleUrl(),
                'method' => $methods[array_rand($methods)],
                'input' => $this->getSampleInput(),
                'ip_address' => $this->getRandomIp(),
                'user_agent' => $this->getRandomUserAgent(),
                'created_at' => Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
            ]);
        }
    }

    /**
     * Create sample daily reports.
     */
    private function createSampleDailyReports(): void
    {
        for ($i = 1; $i <= 7; $i++) {
            $date = Carbon::now()->subDays($i);

            DailyLogReport::create([
                'report_date' => $date->format('Y-m-d'),
                'total_logs' => rand(10, 100),
                'pdf_path' => "reports/logs/daily-log-report-{$date->format('Y-m-d')}.pdf",
                'excel_path' => "reports/logs/daily-log-report-{$date->format('Y-m-d')}.xlsx",
                'created_at' => $date->addHours(1),
            ]);
        }
    }

    /**
     * Get sample changes for activity logs.
     */
    private function getSampleChanges(): array
    {
        $changes = [
            'name' => ['old' => 'أحمد محمد', 'new' => 'أحمد علي'],
            'email' => ['old' => 'ahmed@example.com', 'new' => 'ahmed.ali@example.com'],
            'status' => ['old' => 'نشط', 'new' => 'غير نشط'],
            'grade' => ['old' => 'أ', 'new' => 'ب'],
        ];

        return array_rand($changes);
    }

    /**
     * Get sample error message.
     */
    private function getSampleErrorMessage(): string
    {
        $messages = [
            'المستخدم غير موجود',
            'بيانات الاعتماد غير صحيحة',
            'فشل الاتصال بقاعدة البيانات',
            'فشل رفع الملف',
            'تم رفض الإذن',
            'فشل التحقق من صحة البيانات',
        ];

        return $messages[array_rand($messages)];
    }

    /**
     * Get sample stack trace.
     */
    private function getSampleStackTrace(): string
    {
        return "#0 /var/www/html/app/Http/Controllers/UserController.php(45): User::find(123)\n" .
               "#1 /var/www/html/vendor/laravel/framework/src/Illuminate/Routing/Controller.php(54): UserController->show(123)\n" .
               "#2 /var/www/html/vendor/laravel/framework/src/Illuminate/Routing/ControllerDispatcher.php(45): call_user_func_array(Array, Array)";
    }

    /**
     * Get sample URL.
     */
    private function getSampleUrl(): string
    {
        $urls = [
            '/api/users/123',
            '/api/students/456',
            '/api/teachers/789',
            '/api/grades',
            '/api/assignments',
        ];

        return $urls[array_rand($urls)];
    }

    /**
     * Get sample input data.
     */
    private function getSampleInput(): array
    {
        return [
            'name' => 'أحمد محمد',
            'email' => 'ahmed@example.com',
            'password' => '********',
            'role' => 'student',
        ];
    }

    /**
     * Get random IP address.
     */
    private function getRandomIp(): string
    {
        $ips = [
            '192.168.1.100',
            '10.0.0.50',
            '172.16.0.25',
            '127.0.0.1',
            '203.0.113.45',
        ];

        return $ips[array_rand($ips)];
    }

    /**
     * Get random user agent.
     */
    private function getRandomUserAgent(): string
    {
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1',
        ];

        return $userAgents[array_rand($userAgents)];
    }
}
