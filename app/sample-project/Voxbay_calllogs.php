<?php

namespace App\Controllers\App;

use App\Models\Voxbay_calllogs_model;
use App\Models\Leads_model;
use App\Models\Users_model;

class Voxbay_calllogs extends \App\Controllers\App\AppBaseController
{
    private $voxbay_calllogs_model;

    public function __construct()
    {
        parent::__construct();
        $this->voxbay_calllogs_model = new Voxbay_calllogs_model();
        $this->leads_model           = new Leads_model();
        $this->user_model            = new Users_model();
       
    }

    public function index()
    {
        $this->data['call_logs'] = $this->voxbay_calllogs_model->get()->getResultArray();

        $this->data['page_title'] = 'Voxbay Call Logs';
        $this->data['page_name']  = 'Voxbay_calllogs/index';

        return view('App/index', $this->data);
    }

   public function list($leadId)
{
    // Load the lead
    $lead = $this->leads_model->get(['id' => $leadId], ['code', 'phone'])->getRow();

    // Prepare OR condition using your custom 'get' method format
    $where = [
        'OR' => [
            'destinationNumber' => $lead->code.$lead->phone,
            'calledNumber' => $lead->code.$lead->phone
        ]
    ];

    $this->data['call_logs'] = $this->voxbay_calllogs_model->get($where, [], ['id', 'DESC'])->getResultArray();
    

foreach ($this->data['call_logs'] as $key => $calls) {
    $countryCode = substr($calls['AgentNumber'], 0, 2); // '91'
    $mobileNumber = substr($calls['AgentNumber'], 2);
    $userWhere = [
        'code' => $countryCode,
        'phone' => $mobileNumber,
        'role_id' => 6
    ];
    $user_name = $this->user_model->get($userWhere, ['name'])->getRow()->name ?? '';


    $this->data['call_logs'][$key]['telecaller_name'] = $user_name;
}

$this->data['page_title'] = 'Filtered Voxbay Call Logs';
$this->data['page_name']  = 'Voxbay_calllogs/list';

return view('App/index', $this->data);

}


}
