<?php
namespace App\Controllers\App;
use App\Models\Leads_model;
use App\Models\Users_model;
use App\Models\Lead_status_model;
use App\Models\Lead_source_model;
use App\Models\Lead_stage_model;
use App\Models\Lead_activity_model;
use App\Models\Country_model;
use App\Models\Candidate_activity_model;
use App\Models\University_model;
use App\Models\Course_model;
use App\Models\Lead_upload_model;
use App\Models\Boards_model;
use App\Models\Batch_model;
use App\Models\Admissions_model;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Controllers\ExotelController;

class Leads extends AppBaseController
{
    private $users_model;
    private $leads_model;
    private $lead_status_model;
    private $lead_source_model;
    private $lead_activity_model;
    private $country_model;
    private $candidate_activity_model;
    private $university_model;
    private $course_model;
    private $lead_upload_model;
    private $lead_stage_model;
    private $boards_model;
    private $batch_model;
    private $admissions_model;
    
    public function __construct()
    {
        parent::__construct();
        $this->users_model = new Users_model();
        $this->leads_model = new Leads_model();
        $this->lead_status_model = new Lead_status_model();
        $this->lead_source_model = new Lead_source_model();
        $this->lead_activity_model = new Lead_activity_model();
        $this->country_model = new Country_model();
        $this->candidate_activity_model = new Candidate_activity_model();
        $this->university_model = new University_model();
        $this->course_model = new Course_model();
        $this->lead_upload_model = new Lead_upload_model();
        $this->lead_stage_model = new Lead_stage_model();
        $this->boards_model = new Boards_model();
        $this->batch_model = new Batch_model();
        $this->admissions_model = new Admissions_model();
    }
    
    public function index()
    {
        if(is_academic_assistant()){
            return redirect()->to(base_url('app/dashboard/index'));
        }
        // Load data for filters
        $this->data['lead_status'] = $this->lead_status_model->get()->getResultArray();
        $this->data['lead_source'] = $this->lead_source_model->get()->getResultArray();
        $this->data['countrys'] = $this->country_model->get()->getResultArray();
        $this->data['university_list'] = $this->university_model->get()->getResultArray();
        $this->data['course_list'] = $this->course_model->get()->getResultArray();
    
        // Team Lead logic
        $telecaller_where = [];
        if (is_team_lead() && !is_admin()) {
            $team_id = $this->users_model->get(['id' => get_user_id()], ['team_id'])->getRow()->team_id;
            $team_member_ids = get_team_member_ids($team_id);
            $telecaller_where['id'] = $team_member_ids;
        }
        $telecaller_where['role_id'] = 6;
        $this->data['telecaller'] = $this->users_model->get($telecaller_where)->getResultArray();
    
        // Join tables and select fields
        $join = [
            ['lead_source', 'lead_source.id = leads.lead_source_id', 'left'],
            ['lead_status', 'lead_status.id = leads.lead_status_id', 'left'],
            ['course', 'course.id = leads.course_id', 'left'],
            ['users', 'users.id = leads.telecaller_id', 'left'],
        ];
        $select = ['leads.*', 'lead_status.title as lead_status', 'lead_source.title as lead_source', 'course.title as course', 'users.name as telecaller'];
    
        // Default and GET filters
        $filter_where = [];
        // Search key filter
        $search_key = $this->request->getGet('search_key');
        if (!empty($search_key)) {
            $search_conditions = [
                'leads.title LIKE' => "%$search_key%",
                'leads.phone LIKE' => "%$search_key%",
                'leads.email LIKE' => "%$search_key%",
            ];
            $filter_where['OR'] = $search_conditions;
            $from_date = '';
            $to_date = '';
        }else{
            $from_date = $this->request->getGet('from_date') ?: date('Y-m-d', strtotime('-7 days'));
            $to_date = $this->request->getGet('to_date') ?: date('Y-m-d');
            $filter_where['leads.created_at >='] = $from_date . ' 00:00:00';
            $filter_where['leads.created_at <='] = $to_date . ' 23:59:59';
        }
    
        // Additional filters
        $filters = [
            'lead_status' => 'leads.lead_status_id',
            'lead_source' => 'leads.lead_source_id',
            'country_id' => 'leads.country_id',
            'university_id' => 'leads.university_id',
            'course_id' => 'leads.course_id',
            'telecaller_id' => 'leads.telecaller_id',
        ];
        foreach ($filters as $get_key => $db_field) {
            $value = $this->request->getGet($get_key);
            if ($value > 0) {
                $filter_where[$db_field] = $value;
            }
        }
        $filter_where['is_converted'] = 0;
    
        // Fetch data based on role
        if (is_admin()) {
            $this->data['list_items'] = $this->leads_model->get_join($join, $filter_where, $select, ['key' => 'leads.id', 'direction' => 'desc'])->getResultArray();
        } else {
            if (is_team_lead() && !is_admin()) {
                if (empty($filter_where['leads.telecaller_id'])) {
                    $team_id = $this->users_model->get(['id' => get_user_id()], ['team_id'])->getRow()->team_id;
                    $team_member_ids = get_team_member_ids($team_id);
                    $filter_where['leads.telecaller_id'] = $team_member_ids;
                }
            } else {
                $filter_where['leads.telecaller_id'] = get_user_id();
            }
            $this->data['list_items'] = $this->leads_model->get_join($join, $filter_where, $select, ['key' => 'leads.id', 'direction' => 'desc'])->getResultArray();
        }
    
        // Pass dates to the frontend
        $this->data['from_date'] = $from_date;
        $this->data['to_date'] = $to_date;
    
        // Set page information
        $this->data['page_title'] = 'Leads';
        $this->data['page_name'] = 'Leads/index';
        return view('App/index', $this->data);
    }

    // public function index(){
        
