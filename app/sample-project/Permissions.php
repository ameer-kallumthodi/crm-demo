<?php
namespace App\Controllers\App;
use App\Models\Permissions_model;
class Permissions extends AppBaseController
{
    private $permissions_model;
    public function __construct()
    {
        parent::__construct();
        $this->permissions_model = new Permissions_model();

    }

    public function index(){
        $this->data['list_items'] = $this->permissions_model->get()->getResultArray();
        $this->data['page_title'] = 'Permissions';
        $this->data['page_name'] = 'Permissions/index';
        return view('App/index', $this->data);
    }

    public function ajax_add(){
        echo view('App/Permissions/ajax_add', $this->data);
    }

    public function add(){
        if ($this->request->getMethod() === 'post'){
            $data = [
                'title' => $this->request->getPost('title'),
                'slug' =>  $this->create_slug($this->request->getPost('slug')),
                'created_by' => get_user_id(),
                'created_at' => date('Y-m-d H:i:s'),
            ];
            $permission_id = $this->permissions_model->add($data);
            if ($permission_id){
                session()->setFlashdata('message_success', "Permissions Added Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/permissions/index'));
    }

    public function ajax_edit($id){
        $this->data['edit_data'] = $this->permissions_model->get(['id' => $id])->getRowArray();
        echo view('App/Permissions/ajax_edit', $this->data);
    }

    public function edit($id){
        if ($this->request->getMethod() === 'post'){
            $data = [
                'title' => $this->request->getPost('title'),
                'slug' =>  $this->create_slug($this->request->getPost('slug')),
                'updated_by' => get_user_id(),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $response = $this->permissions_model->edit($data, ['id' => $id]);
            if ($response){
                session()->setFlashdata('message_success', "Permissions Updated Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/permissions/index'));
    }

    public function ajax_view($id){
        $this->data['view_data'] = $this->permissions_model->get(['id' => $id])->getRowArray();
        echo view('App/Permissions/ajax_view', $this->data);
    }

    public function delete($id){
        if ($id > 0){
            if ($this->permissions_model->remove(['id' => $id])){
                session()->setFlashdata('message_success', "Permissions Deleted Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }else{
            session()->setFlashdata('message_danger', "Something went wrong! Try Again");
        }
        return redirect()->to(base_url('app/permissions/index'));
    }
}
