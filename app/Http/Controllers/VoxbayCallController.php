<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use App\Models\VoxbayCallLog;

class VoxbayCallController extends Controller
{
    public function callcenterbridging(Request $request)
    {
        // Log all incoming request data
        $rawInput = file_get_contents('php://input');
        
        Log::info('=== INCOMING CALL REQUEST START ===');
        Log::info('Request Method: ' . $request->method());
        Log::info('Content-Type: ' . $request->header('Content-Type'));
        Log::info('Raw Input: ' . $rawInput);
        Log::info('All Request Data: ' . print_r($request->all(), true));
        Log::info('POST Data: ' . print_r($request->post(), true));
        Log::info('GET Data: ' . print_r($request->query(), true));
        
        try {
            // Handle both JSON and form data
            $data = [];
            if ($request->isJson() && $request->json()) {
                $data = $request->json()->all();
                Log::info('Data extracted from JSON: ' . json_encode($data));
            } else {
                $data = $request->all();
                Log::info('Data extracted from Form/Post: ' . json_encode($data));
            }
            
            Log::info('Final Data Array: ' . json_encode($data, JSON_PRETTY_PRINT));
            Log::info('CallUUID present: ' . (isset($data['CallUUID']) ? 'YES - ' . $data['CallUUID'] : 'NO'));
            Log::info('calledNumber present: ' . (isset($data['calledNumber']) ? 'YES - ' . $data['calledNumber'] : 'NO'));
            Log::info('callerNumber present: ' . (isset($data['callerNumber']) ? 'YES - ' . $data['callerNumber'] : 'NO'));

            if (isset($data['CallUUID'])) {
                Log::info('CallUUID found, checking for calledNumber and callerNumber...');
                if (isset($data['calledNumber']) && isset($data['callerNumber'])) {
                    Log::info('All required fields present, calling handleIncomingCallLanded...');
                    $result = $this->handleIncomingCallLanded($data);
                    Log::info('handleIncomingCallLanded completed. Result: ' . ($result ? 'SUCCESS' : 'FAILED'));
                } else {
                    Log::warning('Missing required fields - calledNumber or callerNumber not present');
                    Log::warning('Available keys: ' . implode(', ', array_keys($data)));
                }
            } else {
                Log::warning('CallUUID not found in request data');
                Log::warning('Available keys: ' . implode(', ', array_keys($data)));
            }

            Log::info('=== INCOMING CALL REQUEST END ===');
            return response('success', 200);
        } catch (\Exception $e) {
            Log::error('=== VOXBAY INCOMING CALL ERROR ===');
            Log::error('Error Message: ' . $e->getMessage());
            Log::error('Error File: ' . $e->getFile() . ':' . $e->getLine());
            Log::error('Stack Trace: ' . $e->getTraceAsString());
            Log::error('=== END ERROR ===');
            return response('error: ' . $e->getMessage(), 500);
        }
    }

    public function outgoingCall(Request $request)
    {
        Log::error(print_r($request->all(), true));

        try {
            $data = $request->json()->all();
            Log::error('outgoing voxbay Call Data: '.json_encode($data));

            if (isset($data['extension']) && isset($data['destination'])) {
                $this->handleOutgoingCallLanded($data);
            } elseif (isset($data['callerNumber']) && isset($data['calledNumber'])) {
                $this->handleIncomingCallLanded($data);
            }

            return response('success', 200);
        } catch (\Exception $e) {
            Log::error('Voxbay Outgoing Error: '.$e->getMessage());
            return response('error', 500);
        }
    }

    public function clickToCall(Request $request)
    {
        try {
            $params = $request->query();
            $required = ['uid', 'upin', 'user_no', 'destination', 'callerid'];

            foreach ($required as $r) {
                if (!isset($params[$r])) {
                    return response(['error'=>"Missing required parameter: $r"], 400);
                }
            }

            Log::info('Click to Call Request: '.json_encode($params));
            $this->handleClickToCall($params);

            return response(['status'=>'success','message'=>'Click to call initiated'], 200);

        } catch (\Exception $e) {
            Log::error('Voxbay Click to Call Error: '.$e->getMessage());
            return response(['error' => 'Internal server error'], 500);
        }
    }