    //     $this->data['lead_status'] = $this->lead_status_model->get()->getResultArray();
    //     $this->data['lead_source'] = $this->lead_source_model->get()->getResultArray();
    //     $this->data['countrys'] = $this->country_model->get()->getResultArray();
    //     $this->data['university_list'] = $this->university_model->get()->getResultArray();
    //     $this->data['course_list'] = $this->course_model->get()->getResultArray();

    //     $this->data['telecaller'] = $this->users_model->get(['role_id'=> 6])->getResultArray();
        
    //     $join = [
    //         ['lead_source', 'lead_source.id = leads.lead_source_id', 'left'],
    //         ['lead_status', 'lead_status.id = leads.lead_status_id', 'left'],
    //         ['course', 'course.id = leads.course_id', 'left'],
    //         ['users', 'users.id = leads.telecaller_id', 'left'],
    //     ];
        
    //     $select = ['leads.*', 'lead_status.title as lead_status', 'lead_source.title as lead_source', 'course.title as course', 'users.name as telecaller'];
        
    //     if ($this->request->getGet()){
            
    //         if (!empty($this->request->getGet('from_date')) && !empty($this->request->getGet('to_date'))){
    //             $filter_where = [
    //                 'leads.created_at >=' => $this->request->getGet('from_date'). ' 00:00:00',
    //                 'leads.created_at <=' => $this->request->getGet('to_date'). ' 23:59:59'
    //             ];
    //         }
                
    //         if($this->request->getGet('lead_status') > 0){
    //             $filter_where['leads.lead_status_id'] = $this->request->getGet('lead_status');
    //         }
            
    //         if($this->request->getGet('lead_source') > 0){
    //             $filter_where['leads.lead_source_id'] = $this->request->getGet('lead_source');
    //         } 
            
    //         if($this->request->getGet('country_id') > 0){
    //             $filter_where['leads.country_id'] = $this->request->getGet('country_id');
    //         } 
            
    //         if($this->request->getGet('university_id') > 0){
    //             $filter_where['leads.university_id'] = $this->request->getGet('university_id');
    //         } 
            
    //         if($this->request->getGet('course_id') > 0){
    //             $filter_where['leads.course_id'] = $this->request->getGet('course_id');
    //         } 
            
    //         if($this->request->getGet('telecaller_id') > 0){
    //             $filter_where['leads.telecaller_id'] = $this->request->getGet('telecaller_id');
    //         } 
            

    //         $this->data['list_items'] = $this->leads_model->get_join($join, $filter_where,$select,['key' => 'leads.id', 'direction' => 'desc'])->getResultArray();
    //         // log_last_query(); 
            
    //     }else{
            
    //         if(is_admin()){
             
    //              $this->data['list_items'] = $this->leads_model->get_join($join, null,$select,['key' => 'leads.id', 'direction' => 'desc'])->getResultArray(); 
    //         }else{
                
    //              $this->data['list_items'] = $this->leads_model->get_join($join, ['leads.telecaller_id' => get_user_id()],$select,['key' => 'leads.id', 'direction' => 'desc'])->getResultArray();
    //         }
            
    //     }
        
    //     // $country = $this->country_model->get()->getResultArray();
    //     // $this->data['country_name'] = array_column($country, 'title', 'id');
        
    //     // $university = $this->university_model->get()->getResultArray();
    //     // $this->data['university_name'] = array_column($university, 'title', 'id');
        
    //     // $lead_source = $this->lead_source_model->get()->getResultArray();
    //     // $this->data['lead_source_list'] = array_column($lead_source,'title','id');
        
    //     // $lead_status = $this->lead_status_model->get()->getResultArray();
    //     // $this->data['lead_status_list'] = array_column($lead_status,'title','id');
        
    //     // $tell_caller = $this->users_model->get(['role_id'=> 6])->getResultArray();
    //     // $this->data['telcaller_list'] = array_column($tell_caller,'name','id');
        
    //     // $course = $this->course_model->get()->getResultArray();
    //     // $this->data['course_name'] = array_column($course, 'title', 'id');
       

    //     $this->data['page_title'] = 'Leads';
    //     $this->data['page_name'] = 'Leads/index';
    //     return view('App/index', $this->data);
    // }
    
    public function ajax_add(){
        if(is_team_lead() && !is_admin()){
            $team_id = $this->users_model->get(['id' => get_user_id()], ['team_id'])->getRow()->team_id;
            $team_member_ids = get_team_member_ids($team_id);
            $telecaller_where['id'] = $team_member_ids;
        }
        
        $telecaller_where['role_id'] = 6;
        $this->data['tele_callers'] = $this->users_model->get($telecaller_where)->getResultArray();
        $this->data['lead_status'] = $this->lead_status_model->get()->getResultArray();
        $this->data['lead_stages'] = $this->lead_stage_model->get()->getResultArray();
        $this->data['lead_source'] = $this->lead_source_model->get()->getResultArray();
        $this->data['country_list'] = $this->country_model->get()->getResultArray();
        $this->data['university_list'] = $this->university_model->get()->getResultArray();
        $this->data['course_list'] = $this->course_model->get()->getResultArray();
        $this->data['country_code'] = get_country_code();
        
        echo view('App/Leads/ajax_add', $this->data);
    }

