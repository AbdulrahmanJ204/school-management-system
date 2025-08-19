# School Management System - Queue System Guide

## Overview

The school management system now includes a comprehensive job queue system that allows background processing of time-consuming tasks like report generation, email sending, and log cleanup.

## 🚀 Quick Start

### 1. Configure Queue Driver

Choose your preferred queue driver:

```bash
# For development/testing (jobs run immediately)
php artisan queue:configure sync

# For production with database (recommended)
php artisan queue:configure database

# For production with Redis (high performance)
php artisan queue:configure redis
```

### 2. Update .env File

Add the following to your `.env` file based on your chosen driver:

**For Database:**
```env
QUEUE_CONNECTION=database
DB_QUEUE_TABLE=jobs
DB_QUEUE=default
DB_QUEUE_RETRY_AFTER=90
```

**For Redis:**
```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_QUEUE=default
REDIS_QUEUE_RETRY_AFTER=90
```

### 3. Run Migrations

```bash
php artisan migrate
```

### 4. Start Queue Worker

```bash
# Process one job and stop
php artisan queue:work --once

# Process jobs continuously
php artisan queue:work

# Process jobs in background (daemon mode)
php artisan queue:work --daemon

# Process specific queue
php artisan queue:work --queue=high,default,low
```

## 📋 Available Commands

### Queue Management

```bash
# Start queue worker
php artisan queue:work

# Monitor queue status
php artisan queue:monitor

# Dispatch jobs manually
php artisan queue:dispatch generate-report --date=2025-08-22
php artisan queue:dispatch send-report --date=2025-08-22
php artisan queue:dispatch clean-logs --days=90

# Clear failed jobs
php artisan queue:flush

# Retry failed jobs
php artisan queue:retry all
```

### Queue Monitoring

```bash
# Real-time monitoring (refreshes every 5 seconds)
php artisan queue:monitor

# One-time status check
php artisan queue:monitor --refresh=0

# Custom refresh interval
php artisan queue:monitor --refresh=10
```

## 🔧 Job Classes

### 1. GenerateDailyLogReportJob

Generates PDF and Excel reports for a specific date.

**Usage:**
```php
use App\Jobs\GenerateDailyLogReportJob;
use Carbon\Carbon;

// Generate report for yesterday
GenerateDailyLogReportJob::dispatch();

// Generate report for specific date
GenerateDailyLogReportJob::dispatch(Carbon::parse('2025-08-22'));

// Force regenerate existing report
GenerateDailyLogReportJob::dispatch(Carbon::parse('2025-08-22'), true);
```

**Configuration:**
- Tries: 2 attempts
- Backoff: 30 seconds between retries
- Timeout: 5 minutes

### 2. SendDailyLogReportJob

Sends daily log reports to admin users with "owner" permission.

**Usage:**
```php
use App\Jobs\SendDailyLogReportJob;
use App\Models\DailyLogReport;

$report = DailyLogReport::find(1);
SendDailyLogReportJob::dispatch($report);
```

**Configuration:**
- Tries: 3 attempts
- Backoff: 60 seconds between retries
- Timeout: 2 minutes

### 3. CleanOldLogsJob

Cleans old activity and error logs.

**Usage:**
```php
use App\Jobs\CleanOldLogsJob;

// Clean logs older than 90 days (default)
CleanOldLogsJob::dispatch();

// Clean logs older than 30 days
CleanOldLogsJob::dispatch(30);
```

**Configuration:**
- Tries: 2 attempts
- Backoff: 30 seconds between retries
- Timeout: 10 minutes

## ⏰ Scheduled Jobs

The system automatically schedules the following jobs:

### Daily Reports (4:00 AM)
```php
// Generate daily report
$schedule->job(new GenerateDailyLogReportJob())
         ->name('generate-daily-log-report')
         ->dailyAt('04:00')
         ->withoutOverlapping();

// Send reports to admins
$schedule->call(function () {
    $report = DailyLogReport::latest('report_date')->first();
    if ($report) {
        SendDailyLogReportJob::dispatch($report);
    }
})->name('send-daily-log-report')
  ->dailyAt('04:05')
  ->withoutOverlapping();
```

### Weekly Cleanup (Sunday 2:00 AM)
```php
$schedule->job(new CleanOldLogsJob(90))
         ->name('clean-old-logs')
         ->weekly()
         ->sundays()
         ->at('02:00')
         ->withoutOverlapping();
```

