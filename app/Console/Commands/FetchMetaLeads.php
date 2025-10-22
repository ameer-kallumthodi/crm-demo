<?php

namespace App\Console\Commands;

use App\Services\FacebookApiService;
use App\Models\MetaLead;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchMetaLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meta:fetch-leads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch leads from Facebook Meta API';

    protected $facebookApiService;

    public function __construct(FacebookApiService $facebookApiService)
    {
        parent::__construct();
        $this->facebookApiService = $facebookApiService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to fetch Meta leads...');

        try {
            $result = $this->facebookApiService->fetchLeads();
            
            if (isset($result['error'])) {
                $this->error('Error: ' . $result['error']);
                if (isset($result['debug_info'])) {
                    $this->info('Debug Info:');
                    foreach ($result['debug_info'] as $key => $value) {
                        $this->line("  {$key}: {$value}");
                    }
                }
                return 1;
            }

            if (isset($result['leads']) && !empty($result['leads'])) {
                $inserted = MetaLead::insertLeads($result['leads']);
                if ($inserted) {
                    $this->info('Successfully inserted ' . count($result['leads']) . ' new leads');
                } else {
                    $this->warn('No new leads to insert (all leads already exist)');
                }
            } else {
                $this->info('No new leads found');
            }

            $this->info('Meta leads fetch completed successfully');
            return 0;

        } catch (\Exception $e) {
            $this->error('Error fetching Meta leads: ' . $e->getMessage());
            Log::error('Meta leads fetch error: ' . $e->getMessage());
            return 1;
        }
    }
}
