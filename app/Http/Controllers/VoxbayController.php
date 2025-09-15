<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\Lead;
use App\Helpers\AuthHelper;
use Illuminate\Support\Facades\Log;

class VoxbayController extends Controller
{
    /**
     * Handle outgoing call events
     * POST /api/voxbay/outgoing-call
     */
    public function outgoingCall(Request $request): JsonResponse
    {
        try {
            $data = $request->json()->all();

            if (!isset($data['phoneNumber'], $data['telecaller_id'])) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Missing required fields: phoneNumber and telecaller_id'
                ], 400);
            }

            $telecallerId = $data['telecaller_id'];
            $leadId = $data['lead_id'] ?? null;

            // Get telecaller information
            $telecaller = User::find($telecallerId);
            if (!$telecaller || empty($telecaller->ext_no)) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Extension not found for telecaller'
                ], 400);
            }

            // Get lead information if lead_id is provided
            $lead = null;
            if ($leadId) {
                $lead = Lead::find($leadId);
                if (!$lead) {
                    return response()->json([
                        'status' => 'error', 
                        'message' => 'Lead not found'
                    ], 400);
                }
            }

            // Prepare call parameters
            $extension = $telecaller->ext_no;
            $phone = $data['phoneNumber'];
            $countryCode = $data['country_code'] ?? $lead?->code ?? '91';
            $destination = $countryCode . $phone;

            // Get Voxbay configuration from environment
            $uidNumber = env('VOXBAY_UID_NUMBER');
            $upin = env('VOXBAY_UPIN');

            if (!$uidNumber || !$upin) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Voxbay configuration not found'
                ], 500);
            }

            // Build Voxbay API URL
            $url = "https://x.voxbay.com/api/click_to_call?id_dept=0&uid={$uidNumber}&upin={$upin}&user_no={$extension}&destination={$destination}";

            // Make the API call
            $response = file_get_contents($url);

            // Log the call attempt
            Log::info('Voxbay Outgoing Call', [
                'telecaller_id' => $telecallerId,
                'lead_id' => $leadId,
                'destination' => $destination,
                'url' => $url,
                'response' => $response
            ]);

            return response()->json([
                'status' => 'success', 
                'url_called' => $url,
                'destination' => $destination,
                'telecaller' => $telecaller->name,
                'lead' => $lead ? $lead->title : null
            ], 200);

        } catch (\Exception $e) {
            Log::error('Voxbay Outgoing Call Error: ' . $e->getMessage(), [
                'request_data' => $request->json()->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error', 
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get telecaller extension for a user
     * GET /api/voxbay/telecaller/{id}/extension
     */
    public function getTelecallerExtension($id): JsonResponse
    {
        try {
            $telecaller = User::find($id);
            
            if (!$telecaller) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Telecaller not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'extension' => $telecaller->ext_no,
                'name' => $telecaller->name,
                'phone' => $telecaller->phone,
                'code' => $telecaller->code
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get Telecaller Extension Error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error', 
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Test Voxbay connection
     * GET /api/voxbay/test-connection
     */
    public function testConnection(): JsonResponse
    {
        try {
            $uidNumber = env('VOXBAY_UID_NUMBER');
            $upin = env('VOXBAY_UPIN');

            if (!$uidNumber || !$upin) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Voxbay configuration not found'
                ], 500);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Voxbay configuration is valid',
                'uid_number' => $uidNumber,
                'upin_configured' => !empty($upin)
            ], 200);

        } catch (\Exception $e) {
            Log::error('Voxbay Test Connection Error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error', 
                'message' => 'Internal server error'
            ], 500);
        }
    }
}
