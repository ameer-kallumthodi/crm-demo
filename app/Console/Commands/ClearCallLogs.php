<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearCallLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'call:logs-clear {--force : Force clear without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all call API logs from the log file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $logPath = storage_path('logs/laravel.log');

        if (!File::exists($logPath)) {
            $this->info('Log file not found: ' . $logPath);
            return 0;
        }

        // Check if log file is too large (optional: warn if > 100MB)
        $fileSize = File::size($logPath);
        $fileSizeMB = round($fileSize / 1024 / 1024, 2);
        
        $this->info("Log file size: {$fileSizeMB} MB");

        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to clear all logs? This will truncate the entire log file.')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        // Truncate the log file
        File::put($logPath, '');

        $this->info('Log file cleared successfully.');
        return 0;
    }
}
