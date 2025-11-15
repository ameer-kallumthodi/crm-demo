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
        try {
            // Handle both JSON and form data
            $data = [];
            if ($request->isJson() && $request->json()) {
                $data = $request->json()->all();
            } else {
                $data = $request->all();
            }

            if (isset($data['CallUUID'])) {
                if (isset($data['calledNumber']) && isset($data['callerNumber'])) {
                    $this->handleIncomingCallLanded($data);
                }
            }

            return response('success', 200);
        } catch (\Exception $e) {
            Log::error('Voxbay Call Center Bridging Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response('error: ' . $e->getMessage(), 500);
        }
    }

    public function outgoingCall(Request $request)
    {
        try {
            $data = $request->json()->all();

            if (isset($data['extension']) && isset($data['destination'])) {
                $this->handleOutgoingCallLanded($data);
            } elseif (isset($data['callerNumber']) && isset($data['calledNumber'])) {
                $this->handleIncomingCallLanded($data);
            }

            return response('success', 200);
        } catch (\Exception $e) {
            Log::error('Voxbay Outgoing Call Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
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

            $this->handleClickToCall($params);

            return response(['status'=>'success','message'=>'Click to call initiated'], 200);

        } catch (\Exception $e) {
            Log::error('Voxbay Click to Call Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response(['error' => 'Internal server error'], 500);
        }
    }

    private function handleIncomingCallLanded($incoming)
    {
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
            
            // Validate required fields
            if (empty($callData['call_uuid'])) {
                return false;
            }
            
            VoxbayCallLog::create($callData);
            
            return true;
            
        } catch (QueryException $e) {
            Log::error('Voxbay Incoming Call Database Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'sql_state' => $e->errorInfo[0] ?? 'N/A',
                'sql_message' => $e->errorInfo[2] ?? 'N/A'
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Voxbay Incoming Call Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    private function handleOutgoingCallLanded($data)
    {
        try {
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
        } catch (QueryException $e) {
            Log::error('Voxbay Outgoing Call Database Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'sql_state' => $e->errorInfo[0] ?? 'N/A',
                'sql_message' => $e->errorInfo[2] ?? 'N/A'
            ]);
        } catch (\Exception $e) {
            Log::error('Voxbay Outgoing Call Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function handleClickToCall($params)
    {
        // Handle click to call logic
    }

    public function incomingcdrpush(Request $request)
    {
        $data = $request->json()->all() ?: $request->post();

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
            Log::error('Voxbay Incoming CDR Push Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response('error',500);
        }
    }

    public function outgoingcdrpush(Request $request)
    {
        $data = $request->json()->all() ?: $request->post();

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
            Log::error('Voxbay Outgoing CDR Push Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response('error',500);
        }
    }

    public function connectincoming(Request $request)
    {
        $data = $request->json()->all() ?: $request->post();

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
            Log::error('Voxbay Connect Incoming Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response('error',500);
        }
    }

    public function connectoutgoing(Request $request)
    {
        $data = $request->json()->all() ?: $request->post();

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
            Log::error('Voxbay Connect Outgoing Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response('error',500);
        }
    }

    public function disconnectincoming(Request $request)
    {
        $data = $request->json()->all() ?: $request->post();

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
            Log::error('Voxbay Disconnect Incoming Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response('error',500);
        }
    }

    public function disconnectoutgoing(Request $request)
    {
        $data = $request->json()->all() ?: $request->post();

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
            Log::error('Voxbay Disconnect Outgoing Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response('error',500);
        }
    }

    public function debugRequest(Request $request)
    {
        return response('debug_complete');
    }
}
