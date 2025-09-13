<?php
namespace App\Controllers\App;
use App\Models\Leads_model;
use App\Models\Users_model;
use App\Models\Candidate_status_model;
use App\Models\Lead_source_model;
use App\Models\Candidate_activity_model;
use App\Models\Country_model;
use App\Models\University_model;
use App\Models\Course_model;

class Candidates extends AppBaseController
{
    private $users_model;
    private $leads_model;
    private $candidate_status_model;
    private $lead_source_model;
    private $candidate_activity_model;
    private $country_model;
    private $course_model;

    public function __construct()
    {
        parent::__construct();
        $this->users_model = new Users_model();
        $this->leads_model = new Leads_model();
        $this->candidate_status_model = new Candidate_status_model();
        $this->lead_source_model = new Lead_source_model();
        $this->candidate_activity_model = new Candidate_activity_model();
        $this->country_model = new Country_model();
        $this->university_model = new University_model();
        $this->course_model = new Course_model();

        
    }

    public function index(){

        

        
        if (!empty($this->request->getGet('from_date')) && !empty($this->request->getGet('to_date'))){
            
            $filter_where = [
                    'created_at >=' => $this->request->getGet('from_date'). ' 00:00:00',
                    'created_at <=' => $this->request->getGet('to_date'). ' 23:59:59'
                ];
                
                
            if($this->request->getGet('candidate_status') > 0){
                $filter_where['candidate_status_id'] = $this->request->getGet('candidate_status');
            }
            
            if($this->request->getGet('university') > 0){
                $filter_where['university_id'] = $this->request->getGet('university');
            }
            
            if($this->request->getGet('university') > 0){
                $filter_where['university_id'] = $this->request->getGet('university');
            }
            
              if($this->request->getGet('course_id') > 0){
                $filter_where['course_id'] = $this->request->getGet('course_id');
            } 
            
            if($this->request->getGet('country') > 0){
                $filter_where['country_id']   = $this->request->getGet('country');
            }
            
             if($this->request->getGet('telecaller_id') > 0){
                $filter_where['telecaller_id'] = $this->request->getGet('telecaller_id');
            } 
            
            if($this->request->getGet('course_id') > 0){
                $filter_where['course_id'] = $this->request->getGet('course_id');
            } 
            
                $filter_where['is_converted'] = 1;
                
            
            $this->data['list_items']  = $this->leads_model->get($filter_where,null,['id','desc'])->getResultArray();
        }else{
            $this->data['list_items']   = $this->leads_model->get(['is_converted' => 1],null,['id','desc'])->getResultArray();
        }
        

        $country                             = $this->country_model->get()->getResultArray();
        $this->data['country_name']          = array_column($country, 'title', 'id');
        
        
        $course                              = $this->course_model->get()->getResultArray();
        $this->data['course_name']           = array_column($course, 'title', 'id');
        
        $lead_source                         = $this->lead_source_model->get()->getResultArray();
        $this->data['lead_source_list']      = array_column($lead_source,'title','id');
        
        $candidate_status                    = $this->candidate_status_model->get()->getResultArray();
        $this->data['candidate_status_list'] = array_column($candidate_status,'title','id');
        
        $tell_caller                         = $this->users_model->get(['role_id'=> 6])->getResultArray();
        $this->data['telcaller_list']        = array_column($tell_caller,'name','id');
        
        
        $this->data['lead_source']           = $this->lead_source_model->get()->getResultArray();
        $this->data['candidate_status']      = $this->candidate_status_model->get()->getResultArray();
        
        $this->data['lead_source']           = $this->lead_source_model->get()->getResultArray();
        $this->data['candidate_status']      = $this->candidate_status_model->get()->getResultArray();
        
        $this->data['countrys']              = $this->country_model->get()->getResultArray();
        $this->data['universitys']           = $this->university_model->get()->getResultArray();
        $this->data['telecaller']            = $this->users_model->get(['role_id'=> 6])->getResultArray();
        $this->data['course_list']           = $this->course_model->get()->getResultArray();



        $this->data['page_title']            = 'Admissions';
        $this->data['page_name']             = 'Candidate_list/index';
        return view('App/index', $this->data);
    }
    
