<?php
namespace App\Controllers\App;
use App\Controllers\App\AppBaseController;
use App\Models\Lead_status_model;
use App\Models\Lead_source_model;
use App\Models\Leads_model;
use App\Models\Country_model;
use App\Models\University_model;
use App\Models\Admissions_model;
class Dashboard extends AppBaseController
{
    private $lead_status_model;
    private $lead_source_model;
    private $leads_model;
    private $country_model;
    private $university_model;
    private $admissions_model;
    public function __construct()
    {
        parent::__construct();
        $this->lead_status_model = new Lead_status_model();
        $this->lead_source_model = new Lead_source_model();
        $this->leads_model = new Leads_model();
        $this->country_model = new Country_model();
        $this->university_model = new University_model();
        $this->admissions_model = new Admissions_model();
    }
    
    public function index()
    {
        if (is_academic_assistant()) {
            $this->_getAcademicAssistantData();
        }else if (is_admission_counsellor()){
            $this->_getAcademicAssistantData();
        } else {
            $this->_getLeadsData();
        }
    
        return view('App/index', $this->data);
    }
    
    private function _getLeadsData()
    {
        $this->data['leads'] = [];
    
        if (is_admin()) {
            $this->data['leads'] = $this->leads_model->get([], ['id', 'is_converted', 'lead_status_id'])->getResultArray();
        } elseif (is_telecaller() && !is_admin()) {
            $this->data['leads'] = $this->leads_model->get(['telecaller_id' => get_user_id()], ['id', 'is_converted', 'lead_status_id'])->getResultArray();
        }
    
        $total = 0;
        $not_converted = 0;
        $converted = 0;
        $follow_up = 0;
    
        foreach ($this->data['leads'] as $lead) {
            $total++;
            if ($lead['is_converted'] == 1) {
                $converted++;
            } else {
                $not_converted++;
            }
            if ($lead['lead_status_id'] == 3) {
                $follow_up++;
            }
        }
    
        $this->data['total'] = $total;
        $this->data['not_converted'] = $not_converted;
        $this->data['converted'] = $converted;
        $this->data['follow_up'] = $follow_up;
    
        $this->data['page_title'] = 'Dashboard';
        $this->data['page_name'] = 'Dashboard/index';
    }
    
    private function _getAcademicAssistantData()
    {
        $where = [];
        if(is_academic_assistant()){
            $where['academic_assistant_id'] = get_user_id();
        }
        $this->data['admissions'] = $this->admissions_model->get($where)->getResultArray();
        $total = 0;
        $documents_collected = 0;
        $not_documents_collected = 0;
        $pending = 0;
        foreach ($this->data['admissions'] as $lead) {
            $total++;
            if ($lead['is_documents_collected'] == 1) {
                $documents_collected++;
            } else {
                $not_documents_collected++;
            }
            if ($lead['candidate_status_id'] == 1) {
                $pending++;
            }
        }
        
        $this->data['total'] = $total;
        $this->data['documents_collected'] = $documents_collected;
        $this->data['not_documents_collected'] = $not_documents_collected;
        $this->data['pending'] = $pending;
    
        $this->data['page_title'] = 'Dashboard';
        $this->data['page_name'] = 'Dashboard/academic_assistant';
    }


    // public function index(){
    //     $this->data['leads'] = [];
    //     if(is_admin()){
    //         $this->data['leads'] = $this->leads_model->get()->getResultArray();
    //     }
        
    //     if(is_telecaller() && !is_admin()){
    //         $this->data['leads'] = $this->leads_model->get(['telecaller_id' => get_user_id()])->getResultArray();
    //     }
    //     // print_r(get_role_id());die();
    //     // abid begin
    //     $total = 0;
    //     $not_converted = 0;
    //     $converted = 0;
    //     $follow_up = 0;
         
    //     foreach($this->data['leads'] as $lead){
    //         $total += 1;
            
    //         if($lead['is_converted'] == 1){
    //             $converted += 1;
    //         }else{
    //             $not_converted += 1;
    //         }
            
    //         if($lead['lead_status_id'] == 3){
    //             $follow_up += 1;
    //         }
    //     }
        
        
    //     $this->data['total'] = $total;
    //     $this->data['not_converted'] = $not_converted;
    //     $this->data['converted'] = $converted;
    //     $this->data['follow_up'] = $follow_up;
        
    //     if(is_academic_assistant()){
    //         $page_name = 'Dashboard/academic_assistant';
    //     }else{
    //         $page_name = 'Dashboard/index';
    //     }
        
    //     $this->data['page_title'] = 'Dashboard';
    //     $this->data['page_name'] = $page_name;
    //     return view('App/index', $this->data);
    // }
}
