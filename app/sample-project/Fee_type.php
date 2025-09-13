<?php
namespace App\Controllers\App;
use App\Models\Fee_type_model;
class Fee_type extends AppBaseController
{
    private $fee_type_model;
    public function __construct()
    {
        parent::__construct();
        $this->fee_type_model = new Fee_type_model();

    }

    public function index(){
        $this->data['list_items'] = $this->fee_type_model->get()->getResultArray();
        $this->data['page_title'] = 'Fee Type';
        $this->data['page_name'] = 'Fee_type/index';
        return view('App/index', $this->data);
    }

    public function ajax_add(){
        echo view('App/Fee_type/ajax_add', $this->data);
    }

    public function add(){
        if ($this->request->getMethod() === 'post'){
            $data = [
                'title' => $this->request->getPost('title'),
             
                'created_by' => get_user_id(),
                'created_at' => date('Y-m-d H:i:s'),
            
            ];
            $type_id = $this->fee_type_model->add($data);
            if ($type_id){
                session()->setFlashdata('message_success', "Course Added Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/fee_type/index'));
    }

    public function ajax_edit($id){
        $this->data['edit_data'] = $this->fee_type_model->get(['id' => $id])->getRowArray();
        echo view('App/Fee_type/ajax_edit', $this->data);
    }

    public function edit($id){
        if ($this->request->getMethod() === 'post'){
            $data = [
                'title' => $this->request->getPost('title'),
     
                'updated_by' => get_user_id(),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $response = $this->fee_type_model->edit($data, ['id' => $id]);
            if ($response){
                session()->setFlashdata('message_success', "Course Updated Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/fee_type/index'));
    }

    public function ajax_view($id){
        $this->data['view_data'] = $this->fee_type_model->get(['id' => $id])->getRowArray();
        echo view('App/Fee_type/ajax_view', $this->data);
    }

    public function delete($id){
        if ($id > 0){
            if ($this->fee_type_model->remove(['id' => $id])){
                session()->setFlashdata('message_success', "Course Deleted Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }else{
            session()->setFlashdata('message_danger', "Something went wrong! Try Again");
        }
        return redirect()->to(base_url('app/fee_type/index'));
    }
}