    public function ajax_add(){
        $this->data['tele_callers'] = $this->users_model->get(['role_id' => 6])->getResultArray();
        $this->data['candidate_status'] = $this->candidate_status_model->get()->getResultArray();
        $this->data['lead_source'] = $this->lead_source_model->get()->getResultArray();
        $this->data['country_list'] = $this->country_model->get()->getResultArray();
        $this->data['university_list'] = $this->country_model->get()->getResultArray();
        $this->data['country_code'] = get_country_code();
        $this->data['course_list'] = $this->course_model->get()->getResultArray();

        
        echo view('App/Candidate_list/ajax_add', $this->data);
    }

    public function add(){
        if ($this->request->getMethod() === 'post'){
            
            $code = $this->request->getPost('code');
            $phone = $this->request->getPost('phone');
            $email = $this->request->getPost('email');
            
            $check_phone_dublication = $this->leads_model->get(['code'=>$code,'phone'=>$phone])->getNumRows();
            $check_email_dublication = $this->leads_model->get(['email'=>$email])->getNumRows();
            
            if($check_phone_dublication == 0 && $check_email_dublication == 0){
                
                $data = 
                [
                'title' => $this->request->getPost('title'),
                'gender' => $this->request->getPost('gender') ? 'male' : 'female', 
                'age' => $this->request->getPost('age'), 
                'code' => $code,
                'phone' => $phone,
                'whatsapp' => $this->request->getPost('whatsapp'),
                'email' => $email, 
                'qualification' => $this->request->getPost('qualification'), 
                'is_converted' => 1, 
                'followup_date' => date('Y-m-d',strtotime($this->request->getPost('followup_date'))), 
                'country_id' => $this->request->getPost('country_id'),
                'remarks' => $this->request->getPost('remarks'), 
                'interest_status' => $this->request->getPost('interest_status'), 
                'candidate_status_id' => $this->request->getPost('candidate_status_id'), 
                'lead_source_id' => $this->request->getPost('lead_source_id'), 
                'address' => $this->request->getPost('address'),
                'telecaller_id' => $this->request->getPost('telecaller_id'), 
                'course_id' => $this->request->getPost('course_id'), 
                'place' => $this->request->getPost('place'), 
                'created_by' => get_user_id(),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_by' => get_user_id(),
                'updated_at' => date('Y-m-d H:i:s')
         
            ];
               
            $candidate_id = $this->leads_model->add($data);
            if($candidate_id){
                $candidate_activity_data = [
                    'candidate_status_id' => $this->request->getPost('candidate_status_id'),
                    'candidate_id' => $candidate_id, 
                    'remarks' => $this->request->getPost('remarks'), 
                    'created_by' => get_user_id(),
                    'updated_by' => get_user_id(),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $lead_activity_id = $this->candidate_activity_model->add($candidate_activity_data);
            } 
            
            if ($candidate_id){
                session()->setFlashdata('message_success', "Leads Added Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
                
            }else{
                session()->setFlashdata('message_danger', "Lead Already Exists");
            }
            
         
        }
        return redirect()->to(base_url('app/candidates/index'));
    }

    public function ajax_edit($id){
        
        $this->data['tele_callers'] = $this->users_model->get(['role_id'=> 6])->getResultArray();
        $this->data['candidate_status'] = $this->candidate_status_model->get()->getResultArray();
        $this->data['lead_source'] = $this->lead_source_model->get()->getResultArray();
        $this->data['country_list'] = $this->country_model->get()->getResultArray();
        $this->data['university_list'] = $this->country_model->get()->getResultArray();
        $this->data['country_code'] = get_country_code();
        $this->data['course_list'] = $this->course_model->get()->getResultArray();
 
        $this->data['edit_data'] = $this->leads_model->get(['id' => $id])->getRowArray();
        
        echo view('App/Candidate_list/ajax_edit', $this->data);
    }

    public function edit($id){
        
        if ($this->request->getMethod() === 'post'){
            
            $code = $this->request->getPost('code');
            $phone = $this->request->getPost('phone');
            $email = $this->request->getPost('email');
            
             $check_phone_dublication = $this->users_model->get(['code' => $code,'phone' => $phone ,'id !=' => $id])->getNumRows();
             $check_email_dublication = $this->users_model->get(['email' => $email,'id !=' => $id])->getNumRows();
             
             if($check_phone_dublication ==0 && $check_email_dublication ==0) {
                $data = [
                    
                'title' => $this->request->getPost('title'),
                'gender' => $this->request->getPost('gender') ? 'male' : 'female', 
                'age' => $this->request->getPost('age'), 
                'code' => $code,
                'phone' => $phone,
                'whatsapp' => $this->request->getPost('whatsapp'), 
                'email' => $email,
                'qualification' => $this->request->getPost('qualification'), 
                'country_id' => $this->request->getPost('country_id'),
                'interest_status' => $this->request->getPost('interest_status'), 
                'candidate_status_id' => $this->request->getPost('candidate_status_id'), 
                'lead_source_id' => $this->request->getPost('lead_source_id'), 
                'address' => $this->request->getPost('address'), 
                'telecaller_id' => $this->request->getPost('telecaller_id'), 
                'course_id' => $this->request->getPost('course_id'), 
                'place' => $this->request->getPost('place'), 
                'updated_by' => get_user_id(),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $response = $this->leads_model->edit($data, ['id' => $id]);
            if ($response){
                session()->setFlashdata('message_success', "Leads Updated Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }else{
            session()->setFlashdata('message_danger', "User Already Exists"); 
        }
            

        }
        return redirect()->to(base_url('app/candidates/index'));
    }
    
    public function ajax_candidate_status($id){
        
        $this->data['edit_data'] = $this->leads_model->get(['id' => $id])->getRowArray();
        $this->data['candidate_status'] = $this->candidate_status_model->get()->getResultArray();
        $this->data['candidate_id'] = $id;
        
        echo view('App/Candidate_list/ajax_candidate_status', $this->data);
    }
    
    public function update_candidate_status(){
        if ($this->request->getMethod() === 'post'){
            
            $candidate_id = $this->request->getPost('candidate_id');
            $candidate_status_id = $this->request->getPost('candidate_status_id');
            
            $data = [
                'candidate_id' => $candidate_id, 
                'candidate_status_id' => $candidate_status_id,
                'remarks' => $this->request->getPost('remarks'), 
                'updated_by' => get_user_id(),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $candidate_activity_id = $this->candidate_activity_model->add($data);
            
            $candidate_data = [
                'candidate_status_id' => $candidate_status_id, 
                'remarks' => $this->request->getPost('remarks'), 
                'updated_by' => get_user_id(),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $this->leads_model->edit($candidate_data, ['id' => $candidate_id]);
            
            if ($candidate_activity_id){
                session()->setFlashdata('message_success', "Lead Status Updated Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/candidates/index'));
    }
    

    public function ajax_view($id){

        $country = $this->country_model->get()->getResultArray();
        $this->data['country_name'] = array_column($country, 'title', 'id');
        
        $lead_source = $this->lead_source_model->get()->getResultArray();
        $this->data['lead_source_list'] = array_column($lead_source,'title','id');
        
        $candidate_status = $this->candidate_status_model->get()->getResultArray();
        $this->data['candidate_status_list'] = array_column($candidate_status,'title','id');
        
        $tell_caller = $this->users_model->get(['role_id'=> 6])->getResultArray();
        $this->data['telcaller_list'] = array_column($tell_caller,'name','id');
        
        $this->data['view_data'] = $this->leads_model->get(['id' => $id])->getRowArray();
        echo view('App/Candidate_list/ajax_view', $this->data);
    }

    public function delete($id){
        if ($id > 0){
            if ($this->leads_model->remove(['id' => $id])){
                session()->setFlashdata('message_success', "Leads Deleted Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }else{
            session()->setFlashdata('message_danger', "Something went wrong! Try Again");
        }
        return redirect()->to(base_url('app/candidates/index'));
    }
    
}