    private function handleIncomingCallLanded($incoming)
    {
        Log::info('=== HANDLE INCOMING CALL LANDED START ===');
        Log::info('Input data: ' . json_encode($incoming, JSON_PRETTY_PRINT));
        
        try {
            $callData = [
                'callerNumber' => $incoming['callerNumber'] ?? $incoming['caller_number'] ?? '',
                'calledNumber' => $incoming['calledNumber'] ?? $incoming['called_number'] ?? '',
                'call_uuid'    => $incoming['CallUUID'] ?? $incoming['callUUID'] ?? $incoming['call_uuid'] ?? '',
                'type'         => 'incoming',
                'date'         => date('Y-m-d'),
                'start_time'   => date('H:i:s'),
                'callerid'     => $incoming['callerid'] ?? $incoming['callerId'] ?? '',
                'created_by'   => 1,
                'updated_by'   => 1,
            ];
            
            Log::info('Prepared call data for insertion: ' . json_encode($callData, JSON_PRETTY_PRINT));
            
            // Validate required fields
            if (empty($callData['call_uuid'])) {
                Log::error('Validation failed: call_uuid is empty');
                return false;
            }
            
            if (empty($callData['callerNumber']) && empty($callData['calledNumber'])) {
                Log::warning('Both callerNumber and calledNumber are empty, but proceeding...');
            }
            
            Log::info('Attempting to create VoxbayCallLog record...');
            $callLog = VoxbayCallLog::create($callData);
            
            Log::info('Database record created successfully!');
            Log::info('Record ID: ' . $callLog->id);
            Log::info('Call UUID: ' . $callLog->call_uuid);
            Log::info('=== HANDLE INCOMING CALL LANDED SUCCESS ===');
            
            return true;
            
        } catch (QueryException $e) {
            Log::error('=== DATABASE QUERY ERROR ===');
            Log::error('SQL Error: ' . $e->getMessage());
            Log::error('SQL Code: ' . $e->getCode());
            Log::error('SQL State: ' . ($e->errorInfo[0] ?? 'N/A'));
            Log::error('SQL Message: ' . ($e->errorInfo[2] ?? 'N/A'));
            Log::error('=== END DATABASE ERROR ===');
            return false;
        } catch (\Exception $e) {
            Log::error('=== HANDLE INCOMING CALL LANDED ERROR ===');
            Log::error('Error Message: ' . $e->getMessage());
            Log::error('Error File: ' . $e->getFile() . ':' . $e->getLine());
            Log::error('Stack Trace: ' . $e->getTraceAsString());
            Log::error('=== END ERROR ===');
            return false;
        }
    }

    private function handleOutgoingCallLanded($data)
    {
        VoxbayCallLog::create([
            'extensionNumber' => $data['extension'] ?? '',
            'destinationNumber' => $data['destination'] ?? '',
            'type' => 'outgoing',
            'date' => date('Y-m-d'),
            'start_time' => date('H:i:s'),
            'callerid' => $data['callerid'] ?? '',
            'call_uuid' => $data['callUUlD'] ?? $data['CallUUID'] ?? '',
            'created_by' => 1,
            'updated_by' => 1
        ]);
    }

    private function handleClickToCall($params)
    {
        Log::info("Click to Call - Data: ".json_encode($params));
    }

    public function incomingcdrpush(Request $request)
    {
        $data = $request->json()->all() ?: $request->post();
        Log::error('CDR Data incoming: '.print_r($data,true));

        try {
            $callUUID = $data['CallUUID'] ?? $data['callUUID'] ?? '';

            if (!$callUUID) return response('missing_uuid', 400);

            $log = VoxbayCallLog::where('call_uuid',$callUUID)->first();

            if (!$log) return response('update_failed', 400);

            $log->update([
                'duration' => $data['duration'] ?? '',
                'status' => $data['status'] ?? '',
                'end_time' => date('H:i:s'),
                'recording_URL' => $data['recording_URL'] ?? '',
                'updated_by' => 1
            ]);

            return response('success');

        } catch (\Exception $e) {
            Log::error('CDR Error: '.$e->getMessage());
            return response('error',500);
        }
    }

    public function outgoingcdrpush(Request $request)
    {
        $data = $request->json()->all() ?: $request->post();
        Log::error('CDR Data outgoing: '.print_r($data,true));

        try {
            $callUUID = $data['callUUID'] ?? $data['CallUUID'] ?? $data['callUUlD'] ?? '';

            if (!$callUUID) return response('missing_uuid',400);

            $log = VoxbayCallLog::where('call_uuid',$callUUID)->first();

            if (!$log) return response('update_failed',400);

            $log->update([
                'duration' => $data['duration'] ?? '',
                'status' => $data['status'] ?? '',
                'end_time' => date('H:i:s'),
                'recording_URL' => $data['recording_URL'] ?? '',
                'updated_by' => 1
            ]);

            return response('success');

        } catch (\Exception $e) {
            Log::error('CDR Error: '.$e->getMessage());
            return response('error',500);
        }
    }

    public function connectincoming(Request $request)
    {
        $data = $request->json()->all() ?: $request->post();
        Log::error('Connect Incoming Data: '.print_r($data,true));

        try {
            $callUUID = $data['CallUUID'] ?? $data['callUUID'] ?? $data['callUUlD'] ?? '';
            $agentNumber = $data['AgentNumber'] ?? '';

            if (!$callUUID) return response('missing_uuid',400);

            $log = VoxbayCallLog::where('call_uuid',$callUUID)->first();

            if ($log) {
                $log->update([
                    'AgentNumber'=>$agentNumber,
                    'status'=>'connected',
                    'updated_by'=>1
                ]);
                return response('success');
            }

            VoxbayCallLog::create([
                'call_uuid'=>$callUUID,
                'AgentNumber'=>$agentNumber,
                'callerNumber'=>$data['callerNumber'] ?? '',
                'calledNumber'=>$data['calledNumber'] ?? '',
                'type'=>'incoming',
                'status'=>'connected',
                'date'=>date('Y-m-d'),
                'start_time'=>date('H:i:s'),
                'created_by'=>1,
                'updated_by'=>1
            ]);

            return response('success');

        } catch (\Exception $e) {
            Log::error('Connect Incoming Error: '.$e->getMessage());
            return response('error',500);
        }
    }

