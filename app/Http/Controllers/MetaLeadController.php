<?php

namespace App\Http\Controllers;

use App\Models\MetaLead;
use App\Models\Lead;
use App\Models\User;
use App\Services\FacebookApiService;
use App\Helpers\PhoneNumberHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class MetaLeadController extends Controller
{
    protected $facebookApiService;

    public function __construct(FacebookApiService $facebookApiService)
    {
        $this->facebookApiService = $facebookApiService;
    }

    /**
     * Fetch leads from Facebook
     */
    public function fetchLeads(): JsonResponse
    {
        try {
            Log::info('Starting Meta leads fetch...');
            
            $result = $this->facebookApiService->fetchLeads();
            
            Log::info('Facebook API service result:', ['result' => $result]);
            
            if (isset($result['error'])) {
                Log::error('Facebook API error:', ['error' => $result['error']]);
                return response()->json($result, 401);
            }

            if (isset($result['leads']) && !empty($result['leads'])) {
                Log::info('Inserting leads to database:', ['count' => count($result['leads'])]);
                $inserted = MetaLead::insertLeads($result['leads']);
                Log::info('Leads insertion result:', ['inserted' => $inserted]);
            } else {
                Log::info('No leads to insert');
            }

            return response()->json([
                'message' => $result['message'],
                'count' => $result['count'] ?? 0
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching Meta leads: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Failed to fetch leads: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Push meta leads to main leads table
     */
    public function pushMetaLeads(): JsonResponse
    {
        try {
            Log::info('Push Meta Lead on: ' . now()->format('Y-m-d h:i A'));

            $lastLead = Lead::whereNotNull('meta_lead_id')
                ->orderBy('id', 'desc')
                ->first();
            
            $lastLeadId = $lastLead ? $lastLead->meta_lead_id : 0;

            $newLeads = MetaLead::where('id', '>', $lastLeadId)->get();
            
            if ($newLeads->isEmpty()) {
                return response()->json(['message' => 'No new leads found'], 200);
            }

            $currentDate = now()->format('Y-m-d');
            $insertedCount = 0;

            foreach ($newLeads as $lead) {
                Log::info("Processing lead: {$lead->full_name} (ID: {$lead->id}, Form: {$lead->form_no})");
                
                $phoneData = PhoneNumberHelper::get_phone_code($lead->phone_number);
                $phone = $phoneData['phone'];
                $code = $phoneData['code'];

                // Check if lead already exists by meta_lead_id
                $existingLead = Lead::where('meta_lead_id', $lead->id)->first();
                if ($existingLead) {
                    Log::info("Lead with meta_lead_id {$lead->id} already exists in leads table, skipping...");
                    continue;
                }

                // Get eligible telecallers (role_id = 2 for telecallers)
                $telecallers = User::where('role_id', 2)->get(['id']);

                if ($telecallers->isEmpty()) {
                    Log::warning('No telecallers found for lead assignment');
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
                    'course_id' => 17, // Specific course ID
                    'lead_status_id' => 1, // Initial status
                    'lead_source_id' => 6, // Meta lead source
                    'by_meta' => true,
                    'meta_lead_id' => $lead->id,
                    'remarks' => $remarks,
                    'created_by' => 1, // System user
                    'updated_by' => 1,
                ]);

                $insertedCount++;
                Log::info("Successfully inserted lead: {$lead->full_name} (ID: {$lead->id}) with course_id: 17, lead_status_id: 1, lead_source_id: 6");
            }

            return response()->json([
                'message' => $insertedCount . ' new leads inserted'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error pushing Meta leads: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to push leads: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test Facebook token
     */
    public function testToken(): JsonResponse
    {
        try {
            $result = $this->facebookApiService->testToken();
            return response()->json($result, isset($result['error']) ? 400 : 200);
        } catch (\Exception $e) {
            Log::error('Error testing token: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to test token: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test original Facebook token
     */
    public function testOriginalToken(): JsonResponse
    {
        try {
            $result = $this->facebookApiService->testOriginalToken();
            return response()->json($result, isset($result['error']) ? 400 : 200);
        } catch (\Exception $e) {
            Log::error('Error testing original token: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to test original token: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Try different token exchange methods
     */
    public function tryTokenExchange(): JsonResponse
    {
        try {
            $result = $this->facebookApiService->tryTokenExchange();
            return response()->json($result, isset($result['error']) ? 400 : 200);
        } catch (\Exception $e) {
            Log::error('Error trying token exchange: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to try token exchange: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Debug environment variables
     */
    public function debugEnv(): JsonResponse
    {
        try {
            $result = $this->facebookApiService->debugEnv();
            return response()->json($result, 200);
        } catch (\Exception $e) {
            Log::error('Error debugging environment: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to debug environment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get meta leads list
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = MetaLead::query();

            // Filter by form number
            if ($request->has('form_no')) {
                $query->where('form_no', $request->form_no);
            }

            // Filter by date range
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->dateRange($request->start_date, $request->end_date);
            }

            // Filter by phone number
            if ($request->has('phone')) {
                $query->where('phone_number', 'like', '%' . $request->phone . '%');
            }

            // Filter by email
            if ($request->has('email')) {
                $query->where('email', 'like', '%' . $request->email . '%');
            }

            // Filter by name
            if ($request->has('name')) {
                $query->where('full_name', 'like', '%' . $request->name . '%');
            }

            $perPage = $request->get('per_page', 15);
            $leads = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json($leads, 200);

        } catch (\Exception $e) {
            Log::error('Error fetching meta leads list: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch leads: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get meta lead details
     */
    public function show($id): JsonResponse
    {
        try {
            $lead = MetaLead::findOrFail($id);
            return response()->json($lead, 200);
        } catch (\Exception $e) {
            Log::error('Error fetching meta lead details: ' . $e->getMessage());
            return response()->json([
                'error' => 'Lead not found'
            ], 404);
        }
    }

    /**
     * Delete meta lead
     */
    public function destroy($id): JsonResponse
    {
        try {
            $lead = MetaLead::findOrFail($id);
            $lead->delete();

            return response()->json([
                'message' => 'Lead deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting meta lead: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to delete lead: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get meta leads statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = [
                'total_leads' => MetaLead::count(),
                'leads_today' => MetaLead::today()->count(),
                'leads_this_week' => MetaLead::where('created_at', '>=', now()->subWeek())->count(),
                'leads_this_month' => MetaLead::where('created_at', '>=', now()->subMonth())->count(),
                'leads_with_phone' => MetaLead::withPhone()->count(),
                'leads_with_email' => MetaLead::withEmail()->count(),
                'leads_by_form' => MetaLead::selectRaw('form_no, COUNT(*) as count')
                    ->groupBy('form_no')
                    ->get()
                    ->pluck('count', 'form_no'),
            ];

            return response()->json($stats, 200);
        } catch (\Exception $e) {
            Log::error('Error fetching meta leads statistics: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch statistics: ' . $e->getMessage()
            ], 500);
        }
    }
}
