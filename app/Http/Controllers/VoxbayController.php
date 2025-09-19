<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\Lead;
use App\Models\VoxbayCallLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class VoxbayController extends Controller
{
    /**
     * Handle outgoing call events
     * POST /api/voxbay/outgoing-call
     */
    public function outgoingCall(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'telecaller_id' => 'required|integer|exists:users,id',
                'lead_id' => 'required|integer|exists:leads,id'
            ]);

            if ($validator->fails()) {
                Log::error('Voxbay Outgoing Call Validation Failed', [
                    'request_data' => $request->all(),
                    'validation_errors' => $validator->errors()->toArray()
                ]);
                
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400)->header('Access-Control-Allow-Origin', '*')
                       ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                       ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-CSRF-TOKEN');
            }

            $data = $request->all();
            $telecallerId = $data['telecaller_id'];
            $leadId = $data['lead_id'];

            // Get telecaller information
            $telecaller = User::select('code', 'phone', 'ext_no')
                ->where('id', $telecallerId)
                ->first();

            if (!$telecaller) {
                Log::error('Voxbay Outgoing Call - Telecaller not found', [
                    'telecaller_id' => $telecallerId,
                    'request_data' => $request->all()
                ]);
                
                return response()->json([
                    'status' => 'error',
                    'message' => 'Telecaller not found'
                ], 400);
            }

            if (empty($telecaller->ext_no)) {
                Log::error('Voxbay Outgoing Call - Extension not found', [
                    'telecaller_id' => $telecallerId,
                    'telecaller_data' => $telecaller->toArray()
                ]);
                
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Extension not found for telecaller'
                ], 400);
            }

            // Get lead information
            $lead = Lead::select('phone', 'code')
                ->where('id', $leadId)
                ->first();

                if (!$lead) {
                Log::error('Voxbay Outgoing Call - Lead not found', [
                    'lead_id' => $leadId,
                    'request_data' => $request->all()
                ]);
                
                    return response()->json([
                        'status' => 'error', 
                        'message' => 'Lead not found'
                    ], 400);
            }

            $extension = $telecaller->ext_no;
            $phone = $lead->phone;
            $uidNumber = env('UID_NUMBER');
            $upin = env('UPIN');
            $countryCode = $lead->code;
            $destination = $countryCode . $phone;

            // Validate Voxbay credentials
            if (empty($uidNumber) || empty($upin)) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Voxbay credentials not configured. Please check UID_NUMBER and UPIN environment variables.'
                ], 400);
            }

            $url = "https://x.voxbay.com/api/click_to_call?id_dept=0&uid={$uidNumber}&upin={$upin}&user_no={$extension}&destination={$destination}";
            // Log::info('Voxbay Outgoing Call URL: ' . $url);
            // Make the API call with error handling
            $context = stream_context_create([
                'http' => [
                    'timeout' => 30,
                    'method' => 'GET'
                ]
            ]);
            
            $response = @file_get_contents($url, false, $context);
            
            if ($response === false) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to connect to Voxbay service. Please check your internet connection and Voxbay service status.'
                ], 500);
            }

            // Log the call attempt
            Log::info('Voxbay Outgoing Call', [
                'telecaller_id' => $telecallerId,
                'lead_id' => $leadId,
                'extension' => $extension,
                'destination' => $destination,
                'url' => $url,
                'response' => $response
            ]);

            return response()->json([
                'status' => 'success', 
                'url_called' => $url,
                'response' => $response
            ], 200)->header('Access-Control-Allow-Origin', '*')
                   ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                   ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-CSRF-TOKEN');

        } catch (\Exception $e) {
            Log::error('Voxbay Outgoing Call Error: ' . $e->getMessage(), [
                'telecaller_id' => $request->input('telecaller_id'),
                'lead_id' => $request->input('lead_id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error', 
                'message' => 'Internal Server Error'
            ], 500);
        }
    }

    /**
     * Get telecaller extension number
     * GET /api/voxbay/telecaller/{id}/extension
     */
    public function getTelecallerExtension($id): JsonResponse
    {
        try {
            $telecaller = User::select('id', 'name', 'ext_no', 'phone', 'code')
                ->where('id', $id)
                ->first();
            
            if (!$telecaller) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Telecaller not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $telecaller->id,
                    'name' => $telecaller->name,
                'extension' => $telecaller->ext_no,
                'phone' => $telecaller->phone,
                'code' => $telecaller->code
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get Telecaller Extension Error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error', 
                'message' => 'Internal Server Error'
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
            $uidNumber = env('UID_NUMBER');
            $upin = env('UPIN');

            if (empty($uidNumber) || empty($upin)) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Voxbay credentials not configured'
                ], 400);
            }

            // Test with a dummy call
            $testUrl = "https://x.voxbay.com/api/click_to_call?id_dept=0&uid={$uidNumber}&upin={$upin}&user_no=test&destination=test";
            
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'method' => 'GET'
                ]
            ]);

            $response = @file_get_contents($testUrl, false, $context);

            return response()->json([
                'status' => 'success',
                'message' => 'Voxbay connection test completed',
                'credentials_configured' => true,
                'test_response' => $response
            ], 200);

        } catch (\Exception $e) {
            Log::error('Voxbay Connection Test Error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error', 
                'message' => 'Connection test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle incoming call webhook from Voxbay
     * POST /api/voxbay/webhook
     */
    public function webhook(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            
            Log::info('Voxbay Webhook Received', $data);

            // Validate required fields
            $validator = Validator::make($data, [
                'type' => 'required|string',
                'call_uuid' => 'required|string',
                'calledNumber' => 'nullable|string',
                'callerNumber' => 'nullable|string',
                'AgentNumber' => 'nullable|string',
                'extensionNumber' => 'nullable|string',
                'destinationNumber' => 'nullable|string',
                'callerid' => 'nullable|string',
                'duration' => 'nullable|integer',
                'status' => 'nullable|string',
                'date' => 'nullable|date',
                'start_time' => 'nullable|string',
                'end_time' => 'nullable|string',
                'recording_URL' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid webhook data',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Create or update call log
            $callLog = VoxbayCallLog::updateOrCreate(
                ['call_uuid' => $data['call_uuid']],
                [
                    'type' => $data['type'],
                    'calledNumber' => $data['calledNumber'] ?? null,
                    'callerNumber' => $data['callerNumber'] ?? null,
                    'AgentNumber' => $data['AgentNumber'] ?? null,
                    'extensionNumber' => $data['extensionNumber'] ?? null,
                    'destinationNumber' => $data['destinationNumber'] ?? null,
                    'callerid' => $data['callerid'] ?? null,
                    'duration' => $data['duration'] ?? null,
                    'status' => $data['status'] ?? null,
                    'date' => $data['date'] ?? null,
                    'start_time' => $data['start_time'] ?? null,
                    'end_time' => $data['end_time'] ?? null,
                    'recording_URL' => $data['recording_URL'] ?? null,
                ]
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Call log saved successfully',
                'call_log_id' => $callLog->id
            ], 200);

        } catch (\Exception $e) {
            Log::error('Voxbay Webhook Error: ' . $e->getMessage(), [
                'data' => $request->all(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Webhook processing failed'
            ], 500);
        }
    }
}