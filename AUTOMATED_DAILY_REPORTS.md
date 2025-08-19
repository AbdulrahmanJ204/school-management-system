# Automated Daily Log Reports System

## Overview

This system automatically generates daily log reports at 4:00 AM every day and sends them via email to all administrators with the "owner" role. The reports include both PDF and Excel formats containing activity logs and error logs from the previous day.

## Features

### 🔄 **Automated Scheduling**
- **Daily execution**: Runs automatically at 4:00 AM every day
- **Background processing**: Runs in background to avoid blocking other operations
- **Overlap prevention**: Prevents multiple instances from running simultaneously
- **Error handling**: Logs failures and continues operation

### 📊 **Report Generation**
- **PDF Reports**: Professional formatted reports with activity and error logs
- **Excel Reports**: Detailed spreadsheets with multiple sheets (Activity, Error, Summary)
- **Comprehensive Data**: Includes changes, user agents, IP addresses, and timestamps
- **Conditional Display**: Shows changes/input data on separate lines when available

### 📧 **Email Distribution**
- **Targeted Recipients**: Sends only to administrators with "owner" role
- **File Attachments**: Automatically attaches both PDF and Excel reports
- **Professional Format**: Well-formatted email with clear subject and content
- **Error Handling**: Continues sending to other recipients if one fails

## System Components

### 1. **Notification Class**
**File**: `app/Notifications/DailyLogReportNotification.php`

- Creates professional email notifications
- Attaches PDF and Excel reports
- Handles email formatting and delivery

### 2. **Service Methods**
**File**: `app/Services/LoggingService.php`

#### `sendDailyReportToAdmins(DailyLogReport $report): int`
- Finds all users with "admin" role and "owner" permission
- Sends notifications to each administrator
- Returns count of successfully sent emails

#### `generateAndSendDailyReport(Carbon $date = null): array`
- Generates daily report for specified date
- Sends emails to administrators
- Returns comprehensive result with success status

### 3. **Artisan Commands**
**File**: `app/Console/Commands/SendDailyLogReport.php`

#### `logs:send-daily-report`
```bash
# Generate and send report for yesterday (default)
php artisan logs:send-daily-report

# Generate and send report for specific date
php artisan logs:send-daily-report --date=2025-08-18
```

### 4. **Scheduler Configuration**
**File**: `bootstrap/app.php`

```php
->withSchedule(function (Schedule $schedule) {
    // Daily log report generation and email sending at 4:00 AM
    $schedule->command('logs:send-daily-report')
             ->dailyAt('04:00')
             ->withoutOverlapping()
             ->runInBackground()
             ->onFailure(function () {
                 \Log::error('Failed to send daily log report');
             });
    
    // Clean old logs weekly on Sunday at 2:00 AM
    $schedule->command('logs:clean-old-logs --days=90')
             ->weekly()
             ->sundays()
             ->at('02:00')
             ->withoutOverlapping()
             ->runInBackground();
})
```

## Setup Instructions

### 1. **Create Administrator Users**
Run the seeder to create admin users with proper roles and permissions:

```bash
php artisan db:seed --class=AdminOwnerSeeder
```

This creates:
- **System Administrator**: admin@school.com
- **John Owner**: owner@school.com

Both users have:
- "admin" role
- "owner" permission
- Admin records in the database

### 2. **Configure Email Settings**
Update your `.env` file with email configuration:

```env
# For production (SMTP)
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host.com
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@school.com
MAIL_FROM_NAME="School Management System"

# For testing (logs emails to storage/logs/laravel.log)
MAIL_MAILER=log
```

### 3. **Set Up Cron Job**
Add this cron job to your server to run Laravel's scheduler:

```bash
# Add to crontab (crontab -e)
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

### 4. **Test the System**
Test the automated system manually:

```bash
# Test for yesterday's data
php artisan logs:send-daily-report

# Test for specific date
php artisan logs:send-daily-report --date=2025-08-18
```

## Email Format

### Subject Line
```
Daily Log Report - [Date]
```

### Email Content
```
Hello [Admin Name],

Here is your daily log report for [Date].
Total logs recorded: [Number]
This report contains all activity and error logs from the previous day.

Please review the attached reports for any unusual activity or errors that may require attention.
If you have any questions, please contact the system administrator.

