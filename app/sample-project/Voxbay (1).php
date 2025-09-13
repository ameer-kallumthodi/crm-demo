<?php

namespace App\Controllers\App;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\Users_model;
use App\Models\Leads_model;

class Voxbay extends ResourceController
{
    use ResponseTrait;

    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->users_model = new Users_model();
        $this->leads_model = new Leads_model();

    }

    /**
     * Handle outgoing call events
     * POST /api/voxbay/outgoing-call
     */
    public function outgoingCall()
    {
        try {
            $data = $this->request->getJSON(true);

            if (isset($data['phoneNumber'], $data['telecaller_id'])) {

                $telecaller_id = $data['telecaller_id'];

            
                $user = $this->users_model->get(['id' => $telecaller_id], ['code','phone', 'ext_no'])->getRow();
                $lead = $this->leads_model->get([
                    'id' => $data['lead_id']
                ], ['phone', 'code'])->getRow();


                if (!$user || empty($user->ext_no)) {
                    return $this->respond(['status' => 'error', 'message' => 'Extension not found'], 400);
                }

                $extension = $user->ext_no;
                $phone = $lead->phone;
                $UID_NUMBER = env('UID_NUMBER');
                $UPIN = env('UPIN');
                $CC = $lead->code;
                $destination = $CC.$phone;

                $url = "https://x.voxbay.com/api/click_to_call?id_dept=0&uid={$UID_NUMBER}&upin={$UPIN}&user_no={$extension}&destination={$destination}";
                        

                $response = file_get_contents($url);


                return $this->respond(['status' => 'success', 'url_called' => $url], 200);
            }

            return $this->respond(['status' => 'error', 'message' => 'Missing fields'], 400);

        } catch (\Exception $e) {
            log_message('error', 'Voxbay Outgoing Call Error: ' . $e->getMessage());
            return $this->respond(['status' => 'error', 'message' => 'Internal Server error'], 500);
        }
    }
}