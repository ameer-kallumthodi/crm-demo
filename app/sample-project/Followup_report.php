<?php
namespace App\Controllers\App;
use App\Models\Leads_model;
use App\Models\Users_model;
use App\Models\Lead_status_model;
use App\Models\Lead_source_model;
use App\Models\Country_model;
use App\Models\Lead_activity_model;
class Followup_report extends AppBaseController
{
    private $users_model;
    private $leads_model;
    private $lead_status_model;
    private $lead_source_model;
    private $country_model;
    private $lead_activity_model;
    public function __construct()
    {
        parent::__construct();
        $this->users_model = new Users_model();
        $this->leads_model = new Leads_model();
        $this->lead_status_model = new Lead_status_model();
        $this->lead_source_model = new Lead_source_model();
        $this->country_model = new Country_model();
        $this->lead_activity_model = new Lead_activity_model();
    }

    public function index(){
        $this->data['tellecallers'] = $this->users_model->get(['role_id' => 6])->getResultArray();
        $this->data['lead_status'] = $this->lead_status_model->get()->getResultArray();
        $this->data['lead_source'] = $this->lead_source_model->get()->getResultArray();
        
        if (!empty($this->request->getGet('from_date')) && !empty($this->request->getGet('to_date'))){
            
            $filter_where = [
                    'created_at >=' => $this->request->getGet('from_date'). ' 00:00:00',
                    'created_at <=' => $this->request->getGet('to_date'). ' 23:59:59'
                ];
                
            if($this->request->getGet('lead_status') > 0){
                $filter_where['lead_status_id'] = $this->request->getGet('lead_status');
            }
            
            if($this->request->getGet('lead_source') > 0){
                $filter_where['lead_source_id'] = $this->request->getGet('lead_source');
            } 
            
            if($this->request->getGet('telecaller') > 0){
                $filter_where['telecaller_id'] = $this->request->getGet('telecaller');
            } 
            
            // if($this->request->getGet('country_id') > 0){
            //     $filter_where['country_id'] = $this->request->getGet('country_id');
            // } 
            
            // if($this->request->getGet('university_id') > 0){
            //     $filter_where['university_id'] = $this->request->getGet('university_id');
            // } 
            
            $filter_where['lead_status_id'] = 3;

            $this->data['list_items'] = $this->leads_model->get($filter_where,null,['id','desc'])->getResultArray();
            // log_last_query();
        }else{
            $this->data['list_items'] = $this->leads_model->get(['lead_status_id' => 3],null,['id','desc'])->getResultArray();
        }
        
        
        $country = $this->country_model->get()->getResultArray();
        $this->data['country_name'] = array_column($country, 'title', 'id');
        
        $lead_source = $this->lead_source_model->get()->getResultArray();
        $this->data['lead_source_list'] = array_column($lead_source,'title','id');
        
        $lead_status = $this->lead_status_model->get()->getResultArray();
        $this->data['lead_status_list'] = array_column($lead_status,'title','id');
        
        $tell_caller = $this->users_model->get(['role_id'=> 6])->getResultArray();
        $this->data['telcaller_list'] = array_column($tell_caller,'name','id');
        
            
   
        
        $this->data['page_title'] = 'Followups Report';
        $this->data['page_name'] = 'Followup_report/index';
        return view('App/index', $this->data);
    }

}
