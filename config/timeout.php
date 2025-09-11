<?php

/**
 * Timeout Configuration
 * 
 * This file contains timeout-related configurations to prevent
 * execution time exceeded errors.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Maximum Execution Time
    |--------------------------------------------------------------------------
    |
    | Set the maximum execution time for PHP scripts in seconds.
    | 0 means no time limit.
    |
    */
    'max_execution_time' => env('MAX_EXECUTION_TIME', 300), // 5 minutes

    /*
    |--------------------------------------------------------------------------
    | Memory Limit
    |--------------------------------------------------------------------------
    |
    | Set the maximum amount of memory that a script may consume.
    |
    */
    'memory_limit' => env('MEMORY_LIMIT', '256M'),

    /*
    |--------------------------------------------------------------------------
    | Bulk Upload Limits
    |--------------------------------------------------------------------------
    |
    | Limits for bulk operations to prevent timeouts.
    |
    */
    'bulk_upload' => [
        'max_rows' => env('BULK_UPLOAD_MAX_ROWS', 1000),
        'batch_size' => env('BULK_UPLOAD_BATCH_SIZE', 100),
    ],
];
