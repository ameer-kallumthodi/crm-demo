<?php
namespace App\Controllers\App;
use App\Models\Admissions_model;
use App\Models\Users_model;
use App\Models\Candidate_status_model;
use App\Models\Lead_source_model;
use App\Models\Candidate_activity_model;
use App\Models\Admission_activity_model;
use App\Models\Country_model;
use App\Models\University_model;
use App\Models\Course_model;
use App\Models\Boards_model;
use App\Models\Subjects_model;
use App\Models\Batch_model;

class Admissions extends AppBaseController
{
    private $users_model;
    private $admissions_model;
    private $candidate_status_model;
    private $lead_source_model;
    private $candidate_activity_model;
    private $admission_activity_model;
    private $country_model;
    private $course_model;
    private $boards_model;
    private $subjects_model;
    private $batch_model;

    public function __construct()
    {
        parent::__construct();
        $this->users_model = new Users_model();
        $this->admissions_model = new Admissions_model();
        $this->candidate_status_model = new Candidate_status_model();
        $this->lead_source_model = new Lead_source_model();
        $this->candidate_activity_model = new Candidate_activity_model();
        $this->admission_activity_model = new Admission_activity_model();
        $this->country_model = new Country_model();
        $this->university_model = new University_model();
        $this->course_model = new Course_model();
        $this->boards_model = new Boards_model();
        $this->subjects_model = new Subjects_model();
        $this->batch_model = new Batch_model();
    }

    public function index(){
        $filter_where = [];
        if (!empty($this->request->getGet('from_date')) && !empty($this->request->getGet('to_date'))){
            
            $filter_where = [
                'created_at >=' => $this->request->getGet('from_date'). ' 00:00:00',
                'created_at <=' => $this->request->getGet('to_date'). ' 23:59:59'
            ];
        }
        
        if($this->request->getGet('candidate_status') > 0){
            $filter_where['candidate_status_id'] = $this->request->getGet('candidate_status');
        }
        
        if($this->request->getGet('course_id') > 0){
            $filter_where['course_id'] = $this->request->getGet('course_id');
        }
        if(is_academic_assistant()){
            $filter_where['academic_assistant_id'] = get_user_id();
        }else if($this->request->getGet('academic_assistant_id') > 0){
            $filter_where['academic_assistant_id'] = $this->request->getGet('academic_assistant_id');
        }
        
        $this->data['list_items']            = $this->admissions_model->get($filter_where, null, ['id','desc'])->getResultArray();
        
        $country                             = $this->country_model->get()->getResultArray();
        $this->data['country_name']          = array_column($country, 'title', 'id');
        
        
        $course                              = $this->course_model->get()->getResultArray();
        $this->data['course_name']           = array_column($course, 'title', 'id');
        
        $lead_source                         = $this->lead_source_model->get()->getResultArray();
        $this->data['lead_source_list']      = array_column($lead_source,'title','id');
        
        $candidate_status                    = $this->candidate_status_model->get()->getResultArray();
        $this->data['candidate_status_list'] = array_column($candidate_status,'title','id');
        
        $academic_assistant                   = $this->users_model->get(['role_id'=> 13])->getResultArray();
        $this->data['academic_assistant_list']= array_column($academic_assistant,'name','id');
        
        $boards                              = $this->boards_model->get()->getResultArray();
        $this->data['boards_list']           = array_column($boards,'title','id');
        $subjects                            = $this->subjects_model->get()->getResultArray();
        $this->data['subjects_list']         = array_column($subjects,'title','id');
        
        $this->data['lead_source']           = $this->lead_source_model->get()->getResultArray();
        $this->data['candidate_status']      = $this->candidate_status_model->get()->getResultArray();
        
        $this->data['lead_source']           = $this->lead_source_model->get()->getResultArray();
        $this->data['candidate_status']      = $this->candidate_status_model->get()->getResultArray();
        
        $this->data['countrys']              = $this->country_model->get()->getResultArray();
        $this->data['universitys']           = $this->university_model->get()->getResultArray();
        $this->data['academic_assistant']   = $academic_assistant;
        $this->data['boards']                = $boards;
        $this->data['course_list']           = $this->course_model->get()->getResultArray();

        $this->data['page_title']            = 'Admissions';
        $this->data['page_name']             = 'Admissions/index';
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
        echo view('App/Admissions/ajax_add', $this->data);
    }

