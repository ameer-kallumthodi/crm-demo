<?php
namespace App\Controllers\App;
use App\Models\Lead_status_model;
use App\Models\Lead_source_model;
use App\Models\Leads_model;
use App\Models\University_model;
use App\Models\Users_model;


class University_report extends AppBaseController
{
    private $teams_model;
    private $lead_status_model;
    private $lead_source_model;
    private $leads_model;
    private $users_model;

    public function __construct()
    {
        parent::__construct();
        $this->university_model = new University_model();
        $this->lead_status_model = new Lead_status_model();  
        $this->lead_source_model = new Lead_source_model();
        $this->leads_model = new Leads_model();
        $this->users_model = new Users_model();

    }

    public function index(){
        
        $this->data['lead_status'] = $this->lead_status_model->get()->getResultArray();
        $this->data['lead_source'] = $this->lead_source_model->get()->getResultArray();
        $this->data['university_list'] = $this->university_model->get()->getResultArray();
        $this->data['telecaller'] = $this->users_model->get(['role_id'=> 6])->getResultArray();
        
        $filter_where = [];
        
        if (!empty($this->request->getGet('from_date')) && !empty($this->request->getGet('to_date'))){
            
            $filter_where = [
                    'created_at >=' => $this->request->getGet('from_date'). ' 00:00:00',
                    'created_at <=' => $this->request->getGet('to_date'). ' 23:59:59'
                ];
            if($this->request->getGet('university_id') > 0){
                $filter_where['university_id'] = $this->request->getGet('university_id');
            }
            
             if($this->request->getGet('telecaller_id') > 0){
                $filter_where['telecaller_id'] = $this->request->getGet('telecaller_id');
            } 
        $this->data['leads'] = $this->leads_model->get($filter_where,null,['id','desc'])->getResultArray();
            // log_last_query();
        }else
        {
            if($this->request->getGet('university_id') > 0){
                $filter_where['university_id'] = $this->request->getGet('university_id');
            }
            
             if($this->request->getGet('telecaller_id') > 0){
                $filter_where['telecaller_id'] = $this->request->getGet('telecaller_id');
            }  
            $this->data['leads'] = $this->leads_model->get($filter_where,null,['id','desc'])->getResultArray();
        }
        
        
        $lead_source = $this->lead_source_model->get()->getResultArray();
        $this->data['lead_source_list'] = array_column($lead_source,'title','id');
        
        $lead_status = $this->lead_status_model->get()->getResultArray();
        $this->data['lead_status_list'] = array_column($lead_status,'title','id');
        
        $university_list = $this->university_model->get()->getResultArray();
        $this->data['universities'] = array_column($university_list,'title','id');
        
        $tell_caller = $this->users_model->get(['role_id'=> 6])->getResultArray();
        $this->data['telcaller_list'] = array_column($tell_caller,'name','id');
        
        $leadStatuses = [];
        foreach ($this->data['lead_status'] as $status) {
            $leadStatuses[$status['id']] = [
                'status_name' => $status['title'],
                'count' => 0 
            ]; 
        }
        
        foreach ($this->data['leads'] as $lead) {
            $statusId = $lead['lead_status_id'];
            if (array_key_exists($statusId, $leadStatuses)) {
                $leadStatuses[$statusId]['count']++;
            }
        }
        
        $this->data['status_count'] = $leadStatuses;
        $this->data['total_count'] = count($this->data['leads']);
        
        $this->data['page_title'] = 'University Report';
        $this->data['page_name'] = 'University_report/index';
        return view('App/index', $this->data);
    }


}
