# Logging System Documentation

## Overview

The School Management System now includes a comprehensive logging system that tracks user activities, errors, and generates automatic daily reports in PDF and Excel formats.

## Features

### 1. Activity Logging
- Tracks all CRUD operations (Create, Read, Update, Delete)
- Records user information, IP address, and user agent
- Stores changes made to records
- Supports filtering by user type, action, table, and date range

### 2. Error Logging
- Automatically logs all exceptions and errors
- Records error details including stack trace, file, line number
- Tracks HTTP method, URL, and input data
- Supports filtering by error code, file, and date range

### 3. Daily Reports
- Automatic generation of daily log reports
- PDF and Excel format support
- Summary statistics and detailed logs
- Configurable retention periods

### 4. Maintenance Features
- Automatic cleanup of old logs
- Configurable retention periods
- Performance optimization options

## Database Schema

### Activity Logs Table
```sql
CREATE TABLE activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    user_type ENUM('student', 'teacher', 'admin') NULL,
    action VARCHAR(50) NOT NULL,
    table_name VARCHAR(100) NULL,
    record_id BIGINT UNSIGNED NULL,
    changes JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_user_type (user_id, user_type),
    INDEX idx_table_record (table_name, record_id),
    INDEX idx_created_at (created_at)
);
```

### Error Logs Table
```sql
CREATE TABLE error_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    code VARCHAR(255) NULL,
    file VARCHAR(255) NULL,
    line INT NULL,
    message TEXT NOT NULL,
    trace TEXT NULL,
    url VARCHAR(255) NULL,
    method VARCHAR(10) NULL,
    input JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_code (code),
    INDEX idx_created_at (created_at)
);
```

### Daily Log Reports Table
```sql
CREATE TABLE daily_log_reports (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    report_date DATE NOT NULL,
    total_logs INT DEFAULT 0,
    pdf_path VARCHAR(255) NULL,
    excel_path VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    UNIQUE KEY unique_report_date (report_date),
    INDEX idx_created_at (created_at)
);
```

## Installation

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Install Dependencies
The system requires the following packages for PDF and Excel generation:
```bash
composer require barryvdh/laravel-dompdf
composer require phpoffice/phpspreadsheet
```

### 3. Publish Configuration
```bash
php artisan vendor:publish --tag=logging-system-config
```

### 4. Seed Sample Data (Optional)
```bash
php artisan db:seed --class=LoggingSystemSeeder
```

## Usage

### 1. Automatic Activity Logging

To enable automatic activity logging for a model, use the `LogsActivity` trait:

```php
use App\Traits\LogsActivity;

class User extends Model
{
    use LogsActivity;
    
    // ... rest of your model
}
```

This will automatically log:
- `created` events when a new record is created
- `updated` events when a record is updated (including changes)
- `deleted` events when a record is deleted

### 2. Manual Activity Logging

You can manually log activities using the LoggingService:

```php
use App\Services\LoggingService;

class UserController extends Controller
{
    protected $loggingService;

    public function __construct(LoggingService $loggingService)
    {
        $this->loggingService = $loggingService;
    }

    public function store(Request $request)
    {
        $user = User::create($request->validated());
        
        // Log the activity
        $this->loggingService->logActivity(
            action: 'user_created',
            tableName: 'users',
            recordId: $user->id,
            changes: $request->all()
        );

        return response()->json($user);
    }
}
```

### 3. Error Logging

Error logging is handled automatically by the `LogErrors` middleware. Add it to your middleware stack:

```php
// In bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->append(\App\Http\Middleware\LogErrors::class);
})
```

### 4. Generating Daily Reports

#### Manual Generation
```bash
# Generate report for yesterday
php artisan logs:generate-daily-report

# Generate report for specific date
php artisan logs:generate-daily-report --date=2025-01-15
```

#### Automatic Generation
Set up a cron job to run daily:
```bash
# Add to crontab
0 1 * * * cd /path/to/your/project && php artisan logs:schedule-daily-report
```

### 5. Cleaning Old Logs
```bash
# Clean logs older than 90 days (default)
php artisan logs:clean

# Clean logs older than specific days
php artisan logs:clean --days=30
```

## API Endpoints

### Daily Reports
- `GET /api/daily-reports` - Get daily reports
- `POST /api/daily-reports/generate` - Generate daily report for specific date
- `GET /api/daily-reports/{id}/pdf` - Download PDF report
- `GET /api/daily-reports/{id}/excel` - Download Excel report

