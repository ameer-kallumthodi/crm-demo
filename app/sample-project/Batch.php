<?php
namespace App\Controllers\App;
use App\Models\Batch_model;
class Batch extends AppBaseController
{
    private $batch_model;
    public function __construct()
    {
        parent::__construct();
        $this->batch_model = new Batch_model();
    }

    public function index(){
        $this->data['list_items'] = $this->batch_model->get()->getResultArray();
        $this->data['page_title'] = 'Batch';
        $this->data['page_name'] = 'Batch/index';
        return view('App/index', $this->data);
    }

    public function ajax_add(){
        echo view('App/Batch/ajax_add', $this->data);
    }

    public function add(){
        if ($this->request->getMethod() === 'post'){
            $data = [
                'title' => $this->request->getPost('title'),
                'created_by' => get_user_id(),
                'created_at' => date('Y-m-d H:i:s'),
            ];
            $batch_id = $this->batch_model->add($data);
            if ($batch_id){
                session()->setFlashdata('message_success', "Batch Added Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/batch/index'));
    }

    public function ajax_edit($id){
        $this->data['edit_data'] = $this->batch_model->get(['id' => $id])->getRowArray();
        echo view('App/Batch/ajax_edit', $this->data);
    }

    public function edit($id){
        if ($this->request->getMethod() === 'post'){
            $data = [
                'title' => $this->request->getPost('title'),
                'updated_by' => get_user_id(),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $response = $this->batch_model->edit($data, ['id' => $id]);
            if ($response){
                session()->setFlashdata('message_success', "Batch Updated Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/batch/index'));
    }

    public function ajax_view($id){
        $this->data['view_data'] = $this->batch_model->get(['id' => $id])->getRowArray();
        echo view('App/Batch/ajax_view', $this->data);
    }

    public function delete($id){
        if ($id > 0){
            if ($this->batch_model->remove(['id' => $id])){
                session()->setFlashdata('message_success', "Batch Deleted Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }else{
            session()->setFlashdata('message_danger', "Something went wrong! Try Again");
        }
        return redirect()->to(base_url('app/batch/index'));
    }
}
