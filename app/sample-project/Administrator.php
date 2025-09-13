<?php
namespace App\Controllers\App;
use App\Models\Users_model;
class Administrator extends AppBaseController
{
    private $users_model;
    public function __construct()
    {
        parent::__construct();
        $this->users_model = new Users_model();
        
    }

    public function index(){
        $this->data['list_items'] = $this->users_model->get(['role_id' => 11])->getResultArray();
        $this->data['page_title'] = 'Administrator';
        $this->data['page_name'] = 'Administrator/index';
        return view('App/index', $this->data);
    }
    
    public function ajax_add(){
        $this->data['country_code'] = get_country_code();
        echo view('App/Administrator/ajax_add', $this->data);
    }
    
    public function add(){
        if ($this->request->getMethod() === 'post'){
            
            $code = $this->request->getPost('code');
            $phone = $this->request->getPost('phone');
            $email = $this->request->getPost('email');
            $check_phone_duplication = $this->users_model->get(['code' => $code ,'phone' => $phone])->getNumRows();
            $check_email_duplication = $this->users_model->get(['email' => $email])->getNumRows();
            
            if($check_phone_duplication == 0 && $check_email_duplication == 0){
                $data = [
                    'name'      => $this->request->getPost('name'),
                    'email'     => $email,
                    'code'     => $code,
                    'phone'     => $phone,
                    'role_id'   => 11,
                    'password'  => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => get_user_id()
                ];
                
                 $profile_picture = $this->upload_file('users/profile_picture','profile_picture');
                
                if($profile_picture && valid_file($profile_picture['file'])){
    				$data['profile_picture'] = $profile_picture['file'];
    			}
                 
                $admin_id = $this->users_model->add($data);
                if ($admin_id){
                    session()->setFlashdata('message_success', "Administrator Added Successfully!");
                }else{
                    session()->setFlashdata('message_danger', "Something went wrong! Try Again");
                }
            }else{
                session()->setFlashdata('message_danger', "User Already Exists");
            }
            
        }
        return redirect()->to(base_url('app/administrator/index'));
    }
    
    public function ajax_edit($id){
        $this->data['country_code'] = get_country_code();
        $this->data['edit_data'] = $this->users_model->get(['id' => $id])->getRowArray();
        echo view('App/Administrator/ajax_edit', $this->data);
    }

    public function edit($id){
        if ($this->request->getMethod() === 'post'){
            
            $code = $this->request->getPost('code');
            $phone = $this->request->getPost('phone');
            $email = $this->request->getPost('email');
            
            $check_phone_duplication = $this->users_model->get(['code' => $code , 'phone' => $phone, 'id !=' => $id])->getNumRows();
            $check_email_duplication = $this->users_model->get(['email' => $email, 'id !=' => $id])->getNumRows();
            if($check_phone_duplication == 0 && $check_email_duplication == 0){
                $data = [
                    'name'      => $this->request->getPost('name'),
                    'email'     => $this->request->getPost('email'),
                    'code'     => $this->request->getPost('code'),
                    'phone'     => $this->request->getPost('phone'),
                    'updated_by' => get_user_id(),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $profile_picture = $this->upload_file('users/profile_picture','profile_picture');
                
                if($profile_picture && valid_file($profile_picture['file'])){
                    $data['profile_picture'] = $profile_picture['file'];
                } 
                 
                $response = $this->users_model->edit($data, ['id' => $id]);
                if ($response){
                    session()->setFlashdata('message_success', "Administrator Updated Successfully!");
                }else{
                    session()->setFlashdata('message_danger', "Something went wrong! Try Again");
                }
            }else{
                session()->setFlashdata('message_danger', "User Already Exists");
            }
            
        }
        return redirect()->to(base_url('app/administrator/index'));
    }

    public function ajax_view($id){
        $this->data['view_data'] = $this->users_model->get(['id' => $id])->getRowArray();
        echo view('App/Administrator/ajax_view', $this->data);
    }
    
    public function delete($id){
        if ($id > 0){
            if ($this->users_model->remove(['id' => $id])){
                session()->setFlashdata('message_success', "Administrator Deleted Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }else{
            session()->setFlashdata('message_danger', "Something went wrong! Try Again");
        }
        return redirect()->to(base_url('app/administrator/index'));
    }
    
}
