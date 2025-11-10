<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class GoogleFormController extends Controller
{
    /**
     * Handle Google Form submission
     * 
     * This endpoint receives POST requests from Google Apps Script
     * when a Google Form is submitted.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function handleFormSubmission(Request $request): JsonResponse
    {
        try {
            // Log all received data for verification
            Log::info('Google Form Submission Received', [
                'timestamp' => now()->toDateTimeString(),
                'all_request_data' => $request->all(),
                'headers' => $request->headers->all(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Log each parameter individually for easier debugging
            foreach ($request->all() as $key => $value) {
                Log::info("Google Form Parameter: {$key}", [
                    'key' => $key,
                    'value' => $value,
                    'type' => gettype($value),
                ]);
            }

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Form submission received and logged successfully',
                'timestamp' => now()->toDateTimeString(),
                'received_parameters' => array_keys($request->all()),
            ], 200);

        } catch (\Exception $e) {
            // Log any errors
            Log::error('Error processing Google Form submission', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error processing form submission',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

