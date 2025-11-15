<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\VoxbayCallLog;

class VoxbayCallController extends Controller
{
    public function callcenterbridging(Request $request)
    {
        Log::error(print_r($request->all(), true));

        try {
            $data = $request->json()->all();
            Log::error('Incoming Call Data: ' . json_encode($data));

            if (isset($data['CallUUID'])) {
                if (isset($data['calledNumber']) && isset($data['callerNumber'])) {
                    $this->handleIncomingCallLanded($data);
                }
            }

            return response('success', 200);
        } catch (\Exception $e) {
            Log::error('Voxbay Incoming Call Error: '.$e->getMessage());
            return response('error', 500);
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
        VoxbayCallLog::create([
            'callerNumber' => $incoming['callerNumber'] ?? '',
            'calledNumber' => $incoming['calledNumber'] ?? '',
            'call_uuid'    => $incoming['CallUUID'] ?? '',
            'type'         => 'incoming',
            'date'         => date('Y-m-d'),
            'start_time'   => date('H:i:s'),
            'callerid'     => $incoming['callerid'] ?? '',
            'created_by'   => 1,
            'updated_by'   => 1,
        ]);
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
