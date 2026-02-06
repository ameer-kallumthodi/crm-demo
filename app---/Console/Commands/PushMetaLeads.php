<?php

namespace App\Console\Commands;

use App\Models\MetaLead;
use App\Models\Lead;
use App\Models\User;
use App\Helpers\PhoneNumberHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PushMetaLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meta:push-leads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push Meta leads to main leads table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to push Meta leads to main leads table...');

        try {
            Log::info('Push Meta Lead on: ' . now()->format('Y-m-d h:i A'));

            $lastLead = Lead::whereNotNull('meta_lead_id')
                ->orderBy('id', 'desc')
                ->first();
            
            $lastLeadId = $lastLead ? $lastLead->meta_lead_id : 0;

            $newLeads = MetaLead::where('id', '>', $lastLeadId)->get();
            
            if ($newLeads->isEmpty()) {
                $this->info('No new leads found');
                return 0;
            }

            $this->info('Found ' . $newLeads->count() . ' new leads to process');

            $currentDate = now()->format('Y-m-d');
            $insertedCount = 0;

            foreach ($newLeads as $lead) {
                $phoneData = PhoneNumberHelper::get_phone_code($lead->phone_number);
                $phone = $phoneData['phone'];
                $code = $phoneData['code'];

                // Check if lead already exists
                $existingLead = Lead::where('meta_lead_id', $lead->id)->first();
                if ($existingLead) {
                    $this->line("Lead {$lead->id} already exists, skipping...");
                    continue;
                }

                // Get eligible telecallers (role_id = 2 for telecallers)
                $telecallers = User::where('role_id', 2)->get(['id']);

                if ($telecallers->isEmpty()) {
                    $this->warn('No telecallers found for lead assignment');
                    continue;
                }

                // Get today's lead count per telecaller
                $telecallerLeadCounts = [];
                foreach ($telecallers as $telecaller) {
                    $leadCount = Lead::where('telecaller_id', $telecaller->id)
                        ->whereDate('created_at', $currentDate)
                        ->count();
                    $telecallerLeadCounts[$telecaller->id] = $leadCount;
                }

                // Sort telecallers by lead count (ascending order)
                asort($telecallerLeadCounts);
                $educationManagerId = array_key_first($telecallerLeadCounts);

                // Generate remarks from other details
                $remarks = $lead->generateRemarks();

                // Insert the new lead
                Lead::create([
                    'title' => $lead->full_name,
                    'email' => $lead->email,
                    'code' => $code,
                    'phone' => $phone,
                    'telecaller_id' => $educationManagerId,
                    'lead_status_id' => 1, // Assuming 1 is the initial status
                    'lead_source_id' => 7, // Assuming 7 is Meta lead source
                    'by_meta' => true,
                    'meta_lead_id' => $lead->id,
                    'remarks' => $remarks,
                    'created_by' => 1, // System user
                    'updated_by' => 1,
                ]);

                $insertedCount++;
                $this->line("Inserted lead: {$lead->full_name} (ID: {$lead->id})");
            }

            $this->info("Successfully inserted {$insertedCount} new leads");
            Log::info("Push Meta Lead completed: {$insertedCount} new leads inserted");
            
            return 0;

        } catch (\Exception $e) {
            $this->error('Error pushing Meta leads: ' . $e->getMessage());
            Log::error('Error pushing Meta leads: ' . $e->getMessage());
            return 1;
        }
    }
}