### Filters
- `GET /api/log-filters/user-types` - Get available user types
- `GET /api/log-filters/table-names` - Get available table names

### Maintenance
- `POST /api/logs/clean` - Clean old logs

## Configuration

### Environment Variables
```env
# Activity Logging
ACTIVITY_LOGGING_ENABLED=true
LOG_CREATES=true
LOG_UPDATES=true
LOG_DELETES=true

# Error Logging
ERROR_LOGGING_ENABLED=true
LOG_4XX_ERRORS=true
LOG_5XX_ERRORS=true
MAX_TRACE_LENGTH=1000

# Daily Reports
DAILY_REPORTS_ENABLED=true
GENERATE_PDF_REPORTS=true
GENERATE_EXCEL_REPORTS=true
REPORTS_STORAGE_PATH=reports/logs
REPORTS_RETENTION_DAYS=365
AUTO_GENERATE_TIME=01:00

# Log Retention
ACTIVITY_LOGS_RETENTION_DAYS=90
ERROR_LOGS_RETENTION_DAYS=90
AUTO_CLEANUP_ENABLED=true
CLEANUP_SCHEDULE=weekly

# Performance
LOG_BATCH_SIZE=100
QUEUE_LOGGING=false
LOGGING_QUEUE_NAME=default

# Security
MASK_SENSITIVE_DATA=true
LOG_IP_ADDRESSES=true
LOG_USER_AGENTS=true
```

### Configuration File
The system uses `config/logging-system.php` for detailed configuration options.

## File Storage

### Report Storage
Daily reports are stored in the configured storage path (default: `storage/app/reports/logs/`):
- PDF files: `daily-log-report-YYYY-MM-DD.pdf`
- Excel files: `daily-log-report-YYYY-MM-DD.xlsx`

### Storage Structure
```
storage/app/reports/logs/
├── daily-log-report-2025-01-15.pdf
├── daily-log-report-2025-01-15.xlsx
├── daily-log-report-2025-01-16.pdf
└── daily-log-report-2025-01-16.xlsx
```

## Security Considerations

### Data Privacy
- Sensitive data is automatically masked in logs
- IP addresses and user agents can be disabled
- Configurable sensitive field list

### Access Control
- All API endpoints require authentication
- Consider implementing role-based access for log viewing
- Log files should be stored securely

### Performance
- Large log volumes can impact performance
- Use the cleanup commands regularly
- Consider using queues for high-volume logging

## Monitoring and Maintenance

### Regular Tasks
1. **Daily**: Generate daily reports
2. **Weekly**: Clean old logs
3. **Monthly**: Review log statistics and adjust retention policies

### Monitoring
- Monitor log table sizes
- Check for unusual error patterns
- Review user activity patterns

### Troubleshooting

#### Common Issues
1. **PDF Generation Fails**: Ensure DomPDF is properly installed
2. **Excel Generation Fails**: Check PhpSpreadsheet installation
3. **Storage Issues**: Verify storage permissions and disk space
4. **Performance Issues**: Consider enabling queue logging

#### Debug Commands
```bash
# Test report generation
php artisan logs:generate-daily-report --date=$(date -d yesterday +%Y-%m-%d)

# Check daily reports
php artisan tinker
>>> App\Models\DailyLogReport::all()
```

## Integration Examples

### Frontend Integration
```javascript
// Get daily reports
const response = await fetch('/api/daily-reports?start_date=2025-01-01&end_date=2025-01-31');
const reports = await response.json();

// Download daily report
window.open('/api/daily-reports/1/pdf', '_blank');
```

### Dashboard Integration
```javascript
// Get daily reports for dashboard
const reports = await fetch('/api/daily-reports?per_page=10');
const dailyReports = await reports.json();
```

## Best Practices

1. **Selective Logging**: Only log important activities to avoid performance issues
2. **Regular Cleanup**: Set up automated cleanup to prevent database bloat
3. **Security**: Implement proper access controls for log viewing
4. **Monitoring**: Set up alerts for unusual error patterns
5. **Backup**: Include logs in your backup strategy
6. **Compliance**: Ensure logging practices comply with data protection regulations

## Support

For issues or questions about the logging system:
1. Check the Laravel logs for errors
2. Verify configuration settings
3. Test with sample data using the seeder
4. Review the API documentation for endpoint usage
