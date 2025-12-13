<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ViewCallLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'call:logs {--count=5 : Number of logs to display}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'View the last N call API logs (default: 5)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int) $this->option('count');
        $logPath = storage_path('logs/laravel.log');

        if (!File::exists($logPath)) {
            $this->error('Log file not found: ' . $logPath);
            return 1;
        }

        // Read the log file (read last 1MB to avoid memory issues with large files)
        $fileSize = File::size($logPath);
        $readSize = min($fileSize, 1024 * 1024); // Read last 1MB
        
        $handle = fopen($logPath, 'r');
        if ($fileSize > $readSize) {
            fseek($handle, -$readSize, SEEK_END);
            // Skip partial first line
            fgets($handle);
        }
        
        $content = stream_get_contents($handle);
        fclose($handle);
        
        // Split by lines and filter for Call API entries
        $lines = explode("\n", $content);
        $callLogEntries = [];
        $currentEntry = '';
        
        foreach ($lines as $line) {
            // Check if this line starts a new log entry (contains timestamp pattern like [2024-...])
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line)) {
                // Save previous entry if it contains Call API
                if (!empty($currentEntry) && stripos($currentEntry, 'Call API:') !== false) {
                    $callLogEntries[] = trim($currentEntry);
                }
                $currentEntry = $line;
            } else {
                // Continue building current entry
                $currentEntry .= "\n" . $line;
            }
        }
        
        // Don't forget the last entry
        if (!empty($currentEntry) && stripos($currentEntry, 'Call API:') !== false) {
            $callLogEntries[] = trim($currentEntry);
        }

        // Get the last N entries
        $lastLogs = array_slice($callLogEntries, -$count);

        if (empty($lastLogs)) {
            $this->info('No Call API logs found.');
            return 0;
        }

        $this->info("Last " . count($lastLogs) . " Call API logs:");
        $this->line(str_repeat('=', 100));

        foreach ($lastLogs as $index => $log) {
            $this->line(($index + 1) . '. ' . str_repeat('-', 98));
            $this->line($log);
            $this->line('');
        }

        return 0;
    }
}