Best regards,
School Management System
```

### Attachments
- **PDF Report**: `daily-log-report-[date].pdf`
- **Excel Report**: `daily-log-report-[date].xlsx`

## Report Contents

### PDF Report Features
- **Professional Layout**: Clean, organized design
- **Summary Section**: Total counts for activity and error logs
- **Activity Logs Table**: User actions, changes, IP addresses, user agents
- **Error Logs Table**: Error details, input data, request information
- **Conditional Display**: Changes/input shown on separate lines when available

### Excel Report Features
- **Multiple Sheets**: Activity Logs, Error Logs, Summary
- **Detailed Data**: All log fields including changes and user agents
- **Formatted Headers**: Clear column labels
- **Summary Statistics**: Report date and total counts

## Monitoring and Troubleshooting

### 1. **Check Scheduler Status**
```bash
# List scheduled tasks
php artisan schedule:list

# Run scheduler manually
php artisan schedule:run
```

### 2. **View Logs**
```bash
# Check Laravel logs for email delivery
tail -f storage/logs/laravel.log | grep -i "email\|mail\|notification"

# Check for scheduler errors
tail -f storage/logs/laravel.log | grep -i "schedule\|cron"
```

### 3. **Test Email Configuration**
```bash
# Test email sending
php artisan tinker
Mail::raw('Test email', function($message) { $message->to('test@example.com')->subject('Test'); });
```

### 4. **Verify Permissions**
```bash
# Check admin users and permissions
php artisan tinker
User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->whereHas('permissions', fn($q) => $q->where('name', 'owner'))->get(['id', 'email', 'first_name', 'last_name']);
```

## Security Considerations

### 1. **Role-Based Access**
- Only administrators with "owner" permission receive reports
- Uses Spatie Permission package for secure role management

### 2. **File Security**
- Reports stored in private storage directory
- Files accessible only through authenticated API endpoints

### 3. **Email Security**
- Uses Laravel's built-in email security features
- Supports encryption (TLS/SSL) for SMTP connections

## Customization Options

### 1. **Change Schedule Time**
Edit `bootstrap/app.php`:
```php
$schedule->command('logs:send-daily-report')
         ->dailyAt('06:00')  // Change to 6:00 AM
```

### 2. **Add More Recipients**
Modify `sendDailyReportToAdmins()` method in `LoggingService.php`:
```php
// Add additional roles or permissions
$adminUsers = User::whereHas('roles', function ($query) {
    $query->whereIn('name', ['admin', 'supervisor']);
})->get();
```

### 3. **Customize Email Content**
Edit `DailyLogReportNotification.php`:
```php
public function toMail(object $notifiable): MailMessage
{
    return (new MailMessage)
        ->subject('Custom Subject - ' . $this->report->report_date->format('F j, Y'))
        ->greeting('Hello ' . $notifiable->name . ',')
        ->line('Your custom message here.');
}
```

### 4. **Modify Report Content**
- **PDF Template**: Edit `resources/views/reports/daily-log-pdf.blade.php`
- **Excel Format**: Modify `generateExcelReport()` method in `LoggingService.php`

## Maintenance

### 1. **Regular Cleanup**
The system automatically cleans old logs weekly:
- Removes logs older than 90 days
- Runs every Sunday at 2:00 AM

### 2. **Storage Management**
Monitor storage usage:
```bash
# Check report storage size
du -sh storage/app/private/reports/logs/

# Clean old reports manually
php artisan logs:clean-old-logs --days=30
```

### 3. **Database Maintenance**
Regular database maintenance:
```bash
# Check log table sizes
php artisan tinker
echo "Activity Logs: " . App\Models\ActivityLog::count() . "\n";
echo "Error Logs: " . App\Models\ErrorLog::count() . "\n";
echo "Daily Reports: " . App\Models\DailyLogReport::count() . "\n";
```

## Troubleshooting Common Issues

### 1. **Emails Not Sending**
- Check email configuration in `.env`
- Verify SMTP credentials
- Check Laravel logs for email errors

### 2. **Scheduler Not Running**
- Verify cron job is active: `crontab -l`
- Check server timezone settings
- Test scheduler manually: `php artisan schedule:run`

### 3. **No Recipients Found**
- Verify admin users exist with proper roles
- Check Spatie Permission setup
- Run permission seeder: `php artisan db:seed --class=AdminOwnerSeeder`

### 4. **Report Generation Fails**
- Check storage permissions
- Verify PDF/Excel packages are installed
- Check Laravel logs for generation errors

## Support

For issues or questions:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Verify configuration settings
3. Test components individually
4. Review this documentation

The system is designed to be robust and self-maintaining, with comprehensive error handling and logging for easy troubleshooting.
