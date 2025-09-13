<?php
namespace App\Controllers\App;
use App\Models\Users_model;
use App\Models\Teams_model;
class Teams extends AppBaseController
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
        $where = [];
        if(!(is_admin())){
            $team_id = $this->users_model->get(['id' => get_user_id()], ['team_id'])->getRow()->team_id;
            $where = ['id' => $team_id];
        }
        $this->data['list_items'] = $this->teams_model->get($where)->getResultArray();
        $managers = $this->users_model->get(['role_id' => 4])->getResultArray();
        $this->data['team_manager_name'] = array_column($managers, 'name', 'id');
        
        $team_lead = $this->users_model->get(['role_id' => 6, 'is_team_lead' => 1])->getResultArray();
        $this->data['team_lead_name'] = array_column($team_lead, 'name', 'id');
        
        $this->data['page_title'] = 'Teams';
        $this->data['page_name'] = 'Teams/index';
        return view('App/index', $this->data);
    }
    
    public function ajax_add(){
        $this->data['team_managers'] =  $this->users_model->get(['role_id' => 4])->getResultArray();
        $this->data['team_leads'] =  $this->users_model->get(['role_id' => 6, 'is_team_lead' => 1])->getResultArray();
        
        echo view('App/Teams/ajax_add', $this->data);
    }
    
    public function add(){
        if ($this->request->getMethod() === 'post'){
            $data = [
                'title'      => $this->request->getPost('title'),
                // 'team_lead_id' => $this->request->getPost('team_lead_id'),
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => get_user_id()
       
            ];
            
            $team_manager_id = $this->teams_model->add($data);
            if ($team_manager_id){
                session()->setFlashdata('message_success', "Team Added Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/teams/index'));
    }
    
        public function ajax_edit($id){
        $this->data['team_managers'] =  $this->users_model->get(['role_id' => 4])->getResultArray();
        $this->data['team_leads'] =  $this->users_model->get(['role_id' => 6, 'is_team_lead' => 1])->getResultArray();
        
        $this->data['edit_data'] = $this->teams_model->get(['id' => $id])->getRowArray();
        echo view('App/Teams/ajax_edit', $this->data);
    }

    public function edit($id){
        if ($this->request->getMethod() === 'post'){
            $data = [
                'title'      => $this->request->getPost('title'),
                'team_lead_id' => $this->request->getPost('team_lead_id'),
                'updated_by' => get_user_id(),
                'updated_at' => date('Y-m-d H:i:s'),
      
            ];
            $response = $this->teams_model->edit($data, ['id' => $id]);
            if ($response){
                session()->setFlashdata('message_success', "Teams Updated Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/teams/index'));
    }
    
    public function ajax_team_members($id){
        $this->data['team_members'] =  $this->users_model->get(['team_id' => $id])->getResultArray();
        
        $this->data['team_details'] = $this->teams_model->get(['id'=>$id])->getRowArray();
        
        $this->data['team_lead'] = $this->users_model->get(['id' => $this->data['team_details']['team_lead_id']])->getRowArray();
  
        echo view('App/Teams/ajax_team_members', $this->data);
    }


    public function ajax_view($id){
        $managers = $this->users_model->get(['role_id' => 4])->getResultArray();
        $this->data['team_manager_name'] = array_column($managers, 'name', 'id');
        
        $team_lead = $this->users_model->get(['role_id' => 6, 'is_team_lead' => 1])->getResultArray();
        $this->data['team_lead_name'] = array_column($team_lead, 'name', 'id'); 
        
        $this->data['view_data'] = $this->teams_model->get(['id' => $id])->getRowArray();
        
        echo view('App/Teams/ajax_view', $this->data);
    }
    
    public function delete($id){
        if ($id > 0){
            if ($this->teams_model->remove(['id' => $id])){
                session()->setFlashdata('message_success', "Teams Deleted Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }else{
            session()->setFlashdata('message_danger', "Something went wrong! Try Again");
        }
        return redirect()->to(base_url('app/teams/index'));
    }
    
    public function remove_member($id) {
        if ($id > 0) {
            $user_details = $this->users_model->get()->getRowArray();
            $user_details = ['team_id' => null];
            $response = $this->users_model->edit($user_details, ['id' => $id]); 
            if ($response){
                session()->setFlashdata('message_success', "Team Member Removed Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
    
        return redirect()->to(base_url('app/teams/index'));
    }

    
}
