<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Logging System Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the logging system.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Activity Logging
    |--------------------------------------------------------------------------
    |
    | Configure activity logging settings.
    |
    */
    'activity_logging' => [
        'enabled' => env('ACTIVITY_LOGGING_ENABLED', true),
        'log_creates' => env('LOG_CREATES', true),
        'log_updates' => env('LOG_UPDATES', true),
        'log_deletes' => env('LOG_DELETES', true),
        'exclude_tables' => [
            'activity_logs',
            'error_logs',
            'daily_log_reports',
            'migrations',
            'failed_jobs',
            'personal_access_tokens',
            'password_reset_tokens',
        ],
        'exclude_columns' => [
            'updated_at',
            'created_at',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Error Logging
    |--------------------------------------------------------------------------
    |
    | Configure error logging settings.
    |
    */
    'error_logging' => [
        'enabled' => env('ERROR_LOGGING_ENABLED', true),
        'log_4xx_errors' => env('LOG_4XX_ERRORS', true),
        'log_5xx_errors' => env('LOG_5XX_ERRORS', true),
        'exclude_codes' => [
            404, // Not Found
        ],
        'max_trace_length' => env('MAX_TRACE_LENGTH', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Daily Reports
    |--------------------------------------------------------------------------
    |
    | Configure daily report generation settings.
    |
    */
    'daily_reports' => [
        'enabled' => env('DAILY_REPORTS_ENABLED', true),
        'generate_pdf' => env('GENERATE_PDF_REPORTS', true),
        'generate_excel' => env('GENERATE_EXCEL_REPORTS', true),
        'storage_path' => env('REPORTS_STORAGE_PATH', 'reports/logs'),
        'retention_days' => env('REPORTS_RETENTION_DAYS', 365),
        'auto_generate_time' => env('AUTO_GENERATE_TIME', '01:00'), // Daily at 1 AM
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Retention
    |--------------------------------------------------------------------------
    |
    | Configure log retention settings.
    |
    */
    'retention' => [
        'activity_logs_days' => env('ACTIVITY_LOGS_RETENTION_DAYS', 90),
        'error_logs_days' => env('ERROR_LOGS_RETENTION_DAYS', 90),
        'auto_cleanup' => env('AUTO_CLEANUP_ENABLED', true),
        'cleanup_schedule' => env('CLEANUP_SCHEDULE', 'weekly'), // daily, weekly, monthly
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance
    |--------------------------------------------------------------------------
    |
    | Configure performance-related settings.
    |
    */
    'performance' => [
        'batch_size' => env('LOG_BATCH_SIZE', 100),
        'queue_logging' => env('QUEUE_LOGGING', false),
        'queue_name' => env('LOGGING_QUEUE_NAME', 'default'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security
    |--------------------------------------------------------------------------
    |
    | Configure security-related settings.
    |
    */
    'security' => [
        'mask_sensitive_data' => env('MASK_SENSITIVE_DATA', true),
        'sensitive_fields' => [
            'password',
            'password_confirmation',
            'token',
            'api_key',
            'secret',
            'credit_card',
        ],
        'log_ip_addresses' => env('LOG_IP_ADDRESSES', true),
        'log_user_agents' => env('LOG_USER_AGENTS', true),
    ],
];