    public function add(){
        
        if ($this->request->getMethod() === 'post'){
            
            $code = $this->request->getPost('code');
            $phone = $this->request->getPost('phone');
            $email = $this->request->getPost('email');
            
            $check_phone_dublication = $this->leads_model->get(['code'=>$code,'phone'=>$phone])->getNumRows();
            $check_email_dublication = $this->leads_model->get(['email'=>$email])->getNumRows();
            $lead_status_id = $this->request->getPost('lead_status_id');
            if (empty($lead_status_id)) {
                $lead_status_id = 23;
            }
            $lead_stage_id = $this->request->getPost('lead_stage_id');
            if($check_phone_dublication == 0 && $check_email_dublication == 0){
                // if(!is_telecaller()){
                //     $telecaller_id = $this->request->getPost('telecaller_id');
                // }else{
                //     $telecaller_id = get_user_id();
                // }
                $telecaller_id = $this->request->getPost('telecaller_id');
                $data = 
                [
                    'title' => $this->request->getPost('title'),
                    'gender' => $this->request->getPost('gender') == 'male' ? 'male' : 'female', 
                    'age' => $this->request->getPost('age'), 
                    'code' => $code,
                    'phone' => $phone,
                    'whatsapp_code' => $this->request->getPost('whatsapp_code'),
                    'whatsapp' => $this->request->getPost('whatsapp'),
                    'email' => $email, 
                    'qualification' => $this->request->getPost('qualification'), 
                    'date' => !empty($this->request->getPost('date')) ? date('Y-m-d', strtotime($this->request->getPost('date'))) : null,
                    'time' => !empty($this->request->getPost('time')) ? date('H:i:s', strtotime($this->request->getPost('time'))) : null,
                    'country_id' => $this->request->getPost('country_id'),
                    'university_id' => $this->request->getPost('university_id'),
                    'remarks' => $this->request->getPost('remarks'), 
                    'interest_status' => $this->request->getPost('interest_status'), 
                    'lead_status_id' => $lead_status_id, 
                    'lead_stage_id' => $lead_stage_id, 
                    'lead_source_id' => $this->request->getPost('lead_source_id'), 
                    'address' => $this->request->getPost('address'),
                    'is_converted' => 0,
                    'telecaller_id' => $telecaller_id, 
                    'course_id' => $this->request->getPost('course_id'), 
                    'place' => $this->request->getPost('place'), 
                    'created_by' => get_user_id(),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_by' => get_user_id(),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            // if($lead_status_id == 4){
            //     $data['is_converted'] = 1;
            // }else{
            //     $data['is_converted'] = 0;
            // }
               
            $Leads_id = $this->leads_model->add($data);
            if($Leads_id){
                $lead_activity_data = [
                    'lead_status_id' => $this->request->getPost('lead_status_id'),
                    'date' => !empty($this->request->getPost('date')) ? date('Y-m-d', strtotime($this->request->getPost('date'))) : null,
                    'time' => !empty($this->request->getPost('time')) ? date('H:i:s', strtotime($this->request->getPost('time'))) : null,
                    'lead_id' => $Leads_id, 
                    'remarks' => $this->request->getPost('remarks'), 
                    'created_by' => get_user_id(),
                    'updated_by' => get_user_id(),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $lead_activity_id = $this->lead_activity_model->add($lead_activity_data);
            } 
            
            if ($Leads_id){
                session()->setFlashdata('message_success', "Leads Added Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
                
            }else{
                session()->setFlashdata('message_danger', "Lead Already Exists");
            }
            
         
        }
        
        // return redirect()->to(base_url('app/leads/index'));
        return redirect()->to(previous_url());
    }
    
    public function ajax_edit($id){
        if(is_team_lead() && !is_admin()){
            $team_id = $this->users_model->get(['id' => get_user_id()], ['team_id'])->getRow()->team_id;
            $team_member_ids = get_team_member_ids($team_id);
            $telecaller_where['id'] = $team_member_ids;
        }
        
        $telecaller_where['role_id'] = 6;
        $this->data['tele_callers'] = $this->users_model->get($telecaller_where)->getResultArray();
        $this->data['lead_status'] = $this->lead_status_model->get()->getResultArray();
        $this->data['lead_stages'] = $this->lead_stage_model->get()->getResultArray();
        $this->data['lead_source'] = $this->lead_source_model->get()->getResultArray();
        $this->data['country_list'] = $this->country_model->get()->getResultArray();
        $this->data['university_list'] = $this->university_model->get()->getResultArray();
        $this->data['country_code'] = get_country_code();
        $this->data['course_list'] = $this->course_model->get()->getResultArray();

        
        $this->data['edit_data'] = $this->leads_model->get(['id' => $id])->getRowArray();
        echo view('App/Leads/ajax_edit', $this->data);
    }

    public function edit($id){
        
        if ($this->request->getMethod() === 'post'){
            
            $code = $this->request->getPost('code');
            $phone = $this->request->getPost('phone');
            $email = $this->request->getPost('email');
            
             $check_phone_dublication = $this->leads_model->get(['code' => $code,'phone' => $phone ,'id !=' => $id])->getNumRows();
             $check_email_dublication = $this->leads_model->get(['email' => $email,'id !=' => $id])->getNumRows();
             $lead_status_id = $this->request->getPost('lead_status_id');
             $lead_stage_id = $this->request->getPost('lead_stage_id');
             if($check_phone_dublication ==0 && $check_email_dublication ==0) {
                // if(!is_telecaller()){
                //     $telecaller_id = $this->request->getPost('telecaller_id');
                // }else{
                //     $telecaller_id = get_user_id();
                // }
                $telecaller_id = $this->request->getPost('telecaller_id');
                $data = [
                    
                    'title' => $this->request->getPost('title'),
                    'gender' => $this->request->getPost('gender') == 'male' ? 'male' : 'female', 
                    'age' => $this->request->getPost('age'), 
                    'code' => $code,
                    'phone' => $phone,
                    'whatsapp_code' => $this->request->getPost('whatsapp_code'),
                    'whatsapp' => $this->request->getPost('whatsapp'), 
                    'email' => $email,
                    'qualification' => $this->request->getPost('qualification'), 
                    'country_id' => $this->request->getPost('country_id'),
                    'university_id' => $this->request->getPost('university_id'),
                    'interest_status' => $this->request->getPost('interest_status'), 
                    'lead_status_id' => $lead_status_id, 
                    'lead_stage_id' => $lead_stage_id,
                    'lead_source_id' => $this->request->getPost('lead_source_id'), 
                    'address' => $this->request->getPost('address'), 
                    'telecaller_id' => $telecaller_id, 
                    'course_id' => $this->request->getPost('course_id'), 
                    'place' => $this->request->getPost('place'), 
                    'remarks' => $this->request->getPost('remarks'), 
                    'updated_by' => get_user_id(),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                // if($lead_status_id == 4){
                //     $data['is_converted'] = 1;
                // }else{
                //     $data['is_converted'] = 0;
                // }
                
                $response = $this->leads_model->edit($data, ['id' => $id]);
                if ($response){
                    session()->setFlashdata('message_success', "Leads Updated Successfully!");
                }else{
                    session()->setFlashdata('message_danger', "Something went wrong! Try Again");
                }
            }else{
                session()->setFlashdata('message_danger', "Lead Already Exists"); 
            }

        }
        // return redirect()->to(base_url('app/leads/index'));
        return redirect()->to(previous_url());
    }

    public function ajax_bulk_upload(){
        if(is_telecaller()){
            if(is_team_lead() && !is_admin()){
                $team_id = $this->users_model->get(['id' => get_user_id()], ['team_id'])->getRow()->team_id;
                $team_member_ids = get_team_member_ids($team_id);
                $telecaller_where['id'] = $team_member_ids;
            }else{
                $telecaller_where['id'] = get_user_id();
            }
        }
        $telecaller_where['role_id'] = 6;
        
        $this->data['tele_callers'] = $this->users_model->get($telecaller_where)->getResultArray();
        $this->data['lead_status'] = $this->lead_status_model->get()->getResultArray();
        $this->data['lead_source'] = $this->lead_source_model->get()->getResultArray();
        $this->data['country_list'] = $this->country_model->get()->getResultArray();
        $this->data['country_code'] = get_country_code();
         $this->data['course_list'] = $this->course_model->get()->getResultArray();

        echo view('App/Leads/ajax_bulk_upload', $this->data);
    }
    
    public function bulk_upload_add(){
        if ($this->request->getMethod() === 'post') {
            // Determine team lead IDs
            $all_team_lead_ids = $this->request->getPost('tele_callers');
            $all_team_lead_id = $this->users_model->get(['role_id' => 6, 'id' => $all_team_lead_ids], ['id'])->getResultArray();
    
            // Prepare data for the file upload
            $data2 = [
                'title' => $this->request->getPost('excel_title'),
                'lead_source_id' => $this->request->getPost('lead_source_id'),
                'created_at' => date('Y-m-d H:i:s'),
            ];
    
            // Handle file upload
            $excel_file = $this->upload_file('excel_file', 'excel_file');
            if (!$excel_file) {
                session()->setFlashdata('message_danger', "File upload failed. Please try again.");
                return redirect()->to(previous_url());
            }
    
            $data2['file'] = $excel_file['file'];
            $excel_file_id = $this->lead_upload_model->add($data2);
    
            $path = WRITEPATH . '/' . $excel_file['file'];
            if (!file_exists($path)) {
                session()->setFlashdata('message_danger', "Uploaded file not found.");
                return redirect()->to(previous_url());
            }
    
            $spreadsheet = IOFactory::load($path);
            $firstRow = $spreadsheet->getActiveSheet()->getRowIterator()->current();
            $actualHeadings = [];
            foreach ($firstRow->getCellIterator() as $cell) {
                $actualHeadings[] = $cell->getValue();
            }
    
            $expectedHeadings = ['Student name', 'Phone', 'Place', 'Remarks'];
            if (array_slice($actualHeadings, 0, count($expectedHeadings)) !== $expectedHeadings) {
                session()->setFlashdata('message_danger', "Invalid Excel Format. Download the template from the form and try again.");
                return redirect()->to(previous_url());
            }
    
            $worksheet = $spreadsheet->getActiveSheet();
            $dataRowCount = $worksheet->getHighestRow() - 1;
            if ($dataRowCount == 0) {
                session()->setFlashdata('message_danger', "No data found in the uploaded sheet.");
                return redirect()->to(previous_url());
            }
    
            $lead_source_id = $this->request->getPost('lead_source_id');
            $course_id = $this->request->getPost('course_id');
    
            $totalLeads = $dataRowCount;
            $telecallersCount = count($all_team_lead_id);
            $leadsPerTelecaller = floor($totalLeads / $telecallersCount);
            $remainingLeads = $totalLeads % $telecallersCount;
    
            $assignedCounts = array_fill(0, $telecallersCount, $leadsPerTelecaller);
            for ($i = 0; $i < $remainingLeads; $i++) {
                $assignedCounts[$i]++;
            }
    
            // $telecallerIndex = 0;
            // $leadAssignedCount = 0;
            // $scount = 0;
            $scount = 0;
            $telecallersCount = count($all_team_lead_id);
            $telecallerIndex = 0;
    
            for ($row = 2; $row <= $worksheet->getHighestRow(); $row++) {
                $data = [];
                $data['title'] = remove_excel_icon($worksheet->getCellByColumnAndRow(1, $row)->getValue());
                $phone_number = remove_excel_icon($worksheet->getCellByColumnAndRow(2, $row)->getValue());
    
                if (empty($phone_number)) {
                    continue;
                }
    
                $phone_number = get_phone_code($phone_number);
                $data['phone'] = $phone_number['phone'];
                $data['place'] = remove_excel_icon($worksheet->getCellByColumnAndRow(3, $row)->getValue());
                $data['remarks'] = remove_excel_icon($worksheet->getCellByColumnAndRow(4, $row)->getValue());
    
                if (!is_numeric($phone_number['code']) || !is_numeric($phone_number['phone'])) {
                    session()->setFlashdata('message_danger', "Invalid data at row $row: Code and Phone must be integers.");
                    return redirect()->to(previous_url());
                }
    
                $data['code'] = $phone_number['code'];
    
                $existingLead = $this->leads_model->get(['code' => $data['code'], 'phone' => $data['phone']])->getRow();
                if ($existingLead) {
                    continue;
                }
    
                $data['is_converted'] = 0;
                // $data['telecaller_id'] = $all_team_lead_id[$telecallerIndex]['id'];
                // $leadAssignedCount++;
    
                // if ($leadAssignedCount >= $assignedCounts[$telecallerIndex]) {
                //     $telecallerIndex++;
                //     $leadAssignedCount = 0;
                // }
                
                // Round-robin telecaller assignment
                $data['telecaller_id'] = $all_team_lead_id[$telecallerIndex]['id'];
                $telecallerIndex = ($telecallerIndex + 1) % $telecallersCount;
    
                $data['excel_file_id'] = $excel_file_id;
                $data['lead_source_id'] = $lead_source_id;
                $data['course_id'] = $course_id;
                $data['lead_status_id'] = 23;
                $data['created_by'] = get_user_id();
                $data['updated_by'] = get_user_id();
                $data['created_at'] = date('Y-m-d H:i:s');
                $data['updated_at'] = date('Y-m-d H:i:s');
    
                if ($this->leads_model->add($data)) {
                    $scount++;
                } else {
                    session()->setFlashdata('message_danger', "Failed to add lead for row $row.");
                }
            }
    
            if ($scount > 0) {
                session()->setFlashdata('message_success', "$scount Leads Added Successfully!");
            }
    
            return redirect()->to(previous_url());
        }
    }
    
    // public function bulk_upload_add(){
    //     if ($this->request->getMethod() === 'post') {
            
    //         // Determine team lead IDs
    //         // if ($this->request->getPost('assign_all_team_lead') == 1) {
    //         //     $all_team_lead_id = $this->users_model->get(['role_id' => 6], ['id'])->getResultArray();
    //         // } else {
    //             $all_team_lead_ids = $this->request->getPost('tele_callers');
    //             $all_team_lead_id = $this->users_model->get(['role_id' => 6, 'id' => $all_team_lead_ids], ['id'])->getResultArray();
    //         // }
            
    //         // Prepare data for the file upload
    //         $data2 = [
    //             'title' => $this->request->getPost('excel_title'),
    //             'lead_source_id' => $this->request->getPost('lead_source_id'),
    //             'created_at' => date('Y-m-d H:i:s'),
    //         ];
    
    //         // Handle file upload
    //         $excel_file = $this->upload_file('excel_file', 'excel_file');
    //         if (!$excel_file) {
    //             session()->setFlashdata('message_danger', "File upload failed. Please try again.");
    //             // return redirect()->to(base_url('app/leads/index'));
    //             return redirect()->to(previous_url());
    //         }
            
    //         $data2['file'] = $excel_file['file'];
    //         $excel_file_id = $this->lead_upload_model->add($data2);
            
    //         $path = WRITEPATH . '/' . $excel_file['file'];
    //         if (!file_exists($path)) {
    //             session()->setFlashdata('message_danger', "Uploaded file not found.");
    //             // return redirect()->to(base_url('app/leads/index'));
    //             return redirect()->to(previous_url());
    //         }
    
    //         $spreadsheet = IOFactory::load($path);
    
    //         // Get the first row of the spreadsheet (assuming it contains column headings)
    //         $firstRow = $spreadsheet->getActiveSheet()->getRowIterator()->current();
    //         $actualHeadings = [];
    //         foreach ($firstRow->getCellIterator() as $cell) {
    //             $actualHeadings[] = $cell->getValue();
    //         }
            
    //         // Define expected column headings
    //         $expectedHeadings = ['Student name', 'Phone', 'Place', 'Remarks'];
    
    //         // Check if actual headings match expected headings
    //         if (array_slice($actualHeadings, 0, count($expectedHeadings)) !== $expectedHeadings) {
    //             session()->setFlashdata('message_danger', "Invalid Excel Format. Download the template from the form and try again..!");
    //             // return redirect()->to(base_url('app/leads/index'));
    //             return redirect()->to(previous_url());
    //         }
            
    //         // Check if sheet contains data other than header
    //         $worksheet = $spreadsheet->getActiveSheet();
    //         $dataRowCount = $worksheet->getHighestRow() - 1; // Exclude header row
    //         if ($dataRowCount == 0) {
    //             session()->setFlashdata('message_danger', "No data found in the uploaded sheet. Please ensure the sheet contains data below the header row.");
    //             // return redirect()->to(base_url('app/leads/index'));
    //             return redirect()->to(previous_url());
    //         }
            
    //         $lead_source_id = $this->request->getPost('lead_source_id');
    //         $course_id = $this->request->getPost('course_id');
    //         $scount = 0;
    
    //         // Process the data
    //         for ($row = 2; $row <= $worksheet->getHighestRow(); $row++) {
    //             $data = [];
    //             $data['title'] = remove_excel_icon($worksheet->getCellByColumnAndRow(1, $row)->getValue());
    //             $phone_number = remove_excel_icon($worksheet->getCellByColumnAndRow(2, $row)->getValue()); // Assuming phone is in the 3rd column
    
    //             if (empty($phone_number)) {
    //                 continue; // Skip empty phone numbers
    //             }
    
    //             $phone_number = get_phone_code($phone_number);
                
    //             $data['phone'] = $phone_number['phone'];
    //             $data['place'] = remove_excel_icon($worksheet->getCellByColumnAndRow(3, $row)->getValue());
    //             $data['remarks'] = remove_excel_icon($worksheet->getCellByColumnAndRow(4, $row)->getValue());
    
    //             // Validate phone and code
    //             if (!is_numeric($phone_number['code']) || !is_numeric($phone_number['phone'])) {
    //                 session()->setFlashdata('message_danger', "Invalid data at row $row: Code and Phone must be integers.");
    //                 // return redirect()->to(base_url('app/leads/index'));
    //                 return redirect()->to(previous_url());
    //             }
    //             $code = $phone_number['code'];
    //             $data['code'] = $code;
    
    //             // Check for existing lead
    //             $existingLead = $this->leads_model->get(['code' => $data['code'], 'phone' => $data['phone']])->getRow();
    //             if ($existingLead) {
    //                 continue; // Skip if already exists
    //             }
                
    //             // Prepare lead data
    //             $data['is_converted'] = 0;
    //             $data['telecaller_id'] = $this->get_team_lead_id_for_bulk_upload($all_team_lead_id);
    //             $data['excel_file_id'] = $excel_file_id;
    //             $data['lead_source_id'] = $lead_source_id;
    //             $data['course_id'] = $course_id;
    //             $data['lead_status_id'] = 23;
    //             $data['created_by'] = get_user_id();
    //             $data['updated_by'] = get_user_id();
    //             $data['created_at'] = date('Y-m-d H:i:s');
    //             $data['updated_at'] = date('Y-m-d H:i:s');
                
    //             // Insert lead data
    //             if ($this->leads_model->add($data)) {
    //                 $scount++;
    //             } else {
    //                 session()->setFlashdata('message_danger', "Failed to add lead for row $row.");
    //             }
    //         }
    
    //         // Set success message
    //         if ($scount > 0) {
    //             session()->setFlashdata('message_success', "$scount Leads Added Successfully!");
    //         }
            
    //         // return redirect()->to(base_url('app/leads/index'));
    //         return redirect()->to(previous_url());
    //     }
    // }

    public function ajax_bulk_reassign(){
        if(is_team_lead() && !is_admin()){
            $team_id = $this->users_model->get(['id' => get_user_id()], ['team_id'])->getRow()->team_id;
            $team_member_ids = get_team_member_ids($team_id);
            $telecaller_where['id'] = $team_member_ids;
        }
        
        $telecaller_where['role_id'] = 6;
        $this->data['tele_callers'] = $this->users_model->get($telecaller_where)->getResultArray();
        $this->data['lead_status'] = $this->lead_status_model->get()->getResultArray();
        $this->data['lead_source'] = $this->lead_source_model->get()->getResultArray();
        $this->data['country_list'] = $this->country_model->get()->getResultArray();
        $this->data['country_code'] = get_country_code();
        $this->data['course_list'] = $this->course_model->get()->getResultArray();

        echo view('App/Leads/ajax_bulk_reassign', $this->data);
    }
    
    public function bulk_reassign() {
        
        if ($this->request->getMethod() === 'post') {
            $tele_caller_id = $this->request->getPost('tele_caller_id');
            $from_tele_caller_id = $this->request->getPost('from_tele_caller_id');
            $lead_source_id = $this->request->getPost('lead_source_id');
            $selected_lead_ids = $this->request->getPost('lead_id');
    
            if (!empty($selected_lead_ids)) {
                foreach ($selected_lead_ids as $selected_lead_id) {
                    $success = $this->leads_model->re_assign_leads($tele_caller_id, $lead_source_id, $selected_lead_id, $from_tele_caller_id);
    
                    // Log error if the update fails
                    if (!$success) {
                        log_message('error', 'Failed to re-assign lead ID: ' . $selected_lead_id);
                    }
                }
                
                session()->setFlashdata('message_success', "Re-assigned Successfully!");
            } else {
                session()->setFlashdata('message_error', "Select Atleast a lead!");
            }
            
            // return redirect()->to(base_url('app/leads/index'));
            return redirect()->to(previous_url());
        }
    }
    
    public function ajax_bulk_delete(){
        
        $this->data['tele_callers'] = $this->users_model->get(['role_id' => 6])->getResultArray();
        $this->data['lead_status'] = $this->lead_status_model->get()->getResultArray();
        $this->data['lead_source'] = $this->lead_source_model->get()->getResultArray();
        $this->data['country_list'] = $this->country_model->get()->getResultArray();
        $this->data['country_code'] = get_country_code();
        $this->data['course_list'] = $this->course_model->get()->getResultArray();

        echo view('App/Leads/ajax_bulk_delete', $this->data);
    }
    
    public function bulk_delete() {
        if ($this->request->getMethod() === 'post') {
            $selected_lead_ids = $this->request->getPost('lead_id');
    
            // Ensure selected_lead_ids is an array and not empty
            if (is_array($selected_lead_ids) && !empty($selected_lead_ids)) {
                $all_success = true; // Track if all deletions were successful
    
                foreach ($selected_lead_ids as $selected_lead_id) {
                    $success = $this->leads_model->bulk_delete_leads($selected_lead_id);
    
                    // Log error if the delete fails
                    if (!$success) {
                        log_message('error', 'Failed to Delete lead ID: ' . $selected_lead_id);
                        $all_success = false; // Flag as false if any deletion fails
                    }
                }
    
                // Flash appropriate message based on success
                if ($all_success) {
                    session()->setFlashdata('message_success', "Deleted Successfully!");
                } else {
                    session()->setFlashdata('message_error', "Some leads failed to delete.");
                }
            } else {
                session()->setFlashdata('message_error', "Select at least one lead!");
            }
    
            return redirect()->to(previous_url());
        }
    }
    
    private function get_team_lead_id_for_bulk_upload($all_team_lead_id){
        $randomKey = array_rand($all_team_lead_id);

        $randomId = $all_team_lead_id[$randomKey]['id'];
        
        return $randomId;
    }
    
    public function ajax_lead_status($id){
        $this->data['edit_data'] = $this->leads_model->get(['id' => $id])->getRowArray();
        $this->data['lead_status'] = $this->lead_status_model->get()->getResultArray();
        $this->data['lead_id'] = $id;
        
        echo view('App/Leads/ajax_lead_status', $this->data);
    }
    
    public function update_lead_status(){
        if ($this->request->getMethod() === 'post'){
            
            $lead_id = $this->request->getPost('lead_id');
            $lead_status_id = $this->request->getPost('lead_status_id');
            $meeting_url = $this->request->getPost('meeting_url');
            $passcode = $this->request->getPost('passcode');
            $product_name = $this->request->getPost('product_name');
            $contact_number = $this->request->getPost('contact_number');
            $date = $this->request->getPost('date') ? date('Y-m-d',strtotime($this->request->getPost('date'))) : '';
            $time = $this->request->getPost('time') ? date('H:i:s',strtotime($this->request->getPost('time'))) : '';
            if($lead_status_id == 4){
                $candidate_data = [
                    'candidate_status_id' => 1,
                    'candidate_id' => $lead_id,
                    'remarks' => $this->request->getPost('remarks'),
                    'reason' => $this->request->getPost('reason'), 
                    'updated_by' => get_user_id(),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $response = $this->candidate_activity_model->add($candidate_data);
                $emailTo = $this->leads_model->get(['id' => $lead_id],['email'])->getRow()->email;
                // if($emailTo > 0){
                //     $this->send_mail($emailTo);
                // }
                
            }else{
                $data = [
                    'lead_status_id' => $lead_status_id,
                    'date' => $date,
                    'time' => $time,
                    'lead_id' => $lead_id, 
                    'contact_number' => $contact_number,
                    'remarks' => $this->request->getPost('remarks'),
                    'reason' => $this->request->getPost('reason'), 
                    'created_by' => get_user_id(),
                    'updated_by' => get_user_id(),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $response = $this->lead_activity_model->add($data);
            }
            
            $lead_data = [
                'date' => $date,
                'time' => $time,
                'lead_status_id' => $lead_status_id,
                'remarks' => $this->request->getPost('remarks'), 
                'reason' => $this->request->getPost('reason'), 
                'updated_by' => get_user_id(),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            // if($lead_status_id == 4){
            //     $lead_data['is_converted'] = 1;
            // }
            $this->leads_model->edit($lead_data, ['id' => $lead_id]);
            if($this->request->getPost('send_mail') == 1){
                $lead_data = $this->leads_model->get(['id' => $lead_id])->getRow();
                if($lead_data->email != ''){
                    $lead_name = $lead_data->title;
                    $this->send_mail($lead_name, $lead_data->email, $meeting_url, $passcode, $date, $time, $product_name, $contact_number);
                }
            }
            if ($response){
                session()->setFlashdata('message_success', "Lead Status Updated Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        // return redirect()->to(base_url('app/leads/index'));
        return redirect()->to(previous_url());
    }
    
    public function ajax_lead_convert($id){
        $this->data['country_code'] = get_country_code();
        $this->data['edit_data'] = $this->leads_model->get(['id' => $id])->getRowArray();
        $this->data['boards'] = $this->boards_model->get()->getResultArray();
        $this->data['courses'] = $this->course_model->get()->getResultArray();
        $this->data['academic_assistants'] = $this->users_model->get(['role_id' => 13])->getResultArray();
        $this->data['batches'] = $this->batch_model->get()->getResultArray();
        
        echo view('App/Leads/ajax_lead_convert', $this->data);
    }
    
    public function convert_lead(){
        if ($this->request->getMethod() === 'post'){
            
            $lead_id = $this->request->getPost('lead_id');
            $data = [
                'name' => $this->request->getPost('name'),
                'code' => $this->request->getPost('code'),
                'phone' => $this->request->getPost('phone'),
                'lead_id' => $lead_id, 
                'email' => $this->request->getPost('email'),
                'board_id' => $this->request->getPost('board_id'),
                'course_id' => $this->request->getPost('course_id'),
                'subject_id' => $this->request->getPost('subject_id'),
                'batch_id' => $this->request->getPost('batch_id'),
                'candidate_status_id' => 1,
                'academic_assistant_id' => $this->request->getPost('academic_assistant_id'),
                'remarks' => $this->request->getPost('remarks'),
                'created_by' => get_user_id(),
                'updated_by' => get_user_id(),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $response = $this->admissions_model->add($data);
            
            if ($response){
                $lead_data = [
                    'is_converted' => 1,
                    'updated_by' => get_user_id(),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $this->leads_model->edit($lead_data, ['id' => $lead_id]);
                session()->setFlashdata('message_success', "Converted Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(previous_url());
    }
    
    private function send_mail($lead_name, $emailTo, $meeting_url, $passcode, $date, $time, $product_name, $contact_number){
        $date = date('d-m-Y',strtotime($date));
        $time = date('h:i A',strtotime($time));
        $subject = "Your Demo is Confirmed!";
        $body = "<html>
                    <head>
                        <style>
                            body {
                                font-family: 'Arial', sans-serif;
                                background-color: #f5f5f5;
                                color: #333;
                                margin: 20px;
                            }
                            .container {
                                max-width: 600px;
                                margin: 0 auto;
                                padding: 20px;
                                background-color: #fff;
                                border-radius: 8px;
                                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                            }
                            h1 {
                                color: #3498db;
                                font-size: 24px;
                                text-align: center;
                            }
                            p {
                                line-height: 1.6;
                                font-size: 16px;
                            }
                            .demo-details {
                                background-color: #ecf0f1;
                                padding: 15px;
                                font-size: 16px;
                                border-radius: 5px;
                                margin: 20px 0;
                            }
                            .demo-details p {
                                margin: 5px 0;
                            }
                            .note {
                                background-color: #ecf0f1;
                                padding: 10px;
                                border-radius: 5px;
                                margin-top: 20px;
                            }
                            .footer {
                                margin-top: 20px;
                                font-size: 14px;
                                color: #777;
                                text-align: center;
                            }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <h1>Your Demo is Confirmed!</h1>
                            <p>Hi $lead_name,</p>
                            <p>Thank you for booking a demo with us! We’ve scheduled your session as follows:</p>
                            <div class='demo-details'>
                                <p><strong>Date:</strong> $date</p>
                                <p><strong>Time:</strong> $time</p>
                                <p><strong>Passcode:</strong> $passcode</p>
                                <p><strong>Link:</strong> <a href='$meeting_url' target='_blank'>$meeting_url</a></p>
                            </div>
                            <p>We look forward to showing you how <strong>$product_name</strong> can benefit you. Should you need to reschedule or have any questions, feel free to reply to this email or call us at <strong>$contact_number</strong>.</p>
                            <div class='footer'>
                                <p>Best regards,</p>
                                <p>Skill Park</p>
                            </div>
                        </div>
                    </body>
                </html>";
                
        send_email($emailTo, $lead_name, $subject, $body);
    }
    
    public function ajax_view($id){

        $country = $this->country_model->get([], ['id', 'title'])->getResultArray();
        $this->data['country_name'] = array_column($country, 'title', 'id');

        $lead_source = $this->lead_source_model->get([], ['id', 'title'])->getResultArray();
        $this->data['lead_source_list'] = array_column($lead_source,'title','id');
        
        $lead_status = $this->lead_status_model->get([], ['id', 'title'])->getResultArray();
        $this->data['lead_status_list'] = array_column($lead_status,'title','id');
        
        $tell_caller = $this->users_model->get(['role_id'=> 6], ['id', 'name'])->getResultArray();
        $this->data['telcaller_list'] = array_column($tell_caller,'name','id');

        $all_users = $this->users_model->get([], ['id', 'name'])->getResultArray();
        $this->data['all_users'] = array_column($all_users,'name','id');

        $course = $this->course_model->get([], ['id', 'title'])->getResultArray();
        $this->data['course_name'] = array_column($course, 'title', 'id');
        
        $this->data['view_data'] = $this->leads_model->get(['id' => $id])->getRowArray();
        $this->data['lead_history'] = $this->lead_activity_model->get(['lead_id' => $id])->getResultArray();
        echo view('App/Leads/ajax_view', $this->data);
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
        // return redirect()->to(base_url('app/leads/index'));
        return redirect()->to(previous_url());
    }
    
    public function get_leads_by_source() {
        $logger = service('logger');
        
        $lead_source_id = $this->request->getPost('lead_source_id');
        $tele_caller_id = $this->request->getPost('tele_caller_id');
        $created_at     = $this->request->getPost('created_at');
        if($tele_caller_id != ''){
            $where['telecaller_id'] = $tele_caller_id;
        }
        $where['lead_source_id'] = $lead_source_id;
        
        if(!empty($created_at)){
            $where['created_at >=']        = $created_at.' 00:00:00';
            $where['created_at <=']        = $created_at.' 23:59:00';
        }
        
        // log_message('error', '$leads '.print_r($_POST,true));
        // Fetch leads by lead source ID from the database
        $leads = $this->leads_model->get($where)->getResultArray();
        // $logger->error('Last Query: ' . db_connect()->getLastQuery());
        if (!empty($leads)) {
            foreach ($leads as $index => $lead) {
                echo '<tr>
                        <td>' . ($index + 1) . '</td>
                        <td>' . $lead['title'] . '<br>' . $lead['code'].$lead['phone'] . '</td>
                        <td>' . date('d-m-Y', strtotime($lead['created_at'])) . '</td>
                        <td>
                            <input type="checkbox" class="form-check-input" name="lead_id[]" value="' . $lead['id'] . '">
                        </td>
                      </tr>';
            }
        } else {
            echo '<tr><td colspan="3">No leads found for this source.</td></tr>';
        }
    }
    
    public function get_leads_by_source_re_assign() {
        $logger = service('logger');
        
        $lead_source_id = $this->request->getPost('lead_source_id');
        $tele_caller_id = $this->request->getPost('tele_caller_id');
        $lead_status_id = $this->request->getPost('lead_status_id');
        $from_date      = $this->request->getPost('from_date');
        $to_date        = $this->request->getPost('to_date');
        if($tele_caller_id != ''){
            $where['leads.telecaller_id'] = $tele_caller_id;
        }
        if($lead_status_id != ''){
            $where['leads.lead_status_id'] = $lead_status_id;
        }
        $where['leads.lead_source_id'] = $lead_source_id;
        
        if(!empty($from_date) && !empty($to_date)){
            $where['leads.created_at >=']        = $from_date.' 00:00:00';
            $where['leads.created_at <=']        = $to_date.' 23:59:00';
        }
        
        // $leads = $this->leads_model->get($where)->getResultArray();
        $leads = $this->leads_model->get_leads($where)->getResultArray();
        // log_message('error',print_r($leads,true));
        
        if (!empty($leads)) {
            foreach ($leads as $index => $lead) {
                echo '<tr>
                        <td>' . ($index + 1) . '</td>
                        <td>' . $lead['title'] . '<br>' . $lead['code'].$lead['phone'] . '</td>
                        <td>' . $lead['status_title'] . '</td>
                        <td style="white-space: nowrap;">' . (!empty($lead['course_name']) ? $lead['course_name'] : 'N/A') . '</td>
                        <td>' . (!empty($lead['remarks']) ? $lead['remarks'] : 'N/A') . '</td>
                        <td style="white-space: nowrap;">' . date('d-m-Y', strtotime($lead['created_at'])) . '</td>
                        <td>
                            <input type="checkbox" class="form-check-input" name="lead_id[]" value="' . $lead['id'] . '">
                        </td>
                      </tr>';
            }
        } else {
            echo '<tr><td colspan="7">No leads found for this source.</td></tr>';
        }
    }
    
    public function call_test()
    {
        $exotel = new ExotelController();
        $result = $exotel->initiateCall('6238975936', '7034756047');
        
        if ($result['success']) {
            echo $result['message'] . "<br>";
            echo "Response: " . $result['response'];
        } else {
            echo $result['message'] . "<br>";
            echo "Error: " . $result['response'];
        }
    }

}