## 📊 Queue Monitoring

### Real-time Statistics

The queue monitor displays:
- Pending jobs count
- Failed jobs count
- Recent jobs (last 5)
- Recent failed jobs (last 3)
- Job creation rate (last hour)

### Monitoring Commands

```bash
# Start monitoring
php artisan queue:monitor

# Check queue status
php artisan queue:monitor --refresh=0

# Monitor with custom refresh
php artisan queue:monitor --refresh=10
```

## 🔄 Queue Configuration

### Queue Priorities

The system supports multiple queue priorities:

- **high**: Urgent jobs (notifications, alerts)
- **default**: Regular jobs (reports, cleanup)
- **low**: Background jobs (archiving, analytics)

### Processing Commands

```bash
# Process all queues (high priority first)
php artisan queue:work

# Process specific queue
php artisan queue:work --queue=high

# Process multiple queues with priority
php artisan queue:work --queue=high,default,low
```

## 🛠️ Troubleshooting

### Common Issues

1. **Jobs not processing**
   ```bash
   # Check if queue worker is running
   php artisan queue:monitor
   
   # Start queue worker
   php artisan queue:work
   ```

2. **Failed jobs**
   ```bash
   # View failed jobs
   php artisan queue:failed
   
   # Retry failed jobs
   php artisan queue:retry all
   
   # Clear failed jobs
   php artisan queue:flush
   ```

3. **Queue connection issues**
   ```bash
   # Check current configuration
   php artisan config:show queue.default
   
   # Clear config cache
   php artisan config:clear
   php artisan config:cache
   ```

### Performance Optimization

1. **Database Queue**
   - Add indexes to jobs table
   - Monitor job table size
   - Regular cleanup of old jobs

2. **Redis Queue**
   - Monitor Redis memory usage
   - Configure Redis persistence
   - Set appropriate TTL for job data

3. **Queue Worker**
   - Use supervisor for production
   - Monitor worker memory usage
   - Restart workers periodically

## 🚀 Production Deployment

### Using Supervisor (Recommended)

Create `/etc/supervisor/conf.d/laravel-worker.conf`:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/project/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=8
redirect_stderr=true
stdout_logfile=/path/to/your/project/storage/logs/worker.log
stopwaitsecs=3600
```

### Using Systemd

Create `/etc/systemd/system/laravel-worker.service`:

```ini
[Unit]
Description=Laravel Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
Restart=on-failure
ExecStart=/usr/bin/php /path/to/your/project/artisan queue:work --sleep=3 --tries=3
StandardOutput=journal
StandardError=journal

[Install]
WantedBy=multi-user.target
```

## 📈 Monitoring and Alerts

### Queue Health Checks

```bash
# Check queue health
php artisan queue:monitor --refresh=0

# Expected output:
# Pending Jobs: 0
# Failed Jobs: 0
# Jobs Created (Last Hour): 5
```

### Alert Thresholds

Configure alerts in `.env`:

```env
QUEUE_MONITORING_ENABLED=true
QUEUE_ALERT_THRESHOLD=100
QUEUE_FAILED_JOB_THRESHOLD=10
```

## 🔧 Custom Jobs

### Creating New Jobs

```bash
php artisan make:job ProcessStudentAttendance
```

### Example Custom Job

```php
<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessStudentAttendance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = 30;

    public function __construct(public int $studentId)
    {
        //
    }

    public function handle(): void
    {
        // Process student attendance logic
    }

    public function failed(\Throwable $exception): void
    {
        // Handle job failure
    }
}
```

### Dispatching Custom Jobs

```php
// Dispatch immediately
ProcessStudentAttendance::dispatch($studentId);

// Dispatch with delay
ProcessStudentAttendance::dispatch($studentId)->delay(now()->addMinutes(5));

// Dispatch to specific queue
ProcessStudentAttendance::dispatch($studentId)->onQueue('high');
```

## 📚 Additional Resources

- [Laravel Queue Documentation](https://laravel.com/docs/queues)
- [Laravel Horizon (Redis Queue Dashboard)](https://laravel.com/docs/horizon)
- [Supervisor Documentation](http://supervisord.org/)
- [Systemd Documentation](https://systemd.io/)

---

**Note**: This queue system is designed to handle the school management system's background processing needs efficiently and reliably. Always test thoroughly in a development environment before deploying to production.
