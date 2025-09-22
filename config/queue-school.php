<?php

return [

    /*
    |--------------------------------------------------------------------------
    | School Management System Queue Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration file contains queue settings specifically optimized
    | for the school management system's background job processing.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Default Queue Connection
    |--------------------------------------------------------------------------
    |
    | The default queue connection for the school system. We recommend using
    | 'database' for development and 'redis' for production.
    |
    */

    'default' => env('QUEUE_CONNECTION', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection options for every queue backend
    | used by your school management system.
    |
    */

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver' => 'database',
            'connection' => env('DB_QUEUE_CONNECTION'),
            'table' => env('DB_QUEUE_TABLE', 'jobs'),
            'queue' => env('DB_QUEUE', 'default'),
            'retry_after' => (int) env('DB_QUEUE_RETRY_AFTER', 90),
            'after_commit' => false,
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => env('REDIS_QUEUE_CONNECTION', 'default'),
            'queue' => env('REDIS_QUEUE', 'default'),
            'retry_after' => (int) env('REDIS_QUEUE_RETRY_AFTER', 90),
            'block_for' => null,
            'after_commit' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Names
    |--------------------------------------------------------------------------
    |
    | Define specific queue names for different types of jobs in the school system.
    |
    */

    'queues' => [
        'high' => 'high',           // Urgent jobs (notifications, alerts)
        'default' => 'default',     // Regular jobs (reports, cleanup)
        'low' => 'low',             // Background jobs (archiving, analytics)
    ],

    /*
    |--------------------------------------------------------------------------
    | Job Timeouts
    |--------------------------------------------------------------------------
    |
    | Maximum execution time for different types of jobs.
    |
    */

    'timeouts' => [
        'report_generation' => 300,    // 5 minutes for report generation
        'email_sending' => 120,        // 2 minutes for email sending
        'log_cleanup' => 600,          // 10 minutes for log cleanup
        'default' => 60,               // 1 minute default
    ],

    /*
    |--------------------------------------------------------------------------
    | Retry Configuration
    |--------------------------------------------------------------------------
    |
    | Retry settings for failed jobs.
    |
    */

    'retry' => [
        'max_attempts' => 3,
        'backoff_seconds' => 60,
        'exponential_backoff' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring
    |--------------------------------------------------------------------------
    |
    | Queue monitoring and alerting settings.
    |
    */

    'monitoring' => [
        'enabled' => env('QUEUE_MONITORING_ENABLED', true),
        'alert_threshold' => env('QUEUE_ALERT_THRESHOLD', 100), // Alert if more than 100 pending jobs
        'failed_job_threshold' => env('QUEUE_FAILED_JOB_THRESHOLD', 10), // Alert if more than 10 failed jobs
    ],

];
