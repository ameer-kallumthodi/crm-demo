<?php
namespace App\Controllers\App;
use App\Models\Users_model;
use App\Models\Leads_model;
use App\Models\Lead_status_model;
use App\Models\Course_model;
use App\Models\Country_model;


class Tellecaller_report extends AppBaseController
{

    private $users_model;
    private $lead_status_model;
    private $course_model;
    private $country_model;



    public function __construct()
    {
        parent::__construct();
        $this->users_model = new Users_model();
        $this->leads_model = new Leads_model();
        $this->lead_status_model = new Lead_status_model();
        $this->course_model = new Course_model();
        $this->country_model = new Country_model();
    }

    public function index(){
        
        if (!empty($this->request->getGet('telecaller_id'))){
            
          if (!empty($this->request->getGet('from_date'))){
            
            $filter_where['created_at >=']  = $this->request->getGet('from_date'). ' 00:00:00';
          
          }
          if (!empty($this->request->getGet('to_date'))){
            
            $filter_where['created_at <=']  = $this->request->getGet('to_date'). ' 23:59:59';

          }

                
            if($this->request->getGet('telecaller_id') > 0){
                $filter_where['telecaller_id']  = $this->request->getGet('telecaller_id');
            }   
            
            
          
            $this->data['telecallers']          = $this->leads_model->get($filter_where,null,['id','desc'])->getResultArray();
            
            
            
            $filter_where['is_converted']       = 1;
            $this->data['converted']            = $this->leads_model->get($filter_where)->getResultArray();  
            
            
            $this->data['Total']                = count($this->data['telecallers']);
            $this->data['converted_count']      = count($this->data['converted']);
            $this->data['pending']              = $this->data['Total'] - $this->data['converted_count']; 
            
        }else{
            
            $db                                 = \Config\Database::connect();
            $sql                                = "SELECT * FROM leads WHERE telecaller_id != ''";
            $query                              = $db->query($sql);
            $this->data['telecallers']          = $query->getResultArray();
            
            $filter_where['is_converted']       = 1;
            $this->data['converted']            = $this->leads_model->get($filter_where)->getResultArray(); 
            
            $this->data['Total']                = count($this->data['telecallers']);
            $this->data['converted_count']      = count($this->data['converted']);
            $this->data['pending']              = $this->data['Total'] - $this->data['converted_count']; 
        }
        
        
            $lead_status = $this->lead_status_model->get()->getResultArray();
            $this->data['lead_status_list'] = array_column($lead_status,'title','id');
            
            $course = $this->course_model->get()->getResultArray();
            $this->data['course_name'] = array_column($course, 'title', 'id');
             
             
            $country = $this->country_model->get()->getResultArray();
            $this->data['country_name'] = array_column($country, 'title', 'id');
            
            
        
        
        // Calculate the date 7 days ago
        // $sevenDaysAgo = strtotime(date('Y-m-d')) - (7 * 24 * 60 * 60); // Subtract 7 days in seconds

        // $this->data['from_date'] = date('Y-m-d', $sevenDaysAgo);
        // $this->data['to_date'] = date('Y-m-d');
        
        // $filter_where = []; // Initialize the $filter_where array

        // if (!empty($this->request->getGet('from_date'))) {
        //     $filter_where['date(created_at) >='] = $this->request->getGet('from_date');
        // } else {
        //     $filter_where['date(created_at) >='] = $this->data['from_date'];
        // }
        
        // if (!empty($this->request->getGet('to_date'))) {
        //     $filter_where['date(created_at) <='] = $this->request->getGet('to_date');
        // } else {
        //     $filter_where['date(created_at) <='] = $this->data['to_date'];
        // }
        
        // if($this->request->getGet('telecaller_id') > 0){
        //     $filter_where['telecaller_id']  = $this->request->getGet('telecaller_id');
        // } 
        
        // $this->data['telecallers'] = $this->leads_model->get($filter_where)->getResultArray();
        
        $this->data['telecallers_list'] = $this->users_model->get(['role_id'=>6])->getResultArray();
        $this->data['page_title'] = 'Academic Counselor Report';
        $this->data['page_name'] = 'Tellecaller_report/index';
        return view('App/index', $this->data);
    }


}
