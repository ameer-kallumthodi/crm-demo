<?php
namespace App\Controllers\App;
use App\Models\Users_model;
class Profile extends AppBaseController
{
    private $users_model;
    public function __construct()
    {
        parent::__construct();
        $this->users_model = new Users_model();
        
    }

    public function index($id){
        
        $this->data['edit_data'] = $this->users_model->get(['id' => $id])->getRowArray();
        $this->data['page_title'] = 'Profile';
        $this->data['page_name'] = 'Profile/index';
        return view('App/index', $this->data);
    }

    public function edit($id){
        if ($this->request->getMethod() === 'post'){
            $data = [
                'name'      => $this->request->getPost('name'),
                'email'     => $this->request->getPost('email'),
                'phone'     => $this->request->getPost('phone'),
                'updated_by' => get_user_id(),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            
//             $profile_picture = $this->upload_file('users/employee','profile_picture');
            
//             if($profile_picture && valid_file($profile_picture['file'])){
// 				$data['profile_picture'] = $profile_picture['file'];
// 			}
			
            $response = $this->users_model->edit($data, ['id' => $id]);
            if ($response){
                session()->setFlashdata('message_success', "Profile Updated Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/profile/index/'.$id));
    }
    
    public function reset_password($id){
        if ($this->request->getMethod() === 'post'){
            $data = [
                'password'  => $this->users_model->password_hash($this->request->getPost('password')),
                'updated_by' => get_user_id(),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            
            $response = $this->users_model->edit($data, ['id' => $id]);
            if ($response){
                session()->setFlashdata('message_success', "Password Reset Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/dashboard/index/'));
    }

}
