<?php
namespace App\Controllers\App;
use App\Models\Leads_model;
use App\Models\Candidate_status_model;
use App\Models\Country_model;
use App\Models\University_model;
use App\Models\Users_model;


class Candidate_report extends AppBaseController
{
    private $users_model;
    private $leads_model;
    private $candidate_status_model;
    private $country_model;
    private $university_model;

    
    public function __construct()
    {
        parent::__construct(); 
        $this->leads_model = new Leads_model();
        $this->candidate_status_model = new Candidate_status_model();
        $this->country_model = new Country_model();
        $this->university_model = new University_model();
        $this->users_model = new Users_model();

    }

    public function index(){

        $this->data['candidate_status'] = $this->candidate_status_model->get()->getResultArray();
        $this->data['countrys'] = $this->country_model->get()->getResultArray();
        $this->data['university_list'] = $this->university_model->get()->getResultArray();
        
        $this->data['telecaller'] = $this->users_model->get(['role_id'=> 6])->getResultArray();

        // Calculate the date 7 days ago
        $sevenDaysAgo = strtotime(date('Y-m-d')) - (7 * 24 * 60 * 60); // Subtract 7 days in seconds

        $this->data['from_date'] = date('Y-m-d', $sevenDaysAgo);
        $this->data['to_date'] = date('Y-m-d');
        
        $filter_where = []; // Initialize the $filter_where array
        
        $filter_where['is_converted'] = 1;
        
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
        
        if($this->request->getGet('candidate_status') > 0){
            $filter_where['candidate_status_id'] = $this->request->getGet('candidate_status');
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
        

        $this->data['candidates'] = $this->leads_model->get($filter_where,null,['id','desc'])->getResultArray();
        
        $country                             = $this->country_model->get()->getResultArray();
        $this->data['country_name']          = array_column($country, 'title', 'id');
        
        $candidate_status  = $this->candidate_status_model->get()->getResultArray();
        $this->data['candidate_status_list'] = array_column($candidate_status,'title','id');
        
         $tell_caller = $this->users_model->get(['role_id'=> 6])->getResultArray();
        $this->data['telcaller_list'] = array_column($tell_caller,'name','id');
        
        $candidateStatuses = [];
        foreach ($this->data['candidate_status'] as $status) {
            $candidateStatuses[$status['id']] = [
                'status_name' => $status['title'],
                'count' => 0 
            ]; 
        }
        
        foreach($this->data['candidates'] as $candidate) {
            $statusId = $candidate['candidate_status_id'];
            if (array_key_exists($statusId, $candidateStatuses)) {
                $candidateStatuses[$statusId]['count']++;
            }
        }
        
        $this->data['status_count'] = $candidateStatuses;
        $this->data['total_count'] = count($this->data['candidates']);
        $this->data['page_title'] = 'Admissions Report';
        $this->data['page_name'] = 'Candidate_report/index';
        return view('App/index', $this->data);
    }


}
