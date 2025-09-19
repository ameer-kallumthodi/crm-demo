<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\Users_model;
use App\Models\Leads_model;
use App\Models\Voxbay_calllogs_model;

class Voxbay extends ResourceController
{
    use ResponseTrait;

    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->users_model = new Users_model();
        $this->leads_model = new Leads_model();
        $this->voxbay_calllogs_model = new Voxbay_calllogs_model();

    }

    /**
     * Handle incoming call events
     * POST /api/voxbay/incoming-call
     */
    public function callcenterbridging()
    {
        log_message('error', print_r($_REQUEST, true));
        try {
            // Get JSON data from request
            $data = $this->request->getJSON(true);
            
            // Log the received data for debugging
            log_message('error', 'Incoming Call Data: ' . json_encode($data));
            
            // Determine the event type and process accordingly
            if (isset($data['CallUUID'])) {
                
                // Event 1: Incoming call landed on server
                if (isset($data['calledNumber']) && isset($data['callerNumber'])) {
                    $this->handleIncomingCallLanded($data);
                }
                
            }
            
            // Return success response as expected by Voxbay
            return $this->respond('success', 200);
            
        } catch (\Exception $e) {
            log_message('error', 'Voxbay Incoming Call Error: ' . $e->getMessage());
            return $this->respond('error', 500);
        }
    }

    /**
     * Handle outgoing call events
     * POST /api/voxbay/outgoing-call
     */
    public function outgoingCall()
    {
        log_message('error', print_r($_REQUEST, true));
        try {
            // Get JSON data from request
            $data = $this->request->getJSON(true);
            

            

            // Log the received data for debugging
            log_message('error', 'outgoing voxbay Call Data: ' . json_encode($data));
            
            // Determine the event type and process accordingly
            if (isset($data['extension']) && isset($data['destination'])) {
            // Outgoing call
            $this->handleOutgoingCallLanded($data);
        } elseif (isset($data['callerNumber']) && isset($data['calledNumber'])) {
            // Incoming call
            $this->handleIncomingCallLanded($data);
        }

            // Return success response as expected by Voxbay
            return $this->respond('success', 200);
            
        } catch (\Exception $e) {
            log_message('error', 'Voxbay Incoming Call Error: ' . $e->getMessage());
            return $this->respond('error', 500);
        }
    }

    public function clickToCall()
    {
        try {
            // Get query parameters
            $params = $this->request->getGet();
            
            // Validate required parameters
            $required = ['uid', 'upin', 'user_no', 'destination', 'callerid'];
            foreach ($required as $param) {
                if (!isset($params[$param])) {
                    return $this->respond(['error' => "Missing required parameter: $param"], 400);
                }
            }
            
            // Log the click to call request
            log_message('info', 'Click to Call Request: ' . json_encode($params));
            
            // Process click to call
            $this->handleClickToCall($params);
            
            return $this->respond(['status' => 'success', 'message' => 'Click to call initiated'], 200);
            
        } catch (\Exception $e) {
            log_message('error', 'Voxbay Click to Call Error: ' . $e->getMessage());
            return $this->respond(['error' => 'Internal server error'], 500);
        }
    }

    private function handleIncomingCallLanded($incoming)
    {
        $data = [
            'callerNumber' => $incoming['callerNumber'] ?? '',
            'calledNumber' => $incoming['calledNumber'] ?? '',
            'call_uuid'    => $incoming['CallUUID'] ?? '', // ✅ correct key
            'type'         => 'incoming',
            'date'         => date('Y-m-d'),
            'start_time'   => date('H:i:s'),
            'callerid'     => $incoming['callerid'] ?? '',
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
            'created_by'   => '1',
            'updated_by'   => '1'
        ];

        $this->voxbay_calllogs_model->add($data);
    }


    /**
     * Handle call answered by agent
     */
    private function handleCallAnswered($data)
    {
        $agentNumber = $data['AgentNumber'] ?? '';
        $callerNumber = $data['callerNumber'] ?? '';
        $callUUID = $data['CallUUID'] ?? '';
        
        log_message('info', "Call answered - Agent: $agentNumber, Caller: $callerNumber, UUID: $callUUID");
        
        // Your business logic here
        // Example: Update call status, notify agent dashboard, etc.
    }

    /**
     * Handle call disconnected
     */
    private function handleCallDisconnected($data)
    {
        $agentNumber = $data['AgentNumber'] ?? '';
        $callUUID = $data['CallUUlD'] ?? $data['CallUUID'] ?? ''; // Note: typo in documentation
        
        log_message('info', "Call disconnected - Agent: $agentNumber, UUID: $callUUID");
        
        // Your business logic here
        // Example: Update call status, calculate duration, etc.
    }

    /**
     * Handle CDR push for incoming calls
     */
    private function handleCDRPush($data)
    {
        $cdrData = [
            'calledNumber' => $data['calledNumber'] ?? '',
            'callerNumber' => $data['callerNumber'] ?? '',
            'totalCallDuration' => $data['totalCallDuration'] ?? '',
            'callDate' => $data['callDate'] ?? '',
            'callStatus' => $data['callStatus'] ?? '',
            'recording_URL' => $data['recording_URL'] ?? '',
            'AgentNumber' => $data['AgentNumber'] ?? '',
            'CallUUID' => $data['CallUUID'] ?? '',
            'callStartTime' => $data['callStartTime'] ?? '',
            'callEndTime' => $data['callEndTime'] ?? '',
            'conversationDuration' => $data['conversationDuration'] ?? '',
            'dtmf' => $data['dtmf'] ?? '',
            'transferredNumber' => $data['transferredNumber'] ?? ''
        ];
        
        log_message('info', "CDR Push - Call completed: " . json_encode($cdrData));
        
        // Your business logic here
        // Example: Save complete call record, generate reports, send notifications, etc.
    }

    /**
     * Handle outgoing call initiated
     */
    private function handleOutgoingCallInitiated($data)
    {
        $extension = $data['extension'] ?? '';
        $destination = $data['destination'] ?? '';
        $callerid = $data['callerid'] ?? '';
        $callUUID = $data['callUUlD'] ?? ''; // Note: typo in documentation
        
        log_message('info', "Outgoing call initiated - Ext: $extension, Dest: $destination, UUID: $callUUID");
        
        // Your business logic here
    }

    // CDR PUSH TRY

    public function incomingcdrpush()
    {
        // Try both JSON and POST data
        $data = $this->request->getJSON(true);
        if (empty($data)) {
            $data = $this->request->getPost();
        }
        
        log_message('error', 'CDR Data incoming: ' . print_r($data, true));
        
        try {
            $updateData = [
                'duration'      => $data['duration'] ?? '',
                'status'        => $data['status'] ?? '',
                'end_time'      => date('H:i:s', strtotime($data['date'] ?? 'now')),
                'recording_URL' => $data['recording_URL'] ?? '',
                'updated_at'    => date('Y-m-d H:i:s'),
                'updated_by'    => '1',
            ];
            
            $where = [];
            
            // Handle both possible field names for CallUUID
            $callUUID = $data['CallUUID'] ?? $data['callUUID'] ?? '';
            
            if (!empty($callUUID)) {
                $where['call_uuid'] = $callUUID;
                $result = $this->voxbay_calllogs_model->edit($updateData, $where);
                
                if ($result) {
                    log_message('info', 'CDR Updated successfully for UUID: ' . $callUUID);
                    return $this->response->setBody('success');
                } else {
                    log_message('error', 'CDR Update failed for UUID: ' . $callUUID);
                    return $this->response->setStatusCode(400)->setBody('update_failed');
                }
            } else {
                log_message('error', 'CDR Update Error: No valid CallUUID found in data');
                return $this->response->setStatusCode(400)->setBody('missing_uuid');
            }
            
        } catch (\Exception $e) {
            log_message('error', 'CDR Error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setBody('error');
        }
    }

    public function outgoingcdrpush()
    {
        // Try both JSON and POST data
        $data = $this->request->getJSON(true);
        if (empty($data)) {
            $data = $this->request->getPost();
        }
        
        log_message('error', 'CDR Data outgoing: ' . print_r($data, true));
        
        try {
            $updateData = [
                'duration'      => $data['duration'] ?? '',
                'status'        => $data['status'] ?? '',
                'end_time'      => date('H:i:s', strtotime($data['date'] ?? 'now')),
                'recording_URL' => $data['recording_URL'] ?? '',
                'updated_at'    => date('Y-m-d H:i:s'),
                'updated_by'    => '1',
            ];
            
            $where = [];
            
            // Handle both possible field names for CallUUID (note the typo in callUUlD)
            $callUUID = $data['callUUID'] ?? $data['CallUUID'] ?? $data['callUUlD'] ?? '';
            
            if (!empty($callUUID)) {
                $where['call_uuid'] = $callUUID;
                $result = $this->voxbay_calllogs_model->edit($updateData, $where);
                
                if ($result) {
                    log_message('info', 'CDR Updated successfully for UUID: ' . $callUUID);
                    return $this->response->setBody('success');
                } else {
                    log_message('error', 'CDR Update failed for UUID: ' . $callUUID);
                    return $this->response->setStatusCode(400)->setBody('update_failed');
                }
            } else {
                log_message('error', 'CDR Update Error: No valid CallUUID found in data');
                return $this->response->setStatusCode(400)->setBody('missing_uuid');
            }
            
        } catch (\Exception $e) {
            log_message('error', 'CDR Error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setBody('error');
        }
    }

public function connectincoming()
{
    // Try both JSON and POST data
    $data = $this->request->getJSON(true);
    if (empty($data)) {
        $data = $this->request->getPost();
    }
    
    log_message('error', 'Connect Incoming Data: ' . print_r($data, true));
    
    try {
        // Handle both possible field names for CallUUID
        $callUUID = $data['CallUUID'] ?? $data['callUUID'] ?? $data['callUUlD'] ?? '';
        $agentNumber = $data['AgentNumber'] ?? $data['AgentNumber'] ?? $data['AgentNumber'] ?? '';
        
        if (empty($callUUID)) {
            log_message('error', 'Connect Incoming Error: No valid CallUUID found');
            return $this->response->setStatusCode(400)->setBody('missing_uuid');
        }
        
        // Check if record exists with this UUID
        $where = ['call_uuid' => $callUUID];
        $existingRecord = $this->voxbay_calllogs_model->get($where);
        
        if ($existingRecord) {
            // Update existing record with agent number
            $updateData = [
                'AgentNumber' => $agentNumber,
                'status' => 'connected',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => '1'
            ];
            
            $result = $this->voxbay_calllogs_model->edit($updateData, $where);
            
            if ($result) {
                log_message('info', "Incoming call connected - UUID: $callUUID, Agent: $agentNumber");
                return $this->response->setBody('success');
            } else {
                log_message('error', "Failed to update incoming call connection for UUID: $callUUID");
                return $this->response->setStatusCode(400)->setBody('update_failed');
            }
        } else {
            // Create new record if UUID not found
            $insertData = [
                'call_uuid' => $callUUID,
                'AgentNumber' => $agentNumber,
                'callerNumber' => $data['callerNumber'] ?? $data['caller_number'] ?? '',
                'calledNumber' => $data['calledNumber'] ?? $data['called_number'] ?? '',
                'type' => 'incoming',
                'status' => 'connected',
                'date' => date('Y-m-d'),
                'start_time' => date('H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'created_by' => '1',
                'updated_by' => '1'
            ];
            
            $result = $this->voxbay_calllogs_model->add($insertData);
            
            if ($result) {
                log_message('info', "New incoming call connection recorded - UUID: $callUUID, Agent: $agentNumber");
                return $this->response->setBody('success');
            } else {
                log_message('error', "Failed to create incoming call connection record for UUID: $callUUID");
                return $this->response->setStatusCode(400)->setBody('insert_failed');
            }
        }
        
    } catch (\Exception $e) {
        log_message('error', 'Connect Incoming Error: ' . $e->getMessage());
        return $this->response->setStatusCode(500)->setBody('error');
    }
}

/**
 * Handle outgoing call connection/answer events
 * POST /api/voxbay/connect-outgoing
 */
public function connectoutgoing()
{
    // Try both JSON and POST data
    $data = $this->request->getJSON(true);
    if (empty($data)) {
        $data = $this->request->getPost();
    }
    
    log_message('error', 'Connect Outgoing Data: ' . print_r($data, true));
    
    try {
        // Handle both possible field names for CallUUID
        $callUUID = $data['callUUID'] ?? $data['callUUID'] ?? $data['callUUlD'] ?? '';
        $agentNumber = $data['AgentNumber'] ?? $data['AgentNumber'] ?? $data['AgentNumber'] ?? $data['extension'] ?? '';
        
        if (empty($callUUID)) {
            log_message('error', 'Connect Outgoing Error: No valid CallUUID found');
            return $this->response->setStatusCode(400)->setBody('missing_uuid');
        }
        
        // Check if record exists with this UUID
        $where = ['call_uuid' => $callUUID];
        $existingRecord = $this->voxbay_calllogs_model->get($where);
        
        if ($existingRecord) {
            // Update existing record with agent number
            $updateData = [
                'AgentNumber' => $agentNumber,
                'status' => 'connected',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => '1'
            ];
            
            $result = $this->voxbay_calllogs_model->edit($updateData, $where);
            
            if ($result) {
                log_message('info', "Outgoing call connected - UUID: $callUUID, Agent: $agentNumber");
                return $this->response->setBody('success');
            } else {
                log_message('error', "Failed to update outgoing call connection for UUID: $callUUID");
                return $this->response->setStatusCode(400)->setBody('update_failed');
            }
        } else {
            // Create new record if UUID not found
            $insertData = [
                'call_uuid' => $callUUID,
                'AgentNumber' => $agentNumber,
                'extensionNumber' => $data['extension'] ?? $agentNumber,
                'destinationNumber' => $data['destination'] ?? $data['destinationNumber'] ?? '',
                'callerid' => $data['callerid'] ?? $data['callerNumber'] ?? '',
                'type' => 'outgoing',
                'status' => 'connected',
                'date' => date('Y-m-d'),
                'start_time' => date('H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'created_by' => '1',
                'updated_by' => '1'
            ];
            
            $result = $this->voxbay_calllogs_model->add($insertData);
            
            if ($result) {
                log_message('info', "New outgoing call connection recorded - UUID: $callUUID, Agent: $agentNumber");
                return $this->response->setBody('success');
            } else {
                log_message('error', "Failed to create outgoing call connection record for UUID: $callUUID");
                return $this->response->setStatusCode(400)->setBody('insert_failed');
            }
        }
        
    } catch (\Exception $e) {
        log_message('error', 'Connect Outgoing Error: ' . $e->getMessage());
        return $this->response->setStatusCode(500)->setBody('error');
    }
}


public function disconnectincoming()
{
    // Try both JSON and POST data
    $data = $this->request->getJSON(true);
    if (empty($data)) {
        $data = $this->request->getPost();
    }
    
    log_message('error', 'Disconnect Incoming Data: ' . print_r($data, true));
    
    try {
        // Handle both possible field names for CallUUID
        $callUUID = $data['callUUID'] ?? $data['callUUID'] ?? $data['callUUlD'] ?? $data['call_UUID'] ?? '';
        $agentNumber = $data['AgentNumber'] ?? $data['agentNumber'] ?? $data['agent_number'] ?? '';
        $status = $data['status'] ?? $data['callStatus'] ?? $data['call_status'] ?? '';
        
        if (empty($callUUID)) {
            log_message('error', 'Disconnect Incoming Error: No valid CallUUID found');
            return $this->response->setStatusCode(400)->setBody('missing_uuid');
        }
        
        // Check if record exists with this UUID
        $where = ['call_uuid' => $callUUID];
        $existingRecord = $this->voxbay_calllogs_model->get($where);
        
        if ($existingRecord) {
            // Update existing record with disconnect status
            $updateData = [
                'status' => $status,
                
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => '1'
            ];
            
            // Only update agent_number if provided and not already set
            
                $updateData['AgentNumber'] = $agentNumber;
            
            
            $result = $this->voxbay_calllogs_model->edit($updateData, $where);
            
            if ($result) {
                log_message('info', "Incoming call disconnected - UUID: $callUUID, Agent: $agentNumber, Status: $status");
                return $this->response->setBody('success');
            } else {
                log_message('error', "Failed to update incoming call disconnection for UUID: $callUUID");
                return $this->response->setStatusCode(400)->setBody('update_failed');
            }
        } else {
            // Create new record if UUID not found (edge case)
            $insertData = [
                'call_uuid' => $callUUID,
                'Agentnumber' => $agentNumber,
                'type' => 'incoming',
                'status' => $status,
                'date' => date('Y-m-d'),
                'start_time' => date('H:i:s'),
               
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'created_by' => '1',
                'updated_by' => '1'
            ];
            
            $result = $this->voxbay_calllogs_model->add($insertData);
            
            if ($result) {
                log_message('info', "New incoming call disconnection recorded - UUID: $callUUID, Agent: $agentNumber, Status: $status");
                return $this->response->setBody('success');
            } else {
                log_message('error', "Failed to create incoming call disconnection record for UUID: $callUUID");
                return $this->response->setStatusCode(400)->setBody('insert_failed');
            }
        }
        
    } catch (\Exception $e) {
        log_message('error', 'Disconnect Incoming Error: ' . $e->getMessage());
        return $this->response->setStatusCode(500)->setBody('error');
    }
}

/**
 * Handle outgoing call disconnection events
 * POST /api/voxbay/disconnect-outgoing
 */
public function disconnectoutgoing()
{
    // Try both JSON and POST data
    $data = $this->request->getJSON(true);
    if (empty($data)) {
        $data = $this->request->getPost();
    }
    
    log_message('error', 'Disconnect Outgoing Data: ' . print_r($data, true));
    
    try {
        // Handle both possible field names for CallUUID
        $callUUID = $data['callUUID'] ?? $data['CallUUID'] ?? $data['callUUlD'] ?? $data['call_UUID'] ?? '';
        $agentNumber = $data['AgentNumber'] ?? $data['agentNumber'] ?? $data['agent_number'] ?? $data['extension'] ?? '';
        $status = $data['status'] ?? $data['callStatus'] ?? $data['call_status'] ?? '';
        
        if (empty($callUUID)) {
            log_message('error', 'Disconnect Outgoing Error: No valid CallUUID found');
            return $this->response->setStatusCode(400)->setBody('missing_uuid');
        }
        
        // Check if record exists with this UUID
        $where = ['call_uuid' => $callUUID];
        $existingRecord = $this->voxbay_calllogs_model->get($where);
        
        if ($existingRecord) {
            // Update existing record with disconnect status
            $updateData = [
                'status' => $status,
                
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => '1'
            ];
            
            $updateData['AgentNumber'] = $agentNumber;
            
            $result = $this->voxbay_calllogs_model->edit($updateData, $where);
            
            if ($result) {
                log_message('info', "Outgoing call disconnected - UUID: $callUUID, Agent: $agentNumber, Status: $status");
                return $this->response->setBody('success');
            } else {
                log_message('error', "Failed to update outgoing call disconnection for UUID: $callUUID");
                return $this->response->setStatusCode(400)->setBody('update_failed');
            }
        } else {
            // Create new record if UUID not found (edge case)
            $insertData = [
                'call_uuid' => $callUUID,
                'AgentNumber' => $agentNumber,
                'type' => 'outgoing',
                'status' => $status,
                'date' => date('Y-m-d'),
                'start_time' => date('H:i:s'),
                
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'created_by' => '1',
                'updated_by' => '1'
            ];
            
            $result = $this->voxbay_calllogs_model->add($insertData);
            
            if ($result) {
                log_message('info', "New outgoing call disconnection recorded - UUID: $callUUID, Agent: $agentNumber, Status: $status");
                return $this->response->setBody('success');
            } else {
                log_message('error', "Failed to create outgoing call disconnection record for UUID: $callUUID");
                return $this->response->setStatusCode(400)->setBody('insert_failed');
            }
        }
        
    } catch (\Exception $e) {
        log_message('error', 'Disconnect Outgoing Error: ' . $e->getMessage());
        return $this->response->setStatusCode(500)->setBody('error');
    }
}


// Also fix the handleOutgoingCallLanded method to handle the typo:
private function handleOutgoingCallLanded($data)
{
    $insertData = [
        'extensionNumber' => $data['extension'] ?? '',
        'destinationNumber' => $data['destination'] ?? '',
        'type' => 'outgoing',
        'date' => date('Y-m-d'), 
        'start_time' => date('H:i:s'), 
        'callerid' => $data['callerid'] ?? '',
        // Handle both possible field names (callUUlD vs CallUUID)
        'call_uuid' => $data['callUUlD'] ?? $data['CallUUID'] ?? '',
        'created_at' => date('Y-m-d H:i:s'), 
        'updated_at' => date('Y-m-d H:i:s'),
        'created_by' => '1',
        'updated_by' => '1'
    ];
    
    $result = $this->voxbay_calllogs_model->add($insertData);
    
    if ($result) {
        log_message('info', 'Outgoing call logged successfully: ' . ($insertData['call_uuid'] ?? 'no-uuid'));
    } else {
        log_message('error', 'Failed to log outgoing call');
    }
}

// Add this debugging method
public function debugRequest()
{
    log_message('error', 'Raw Input: ' . file_get_contents('php://input'));
    log_message('error', 'Content-Type: ' . $this->request->getHeaderLine('Content-Type'));
    log_message('error', 'Method: ' . $this->request->getMethod());
    log_message('error', 'GET Data: ' . print_r($this->request->getGet(), true));
    log_message('error', 'POST Data: ' . print_r($this->request->getPost(), true));
    log_message('error', 'JSON Data: ' . print_r($this->request->getJSON(true), true));
    
    return $this->response->setBody('debug_complete');
}






    private function handleOutgoingCDRPush($data)
    {
        $cdrData = [
            'extension' => $data['extension'] ?? '',
            'destination' => $data['destination'] ?? '',
            'callerid' => $data['callerid'] ?? '',
            'duration' => $data['duration'] ?? '',
            'status' => $data['status'] ?? '',
            'date' => $data['date'] ?? '',
            'recording_URL' => $data['recording_URL'] ?? ''
        ];
        
        log_message('info', "Outgoing CDR Push - Call completed: " . json_encode($cdrData));
        
        // Your business logic here
    }

    /**
     * Handle click to call
     */
    private function handleClickToCall($params)
    {
        $clickData = [
            'uid' => $params['uid'] ?? '',
            'upin' => $params['upin'] ?? '',
            'user_no' => $params['user_no'] ?? '',
            'destination' => $params['destination'] ?? '',
            'callerid' => $params['callerid'] ?? '',
            'source' => $params['source'] ?? '', // For mobile to mobile
            'id_dept' => $params['id_dept'] ?? 0
        ];
        
        log_message('info', "Click to Call - Data: " . json_encode($clickData));
        
        // Your business logic here
        // Example: Initiate call, log request, etc.
    }

    /**
     * Get call status definitions
     * GET /api/voxbay/call-status
     */
    public function getCallStatus()
    {
        $statusDefinitions = [
            'ANSWERED' => 'Call is answered. A successful dial. The caller reached the Callee.',
            'BUSY' => 'Busy signal. The dial command reached its number but the number is busy.',
            'NOANSWER' => 'No answer. The dial command reached its number, the number rang for too long, then the dial timed out.',
            'CONGESTION' => 'Congestion. This status is usually a sign that the dialled number is not recognised.',
            'CHANUNAVAIL' => 'Channel unavailable. On SIP, peer may not be registered.',
            'CANCEL' => 'Call is cancelled. The dial command reached its number but the caller hung up before the Callee picked up.'
        ];
        
        return $this->respond($statusDefinitions, 200);
    }

    /**
     * Health check endpoint
     * GET /api/voxbay/health
     */
    public function health()
    {
        return $this->respond(['status' => 'healthy', 'timestamp' => date('Y-m-d H:i:s')], 200);
    }
}