    public function connectoutgoing(Request $request)
    {
        $data = $request->json()->all() ?: $request->post();
        Log::error('Connect Outgoing Data: '.print_r($data,true));

        try {
            $callUUID = $data['callUUID'] ?? $data['callUUlD'] ?? '';
            $agentNumber = $data['AgentNumber'] ?? $data['extension'] ?? '';

            if (!$callUUID) return response('missing_uuid',400);

            $log = VoxbayCallLog::where('call_uuid',$callUUID)->first();

            if ($log) {
                $log->update([
                    'AgentNumber'=>$agentNumber,
                    'status'=>'connected',
                    'updated_by'=>1
                ]);
                return response('success');
            }

            VoxbayCallLog::create([
                'call_uuid'=>$callUUID,
                'AgentNumber'=>$agentNumber,
                'extensionNumber'=>$data['extension'] ?? $agentNumber,
                'destinationNumber'=>$data['destination'] ?? '',
                'callerid'=>$data['callerid'] ?? '',
                'type'=>'outgoing',
                'status'=>'connected',
                'date'=>date('Y-m-d'),
                'start_time'=>date('H:i:s'),
                'created_by'=>1,
                'updated_by'=>1
            ]);

            return response('success');

        } catch (\Exception $e) {
            Log::error('Connect Outgoing Error: '.$e->getMessage());
            return response('error',500);
        }
    }

    public function disconnectincoming(Request $request)
    {
        $data = $request->json()->all() ?: $request->post();
        Log::error('Disconnect Incoming Data: '.print_r($data,true));

        try {
            $callUUID = $data['callUUID'] ?? $data['callUUlD'] ?? '';
            $agentNumber = $data['AgentNumber'] ?? '';
            $status = $data['status'] ?? $data['callStatus'] ?? '';

            if (!$callUUID) return response('missing_uuid',400);

            $log = VoxbayCallLog::where('call_uuid',$callUUID)->first();

            if ($log) {
                $log->update([
                    'status'=>$status,
                    'AgentNumber'=>$agentNumber,
                    'updated_by'=>1
                ]);
                return response('success');
            }

            VoxbayCallLog::create([
                'call_uuid'=>$callUUID,
                'AgentNumber'=>$agentNumber,
                'type'=>'incoming',
                'status'=>$status,
                'date'=>date('Y-m-d'),
                'start_time'=>date('H:i:s'),
                'created_by'=>1,
                'updated_by'=>1
            ]);

            return response('success');

        } catch (\Exception $e) {
            Log::error('Disconnect Incoming Error: '.$e->getMessage());
            return response('error',500);
        }
    }

    public function disconnectoutgoing(Request $request)
    {
        $data = $request->json()->all() ?: $request->post();
        Log::error('Disconnect Outgoing Data: '.print_r($data,true));

        try {
            $callUUID = $data['callUUID'] ?? $data['CallUUID'] ?? $data['callUUlD'] ?? '';
            $agentNumber = $data['AgentNumber'] ?? $data['extension'] ?? '';
            $status = $data['status'] ?? $data['callStatus'] ?? '';

            if (!$callUUID) return response('missing_uuid',400);

            $log = VoxbayCallLog::where('call_uuid',$callUUID)->first();

            if ($log) {
                $log->update([
                    'status'=>$status,
                    'AgentNumber'=>$agentNumber,
                    'updated_by'=>1
                ]);
                return response('success');
            }

            VoxbayCallLog::create([
                'call_uuid'=>$callUUID,
                'AgentNumber'=>$agentNumber,
                'type'=>'outgoing',
                'status'=>$status,
                'date'=>date('Y-m-d'),
                'start_time'=>date('H:i:s'),
                'created_by'=>1,
                'updated_by'=>1
            ]);

            return response('success');

        } catch (\Exception $e) {
            Log::error('Disconnect Outgoing Error: '.$e->getMessage());
            return response('error',500);
        }
    }

    public function debugRequest(Request $request)
    {
        Log::error('Raw Input: '.file_get_contents('php://input'));
        Log::error('Content-Type: '.$request->header('Content-Type'));
        Log::error('Method: '.$request->method());
        Log::error('GET Data: '.print_r($request->query(), true));
        Log::error('POST Data: '.print_r($request->post(), true));
        Log::error('JSON Data: '.print_r($request->json()->all(), true));

        return response('debug_complete');
    }
}
