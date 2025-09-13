<?php
namespace App\Controllers\App;
use App\Models\Users_model;
use App\Models\Teams_model;
class Telecaller extends AppBaseController
{
    private $users_model;
    private $teams_model;
    public function __construct()
    {
        parent::__construct();
        $this->users_model = new Users_model();
        $this->teams_model = new Teams_model();
    }

    public function index(){
        $this->data['list_items'] = $this->users_model->get(['role_id' => 6])->getResultArray();
        $this->data['page_title'] = 'Academic Counselor';
        $this->data['page_name'] = 'Telecaller/index';
        return view('App/index', $this->data);
    }
    
    public function ajax_add(){
        $this->data['country_code'] = get_country_code();
        $this->data['teams_list'] = $this->teams_model->get()->getResultArray();
        echo view('App/Telecaller/ajax_add', $this->data);
    }
    
    public function add(){
        if ($this->request->getMethod() === 'post'){
            
            $code = $this->request->getPost('code');
            $phone = $this->request->getPost('phone');
            $email = $this->request->getPost('email');
            $extension = $this->request->getPost('extension_number'); 
            $check_phone_duplication = $this->users_model->get(['code' => $code ,'phone' => $phone])->getNumRows();
            $check_email_duplication = $this->users_model->get(['email' => $email])->getNumRows();
            
            if($check_phone_duplication == 0 && $check_email_duplication == 0) {
                
            $data = [
                'name'      => $this->request->getPost('name'),
                'email'     => $email,
                'code'      => $code,
                'phone'     => $phone,
                'ext_no'    => $extension,
                'team_id'   => $this->request->getPost('team_id'),
                'role_id'   => 6,
                'is_team_lead' => $this->request->getPost('is_team_lead') == 1 ? 1 : 0,
                'is_team_manager' => $this->request->getPost('is_team_manager') == 1 ? 1 : 0,
                'is_senior_manager' => $this->request->getPost('is_senior_manager') == 1 ? 1 : 0,
                'current_role' => $this->request->getPost('is_team_lead') == 1 ? 'team_lead' : 'academic_counsellor',
                'password'  => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => get_user_id()
            ];
            
            $profile_picture = $this->upload_file('users/profile_picture','profile_picture');
            
            if($profile_picture && valid_file($profile_picture['file'])){
				$data['profile_picture'] = $profile_picture['file'];
			}  
            
            
            $team_manager_id = $this->users_model->add($data);
            
            if($this->request->getPost('is_team_lead') == 1){
                
                $team_data =[
                    'team_lead_id' => $team_manager_id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => get_user_id()
                ];
                
                $this->teams_model->edit($team_data, ['id' => $this->request->getPost('team_id')]);
            }
            
            if ($team_manager_id){
                session()->setFlashdata('message_success', "Academic Counselor Added Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            } 
            
            }else{
                  session()->setFlashdata('message_danger', "user already exists"); 
            }
            

        }
        return redirect()->to(base_url('app/telecaller/index'));
    }
    
    public function ajax_edit($id){
        $this->data['country_code'] = get_country_code();
        $this->data['edit_data'] = $this->users_model->get(['id' => $id])->getRowArray();
        $this->data['teams_list'] = $this->teams_model->get()->getResultArray();
        echo view('App/Telecaller/ajax_edit', $this->data);
    }

    public function edit($id){
        if ($this->request->getMethod() === 'post'){
            
            $code = $this->request->getPost('code');
            $phone = $this->request->getPost('phone');
            $email = $this->request->getPost('email');
            $extension = $this->request->getPost('extension_number');
            $check_phone_duplication = $this->users_model->get(['code' => $code ,'phone' => $phone,' id!= ' => $id])->getNumRows();
            $check_email_duplication = $this->users_model->get(['email' => $email,'id !=' => $id])->getNumRows();
            $data = [
                'name'      => $this->request->getPost('name'),
                'email'     => $email,
                'code'      => $code,
                'phone'     => $phone,
                'ext_no'    => $extension,
                'team_id'   => $this->request->getPost('team_id'),
                'is_team_lead' => $this->request->getPost('is_team_lead') == 1 ? 1 : 0,
                'is_team_manager' => $this->request->getPost('is_team_manager') == 1 ? 1 : 0,
                'is_senior_manager' => $this->request->getPost('is_senior_manager') == 1 ? 1 : 0,
                'current_role' => $this->request->getPost('is_team_lead') == 1 ? 'team_lead' : 'academic_counsellor',
                'updated_by' => get_user_id(),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            
            $profile_picture = $this->upload_file('users/profile_picture','profile_picture');
            
            if($profile_picture && valid_file($profile_picture['file'])){
				$data['profile_picture'] = $profile_picture['file'];
			}
			
			if($this->request->getPost('is_team_lead') == 1){
                
                $team_data =[
                    'team_lead_id' => $id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => get_user_id()
                ];
                
                $this->teams_model->edit($team_data, ['id' => $this->request->getPost('team_id')]);
            }
             
            $response = $this->users_model->edit($data, ['id' => $id]);
            if ($response){
                session()->setFlashdata('message_success', "Academic Counselor Updated Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/telecaller/index'));
    }

    public function ajax_view($id){
        $this->data['view_data'] = $this->users_model->get(['id' => $id])->getRowArray();
        echo view('App/Telecaller/ajax_view', $this->data);
    }
    
    public function delete($id){
        if ($id > 0){
            if ($this->users_model->remove(['id' => $id])){
                session()->setFlashdata('message_success', "Academic Counselor Deleted Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }else{
            session()->setFlashdata('message_danger', "Something went wrong! Try Again");
        }
        return redirect()->to(base_url('app/telecaller/index'));
    }
    
}
