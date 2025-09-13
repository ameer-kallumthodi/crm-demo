<?php
namespace App\Controllers\App;
use App\Models\Leads_model;
use App\Models\Users_model;
use App\Models\Lead_status_model;
use App\Models\Lead_source_model;
use App\Models\Country_model;
use App\Models\Lead_activity_model;
use App\Models\Call_log_model;

class Call_logs extends AppBaseController
{
    private $users_model;
    private $leads_model;
    private $lead_status_model;
    private $lead_source_model;
    private $country_model;
    private $lead_activity_model;
    private $call_log_model;
    
    public function __construct()
    {
        parent::__construct();
        $this->users_model = new Users_model();
        $this->leads_model = new Leads_model();
        $this->lead_status_model = new Lead_status_model();
        $this->lead_source_model = new Lead_source_model();
        $this->country_model = new Country_model();
        $this->lead_activity_model = new Lead_activity_model();
        $this->call_log_model = new Call_log_model();
    }

    public function index(){
        $logger = service('logger');
        $this->data['tellecallers'] = $this->users_model->get(['role_id' => 6])->getResultArray();
        $this->data['lead_status'] = $this->lead_status_model->get()->getResultArray();
        $this->data['lead_source'] = $this->lead_source_model->get()->getResultArray();
        
        $filter_where = [];
        
        if($this->request->getGet('from_date')!=NULL){
            $filter_where['DATE(date) >='] = date('Y-m-d', strtotime($this->request->getGet('from_date')));
        }
        
        if ($this->request->getGet('to_date')!=NULL){
            $filter_where['DATE(date) <='] = date('Y-m-d', strtotime($this->request->getGet('to_date')));
        }
            
        if($this->request->getGet('telecaller') > 0){
            $filter_where['telecaller_id'] = $this->request->getGet('telecaller');
        } 
 
        $this->data['call_logs'] = $this->call_log_model->get($filter_where,null,['id','desc'])->getResultArray();
        // $logger->error('Database Error: ' . db_connect()->getLastQuery() . ' | Array Log: ' . print_r($this->data['call_logs'], true));
 
        $this->data['total_incoming'] = $this->call_log_model->get(['type' => 1])->getNumRows();
        $this->data['total_outgoing'] = $this->call_log_model->get(['type' => 2])->getNumRows();
        $this->data['total_missed']   = $this->call_log_model->get(['type' => 3])->getNumRows();
        $this->data['total_declined'] = $this->call_log_model->get(['type' => 0])->getNumRows();

         
        $country = $this->country_model->get()->getResultArray();
        $this->data['country_name'] = array_column($country, 'title', 'id');
        
        $lead_source = $this->lead_source_model->get()->getResultArray();
        $this->data['lead_source_list'] = array_column($lead_source,'title','id');
        
        $lead_status = $this->lead_status_model->get()->getResultArray();
        $this->data['lead_status_list'] = array_column($lead_status,'title','id');
        
        $tell_caller = $this->users_model->get(['role_id'=> 6])->getResultArray();
        $this->data['telcaller_list'] = array_column($tell_caller,'name','id');
        
        $this->data['page_title'] = 'Call Logs';
        $this->data['page_name'] = 'Call_logs/index';
        return view('App/index', $this->data);
    }
    
    
    
    
    
    public function update_star_status(){
        
        if($this->request->getPost()){
            $data['star'] = $this->request->getPost('starred');
            $update = $this->call_log_model->edit($data,['id' => $this->request->getPost('id')]);
            if($update){
                echo  json_encode(['status' => 'success', 'message' => 'Updated Successfully']);
            }else{
                echo  json_encode(['status' => 'failed', 'message' => 'Error!']);
            }
        }
    }
    
    
    
    
    
    
    
    
    
    

}
