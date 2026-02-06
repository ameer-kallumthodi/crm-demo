<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelecallerNotificationService;

class CheckTelecallerAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telecaller:check-alerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and send telecaller tracking alerts and notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking telecaller alerts...');
        
        try {
            // Check and send all alerts
            TelecallerNotificationService::checkAndSendAlerts();
            
            $this->info('Telecaller alerts checked successfully.');
            
            // Send daily summary at 6 PM
            if (now()->hour == 18) {
                TelecallerNotificationService::sendDailySummary();
                $this->info('Daily summary sent.');
            }
            
        } catch (\Exception $e) {
            $this->error('Error checking telecaller alerts: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
