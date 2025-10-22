<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FacebookApiService
{
    private $accessToken;
    private $leadFormId;
    private $apiUrl;
    private $tokenFilePath;

    public function __construct()
    {
        $this->apiUrl = 'https://graph.facebook.com/v24.0/';
        $this->leadFormId = config('services.facebook.lead_form_id');
        $this->tokenFilePath = storage_path('app/meta/fb_access_token.json');
        
        // Ensure meta directory exists
        if (!file_exists(dirname($this->tokenFilePath))) {
            mkdir(dirname($this->tokenFilePath), 0755, true);
        }
        
        $this->accessToken = $this->getAccessToken();
    }

    /**
     * Get Facebook access token
     */
    private function getAccessToken()
    {
        $appId = config('services.facebook.app_id');
        $appSecret = config('services.facebook.app_secret');
        $accessToken = config('services.facebook.access_token');

        if (!$appId || !$appSecret || !$accessToken) {
            Log::error('Missing required Facebook environment variables');
            return null;
        }

        return $this->getTokenFromFile($appId, $appSecret, $accessToken);
    }

    /**
     * Get token from file or refresh if needed
     */
    private function getTokenFromFile($appId, $appSecret, $originalToken)
    {
        if (file_exists($this->tokenFilePath)) {
            $tokenData = json_decode(file_get_contents($this->tokenFilePath), true);
            if (!empty($tokenData['access_token'])) {
                if ($this->isTokenValid($tokenData['access_token'])) {
                    return $tokenData['access_token'];
                } else {
                    Log::info('Stored token is invalid, refreshing...');
                }
            }
        }

        return $this->refreshToken($appId, $appSecret, $originalToken);
    }

    /**
     * Check if token is valid
     */
    private function isTokenValid($token)
    {
        $appId = config('services.facebook.app_id');
        $appSecret = config('services.facebook.app_secret');
        $appAccessToken = $appId . '|' . $appSecret;
        
        $url = "https://graph.facebook.com/debug_token?input_token={$token}&access_token={$appAccessToken}";
        
        try {
            $response = Http::get($url);
            $data = $response->json();
            
            return isset($data['data']['is_valid']) && $data['data']['is_valid'];
        } catch (\Exception $e) {
            Log::error('Token validation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Refresh access token
     */
    private function refreshToken($appId, $appSecret, $originalToken)
    {
        $url = "https://graph.facebook.com/oauth/access_token?grant_type=fb_exchange_token&client_id={$appId}&client_secret={$appSecret}&fb_exchange_token={$originalToken}";
        
        try {
            $response = Http::get($url);
            $data = $response->json();
            
            if (isset($data['access_token'])) {
                file_put_contents($this->tokenFilePath, json_encode(['access_token' => $data['access_token']]));
                return $data['access_token'];
            } else {
                Log::error('Failed to refresh token. Response: ' . json_encode($data));
            }
        } catch (\Exception $e) {
            Log::error('Token refresh failed: ' . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Make API call to Facebook
     */
    private function callFacebookApi($url)
    {
        try {
            $response = Http::get($url);
            
            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('Facebook API Error Status: ' . $response->status());
                Log::error('Facebook API Error Body: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Facebook API Request Error: ' . $e->getCode() . ' : ' . $e->getMessage());
            Log::error('Facebook API URL that failed: ' . $url);
        }

        return null;
    }

    /**
     * Fetch leads from Facebook
     */
    public function fetchLeads()
    {
        if (!$this->accessToken) {
            Log::error('No access token available');
            return [
                'error' => 'No access token available. Check your environment variables.',
                'debug_info' => [
                    'FB_APP_ID' => config('services.facebook.app_id') ? 'SET' : 'NOT SET',
                    'FB_APP_SECRET' => config('services.facebook.app_secret') ? 'SET' : 'NOT SET',
                    'FB_ACCESS_TOKEN' => config('services.facebook.access_token') ? 'SET' : 'NOT SET',
                    'FB_LEAD_FORM_ID' => config('services.facebook.lead_form_id') ? 'SET' : 'NOT SET'
                ]
            ];
        }

        if (!$this->leadFormId) {
            Log::error('No lead form ID configured');
            return ['error' => 'No lead form ID configured. Check FB_LEAD_FORM_ID in your environment.'];
        }

        return $this->fetchLeadsByToken($this->accessToken, $this->leadFormId, 1);
    }

    /**
     * Fetch leads by token
     */
    private function fetchLeadsByToken($token, $leadFormId, $formNo)
    {
        if (!$token) {
            return ['error' => 'Access token missing'];
        }

        $url = "https://graph.facebook.com/v24.0/{$leadFormId}/leads?access_token={$token}&limit=100";
        
        $response = $this->callFacebookApi($url);
        
        Log::info('Facebook API Response:', ['response' => $response]);
        
        if (!$response || !isset($response['data'])) {
            Log::warning('No leads found in Facebook API response');
            return ['message' => 'No leads found'];
        }

        Log::info('Found leads from Facebook:', ['count' => count($response['data'])]);

        $newLeads = [];
        foreach ($response['data'] as $lead) {
            $leadData = $this->mapLeadData($lead, $formNo);
            if ($leadData) {
                $newLeads[] = $leadData;
                Log::info('Mapped lead data:', ['lead' => $leadData]);
            }
        }

        return [
            'message' => count($newLeads) . " new leads found",
            'leads' => $newLeads,
            'count' => count($newLeads)
        ];
    }

    /**
     * Map Facebook lead data to our format
     */
    private function mapLeadData($lead, $formNo)
    {
        $mappedData = [
            'lead_id' => $lead['id'],
            'created_time' => isset($lead['created_time']) ? date('Y-m-d H:i:s', strtotime($lead['created_time'])) : null,
            'email' => '',
            'full_name' => '',
            'phone_number' => '',
            'other_details' => [],
            'form_no' => $formNo,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        foreach ($lead['field_data'] as $field) {
            switch ($field['name']) {
                case 'email':
                    $mappedData['email'] = $field['values'][0] ?? '';
                    break;
                case 'full_name':
                    $mappedData['full_name'] = $field['values'][0] ?? '';
                    break;
                case 'phone':
                case 'phone_number':
                    $mappedData['phone_number'] = $field['values'][0] ?? '';
                    break;
                default:
                    $mappedData['other_details'][$field['name']] = $field['values'][0] ?? '';
                    break;
            }
        }

        return $mappedData;
    }

    /**
     * Test original token
     */
    public function testOriginalToken()
    {
        $originalToken = config('services.facebook.access_token');
        if (!$originalToken) {
            return ['error' => 'No original token found in environment variables'];
        }

        $url = "https://graph.facebook.com/v24.0/me?access_token={$originalToken}";
        $response = $this->callFacebookApi($url);

        if ($response && isset($response['id'])) {
            return [
                'message' => 'Original token is valid',
                'user_id' => $response['id'],
                'token_type' => 'original'
            ];
        } else {
            return [
                'error' => 'Original token validation failed',
                'response' => $response
            ];
        }
    }

    /**
     * Test current token
     */
    public function testToken()
    {
        if ($this->accessToken) {
            $url = "https://graph.facebook.com/v24.0/me?access_token={$this->accessToken}";
            $response = $this->callFacebookApi($url);

            if ($response && isset($response['id'])) {
                return [
                    'message' => 'Token is valid',
                    'user_id' => $response['id']
                ];
            } else {
                return [
                    'error' => 'Token validation failed',
                    'response' => $response
                ];
            }
        } else {
            return [
                'error' => 'No access token available',
                'debug_info' => [
                    'FB_APP_ID' => config('services.facebook.app_id') ? 'SET' : 'NOT SET',
                    'FB_APP_SECRET' => config('services.facebook.app_secret') ? 'SET' : 'NOT SET',
                    'FB_ACCESS_TOKEN' => config('services.facebook.access_token') ? 'SET' : 'NOT SET',
                    'FB_LEAD_FORM_ID' => config('services.facebook.lead_form_id') ? 'SET' : 'NOT SET'
                ]
            ];
        }
    }

    /**
     * Try different token exchange methods
     */
    public function tryTokenExchange()
    {
        $appId = config('services.facebook.app_id');
        $appSecret = config('services.facebook.app_secret');
        $originalToken = config('services.facebook.access_token');

        if (!$appId || !$appSecret || !$originalToken) {
            return ['error' => 'Missing required environment variables'];
        }

        $results = [];

        // Method 1: Standard token exchange
        $url1 = "https://graph.facebook.com/oauth/access_token?grant_type=fb_exchange_token&client_id={$appId}&client_secret={$appSecret}&fb_exchange_token={$originalToken}";
        $response1 = $this->callFacebookApi($url1);
        $results['method_1_standard'] = $response1;

        // Method 2: App token generation
        $url2 = "https://graph.facebook.com/oauth/access_token?grant_type=client_credentials&client_id={$appId}&client_secret={$appSecret}";
        $response2 = $this->callFacebookApi($url2);
        $results['method_2_app_token'] = $response2;

        // Method 3: v18.0 API token exchange
        $url3 = "https://graph.facebook.com/v18.0/oauth/access_token?grant_type=fb_exchange_token&client_id={$appId}&client_secret={$appSecret}&fb_exchange_token={$originalToken}";
        $response3 = $this->callFacebookApi($url3);
        $results['method_3_v18_exchange'] = $response3;

        // Method 4: Debug the original token
        $debugUrl = "https://graph.facebook.com/debug_token?input_token={$originalToken}&access_token={$appId}|{$appSecret}";
        $debugResponse = $this->callFacebookApi($debugUrl);
        $results['method_4_debug_token'] = $debugResponse;

        return [
            'message' => 'Token exchange attempts completed',
            'results' => $results
        ];
    }

    /**
     * Debug environment variables
     */
    public function debugEnv()
    {
        return [
            'environment_variables' => [
                'FB_APP_ID' => config('services.facebook.app_id') ? 'SET' : 'NOT SET',
                'FB_APP_SECRET' => config('services.facebook.app_secret') ? 'SET' : 'NOT SET',
                'FB_ACCESS_TOKEN' => config('services.facebook.access_token') ? 'SET' : 'NOT SET',
                'FB_LEAD_FORM_ID' => config('services.facebook.lead_form_id') ? 'SET' : 'NOT SET'
            ],
            'token_file_exists' => file_exists($this->tokenFilePath),
            'token_file_path' => $this->tokenFilePath,
            'storage_path' => storage_path('app'),
            'meta_directory_exists' => is_dir(dirname($this->tokenFilePath))
        ];
    }
}
