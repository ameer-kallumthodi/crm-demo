<?php
namespace App\Controllers\App;
use App\Models\Lead_status_model;
use App\Models\Lead_source_model;
use App\Models\Leads_model;
use App\Models\Country_model;
use App\Models\University_model;
use App\Models\Users_model;

class Lead_report extends AppBaseController
{
    private $users_model;
    private $lead_status_model;
    private $lead_source_model;
    private $leads_model;
    private $country_model;
    private $university_model;
    public function __construct()
    {
        parent::__construct(); 
        $this->lead_status_model = new Lead_status_model();
        $this->lead_source_model = new Lead_source_model();
        $this->leads_model = new Leads_model();
        $this->country_model = new Country_model();
        $this->university_model = new University_model();
        $this->users_model = new Users_model();
    }

    public function index(){
        // $this->data['list_items'] = $this->lead_report_model->get()->getResultArray();
        $this->data['lead_status'] = $this->lead_status_model->get()->getResultArray();
        $this->data['lead_source'] = $this->lead_source_model->get()->getResultArray();
        $this->data['countrys'] = $this->country_model->get()->getResultArray();
        $this->data['university_list'] = $this->university_model->get()->getResultArray();
        
        $this->data['telecaller'] = $this->users_model->get(['role_id'=> 6])->getResultArray();

        
        // Calculate the date 7 days ago
        $sevenDaysAgo = strtotime(date('Y-m-d')) - (7 * 24 * 60 * 60); // Subtract 7 days in seconds

        $this->data['from_date'] = date('Y-m-d', $sevenDaysAgo);
        $this->data['to_date'] = date('Y-m-d');
        
        $filter_where = []; // Initialize the $filter_where array

        if (!empty($this->request->getGet('from_date'))) {
            $filter_where['date(created_at) >='] = $this->request->getGet('from_date');
        } else {
            $filter_where['date(created_at) >='] = $this->data['from_date'];
        }
        
        if (!empty($this->request->getGet('to_date'))) {
            $filter_where['date(created_at) <='] = $this->request->getGet('to_date');
        } else {
            $filter_where['date(created_at) <='] = $this->data['to_date'];
        }

            
        if($this->request->getGet('lead_status') > 0){
            $filter_where['lead_status_id'] = $this->request->getGet('lead_status');
        }
        
        if($this->request->getGet('lead_source') > 0){
            $filter_where['lead_source_id'] = $this->request->getGet('lead_source');
        } 
        
        if($this->request->getGet('country_id') > 0){
            $filter_where['country_id'] = $this->request->getGet('country_id');
        } 
        
        if($this->request->getGet('university_id') > 0){
            $filter_where['university_id'] = $this->request->getGet('university_id');
        } 
        
         if($this->request->getGet('telecaller_id') > 0){
                $filter_where['telecaller_id'] = $this->request->getGet('telecaller_id');
            } 
        
        $this->data['leads'] = $this->leads_model->get($filter_where,null,['id','desc'])->getResultArray();
        // log_last_query();

        $lead_source = $this->lead_source_model->get()->getResultArray();
        $this->data['lead_source_list'] = array_column($lead_source,'title','id');
        
        $lead_status = $this->lead_status_model->get()->getResultArray();
        $this->data['lead_status_list'] = array_column($lead_status,'title','id');
        
        $country = $this->country_model->get()->getResultArray();
        $this->data['country_name'] = array_column($country, 'title', 'id');
        
        $university = $this->university_model->get()->getResultArray();
        $this->data['university_name'] = array_column($university, 'title', 'id');
        
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
        $this->data['page_title'] = 'Lead Report';
        $this->data['page_name'] = 'Lead_report/index';
        return view('App/index', $this->data);
    }


}