    public function add(){
        if ($this->request->getMethod() === 'post'){
            
            $code = $this->request->getPost('code');
            $phone = $this->request->getPost('phone');
            $email = $this->request->getPost('email');
            
            $check_phone_dublication = $this->admissions_model->get(['code'=>$code,'phone'=>$phone])->getNumRows();
            $check_email_dublication = $this->admissions_model->get(['email'=>$email])->getNumRows();
            
            if($check_phone_dublication == 0 && $check_email_dublication == 0){
                
                $data = 
                [
                    'name' => $this->request->getPost('name'),
                    'code' => $this->request->getPost('code'),
                    'phone' => $this->request->getPost('phone'),
                    'email' => $this->request->getPost('email'),
                    'board_id' => $this->request->getPost('board_id'),
                    'course_id' => $this->request->getPost('course_id'),
                    'subject_id' => $this->request->getPost('subject_id'),
                    'batch_id' => $this->request->getPost('batch_id'),
                    'academic_assistant_id' => $this->request->getPost('academic_assistant_id'),
                    'remarks' => $this->request->getPost('remarks'),
                    'created_by' => get_user_id(),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_by' => get_user_id(),
                    'updated_at' => date('Y-m-d H:i:s')
             
                ];
               
                $candidate_id = $this->admissions_model->add($data);
                if($candidate_id){
                    $candidate_activity_data = [
                        'candidate_status_id' => $this->request->getPost('candidate_status_id'),
                        'admission_id' => $candidate_id, 
                        'remarks' => $this->request->getPost('remarks'), 
                        'created_by' => get_user_id(),
                        'updated_by' => get_user_id(),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                    $lead_activity_id = $this->admission_activity_model->add($candidate_activity_data);
                } 
            
                if ($candidate_id){
                    session()->setFlashdata('message_success', "Added Successfully!");
                }else{
                    session()->setFlashdata('message_danger', "Something went wrong! Try Again");
                }
                    
            }else{
                session()->setFlashdata('message_danger', "Already Exists");
            }
        }
        return redirect()->to(base_url('app/admissions/index'));
    }

    public function ajax_edit($id){
        $this->data['country_code'] = get_country_code();
        $this->data['boards'] = $this->boards_model->get()->getResultArray();
        $this->data['courses'] = $this->course_model->get()->getResultArray();
        $this->data['academic_assistants'] = $this->users_model->get(['role_id' => 13])->getResultArray();
        $this->data['batches'] = $this->batch_model->get()->getResultArray();
 
        $this->data['edit_data'] = $this->admissions_model->get(['id' => $id])->getRowArray();
        echo view('App/Admissions/ajax_edit', $this->data);
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
                    'name' => $this->request->getPost('name'),
                    'code' => $this->request->getPost('code'),
                    'phone' => $this->request->getPost('phone'),
                    'email' => $this->request->getPost('email'),
                    'board_id' => $this->request->getPost('board_id'),
                    'course_id' => $this->request->getPost('course_id'),
                    'subject_id' => $this->request->getPost('subject_id'),
                    'batch_id' => $this->request->getPost('batch_id'),
                    'academic_assistant_id' => $this->request->getPost('academic_assistant_id'),
                    'remarks' => $this->request->getPost('remarks'),
                    'updated_by' => get_user_id(),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            
                $response = $this->admissions_model->edit($data, ['id' => $id]);
                if ($response){
                    session()->setFlashdata('message_success', "Updated Successfully!");
                }else{
                    session()->setFlashdata('message_danger', "Something went wrong! Try Again");
                }
            }else{
                session()->setFlashdata('message_danger', "User Already Exists"); 
            }
            
        }
        return redirect()->to(base_url('app/admissions/index'));
    }
    
    public function ajax_candidate_status($id){
        
        $this->data['edit_data'] = $this->admissions_model->get(['id' => $id])->getRowArray();
        $this->data['candidate_status'] = $this->candidate_status_model->get()->getResultArray();
        $this->data['candidate_id'] = $id;
        
        echo view('App/Admissions/ajax_candidate_status', $this->data);
    }
    
    public function update_candidate_status(){
        if ($this->request->getMethod() === 'post'){
            
            $candidate_id = $this->request->getPost('candidate_id');
            $candidate_status_id = $this->request->getPost('candidate_status_id');
            
            $data = [
                'admission_id' => $candidate_id, 
                'candidate_status_id' => $candidate_status_id,
                'remarks' => $this->request->getPost('remarks'), 
                'updated_by' => get_user_id(),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $candidate_activity_id = $this->admission_activity_model->add($data);
            
            $candidate_data = [
                'candidate_status_id' => $candidate_status_id, 
                'remarks' => $this->request->getPost('remarks'), 
                'updated_by' => get_user_id(),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $this->admissions_model->edit($candidate_data, ['id' => $candidate_id]);
            
            if ($candidate_activity_id){
                session()->setFlashdata('message_success', "Status Updated Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/admissions/index'));
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
        $academic_assistant = $this->users_model->get(['role_id'=> 13])->getResultArray();
        $this->data['academic_assistant_list'] = array_column($academic_assistant,'name','id');
        
        $this->data['view_data'] = $this->admissions_model->get(['id' => $id])->getRowArray();
        echo view('App/Admissions/ajax_view', $this->data);
    }

    public function delete($id){
        if ($id > 0){
            if ($this->admissions_model->remove(['id' => $id])){
                session()->setFlashdata('message_success', "Deleted Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }else{
            session()->setFlashdata('message_danger', "Something went wrong! Try Again");
        }
        return redirect()->to(base_url('app/admissions/index'));
    }
    
    public function ajax_status_documents_remarks($id){
        $this->data['admission_id'] = $id;
        
        echo view('App/Admissions/ajax_status_remarks', $this->data);
    }
    
    public function update_documents_status(){
        if ($this->request->getMethod() === 'post'){
            
            $admission_id = $this->request->getPost('admission_id');
            
            $data = [
                'admission_id' => $admission_id, 
                'candidate_status_id' => 2, 
                'remarks' => $this->request->getPost('remarks'), 
                'updated_by' => get_user_id(),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $candidate_activity_id = $this->admission_activity_model->add($data);
            
            $candidate_data = [
                'is_documents_collected' => 0, 
                'documents_collected_remarks' => $this->request->getPost('remarks'), 
                'updated_by' => get_user_id(),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $this->admissions_model->edit($candidate_data, ['id' => $admission_id]);
            
            if ($candidate_activity_id){
                session()->setFlashdata('message_success', "Status Updated Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/admissions/index'));
    }
    
    public function ajax_status_link_remarks($id){
        $this->data['admission_id'] = $id;
        
        echo view('App/Admissions/ajax_status_link_remarks', $this->data);
    }
    
    public function update_link_status(){
        if ($this->request->getMethod() === 'post'){
            
            $admission_id = $this->request->getPost('admission_id');
            
            $data = [
                'admission_id' => $admission_id, 
                'candidate_status_id' => 3, 
                'remarks' => $this->request->getPost('remarks'), 
                'updated_by' => get_user_id(),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $candidate_activity_id = $this->admission_activity_model->add($data);
            
            $candidate_data = [
                'is_link_shared' => 0, 
                'link_shared_remarks' => $this->request->getPost('remarks'), 
                'updated_by' => get_user_id(),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $this->admissions_model->edit($candidate_data, ['id' => $admission_id]);
            
            if ($candidate_activity_id){
                session()->setFlashdata('message_success', "Status Updated Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/admissions/index'));
    }
    
    public function updateDocumentStatus()
    {
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');
    
        if ($id !== null && $status !== null) {
            $update = $this->admissions_model->edit(['is_documents_collected' => $status], ['id' => $id]);
    
            return $this->response->setJSON(['success' => $update]);
        }
    
        return $this->response->setJSON(['success' => false]);
    }
    
    public function updateLinkShareStatus()
    {
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');
    
        if ($id !== null && $status !== null) {
            $update = $this->admissions_model->edit(['is_link_shared' => $status], ['id' => $id]);
    
            return $this->response->setJSON(['success' => $update]);
        }
    
        return $this->response->setJSON(['success' => false]);
    }

    
}
