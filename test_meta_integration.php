<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\FacebookApiService;
use App\Models\MetaLead;

echo "Testing Meta Leads Integration...\n\n";

// Test 1: Check environment variables
echo "1. Checking environment variables:\n";
echo "FB_APP_ID: " . (config('services.facebook.app_id') ? 'SET' : 'NOT SET') . "\n";
echo "FB_APP_SECRET: " . (config('services.facebook.app_secret') ? 'SET' : 'NOT SET') . "\n";
echo "FB_ACCESS_TOKEN: " . (config('services.facebook.access_token') ? 'SET' : 'NOT SET') . "\n";
echo "FB_LEAD_FORM_ID: " . (config('services.facebook.lead_form_id') ? 'SET' : 'NOT SET') . "\n\n";

// Test 2: Test Facebook API Service
echo "2. Testing Facebook API Service:\n";
try {
    $facebookService = new FacebookApiService();
    $result = $facebookService->fetchLeads();
    
    echo "Facebook API Result:\n";
    print_r($result);
    
    if (isset($result['leads']) && !empty($result['leads'])) {
        echo "\n3. Testing lead insertion:\n";
        $inserted = MetaLead::insertLeads($result['leads']);
        echo "Insertion result: " . ($inserted ? 'SUCCESS' : 'FAILED') . "\n";
        
        echo "\n4. Checking database:\n";
        $totalLeads = MetaLead::count();
        echo "Total leads in database: $totalLeads\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\nTest completed.\n";
