<?php
namespace App\Controllers\App;
use App\Models\Boards_model;
class Boards extends AppBaseController
{
    private $boards_model;
    public function __construct()
    {
        parent::__construct();
        $this->boards_model = new Boards_model();

    }

    public function index(){
        $this->data['list_items'] = $this->boards_model->get()->getResultArray();
        $this->data['page_title'] = 'Board';
        $this->data['page_name'] = 'Boards/index';
        return view('App/index', $this->data);
    }

    public function ajax_add(){
        echo view('App/Boards/ajax_add', $this->data);
    }

    public function add(){
        if ($this->request->getMethod() === 'post'){
            $data = [
                'title' => $this->request->getPost('title'),
                'created_by' => get_user_id(),
                'created_at' => date('Y-m-d H:i:s'),
                
            ];
            $lead_status_id = $this->boards_model->add($data);
            if ($lead_status_id){
                session()->setFlashdata('message_success', "Board Added Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/boards/index'));
    }

    public function ajax_edit($id){
        $this->data['edit_data'] = $this->boards_model->get(['id' => $id])->getRowArray();
        echo view('App/Boards/ajax_edit', $this->data);
    }

    public function edit($id){
        if ($this->request->getMethod() === 'post'){
            $data = [
                'title' => $this->request->getPost('title'),
                'updated_by' => get_user_id(),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $response = $this->boards_model->edit($data, ['id' => $id]);
            if ($response){
                session()->setFlashdata('message_success', "Board Updated Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/boards/index'));
    }

    public function ajax_view($id){
        $this->data['view_data'] = $this->boards_model->get(['id' => $id])->getRowArray();
        echo view('App/Boards/ajax_view', $this->data);
    }

    public function delete($id){
        if ($id > 0){
            if ($this->boards_model->remove(['id' => $id])){
                session()->setFlashdata('message_success', "Board Deleted Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }else{
            session()->setFlashdata('message_danger', "Something went wrong! Try Again");
        }
        return redirect()->to(base_url('app/boards/index'));
    }
}
