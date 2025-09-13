<?php
namespace App\Controllers\App;
use App\Models\Lead_status_model;
class Lead_status extends AppBaseController
{
    private $lead_status_model;
    public function __construct()
    {
        parent::__construct();
        $this->lead_status_model = new Lead_status_model();

    }

    public function index(){
        $this->data['list_items'] = $this->lead_status_model->get()->getResultArray();
        $this->data['page_title'] = 'Lead Status';
        $this->data['page_name'] = 'Lead_status/index';
        return view('App/index', $this->data);
    }

    public function ajax_add(){
        echo view('App/Lead_status/ajax_add', $this->data);
    }

    public function add(){
        if ($this->request->getMethod() === 'post'){
            $data = [
                'title' => $this->request->getPost('title'),
                'created_by' => get_user_id(),
                'created_on' => date('Y-m-d H:i:s'),
                
            ];
            $lead_status_id = $this->lead_status_model->add($data);
            if ($lead_status_id){
                session()->setFlashdata('message_success', "Lead Status Added Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/lead_status/index'));
    }

    public function ajax_edit($id){
        $this->data['edit_data'] = $this->lead_status_model->get(['id' => $id])->getRowArray();
        echo view('App/Lead_status/ajax_edit', $this->data);
    }

    public function edit($id){
        if ($this->request->getMethod() === 'post'){
            $data = [
                'title' => $this->request->getPost('title'),
                'updated_by' => get_user_id(),
                'updated_on' => date('Y-m-d H:i:s'),
            ];
            $response = $this->lead_status_model->edit($data, ['id' => $id]);
            if ($response){
                session()->setFlashdata('message_success', "Lead Status Updated Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/lead_status/index'));
    }

    public function ajax_view($id){
        $this->data['view_data'] = $this->lead_status_model->get(['id' => $id])->getRowArray();
        echo view('App/Lead_status/ajax_view', $this->data);
    }

    public function delete($id){
        if ($id > 0){
            if ($this->lead_status_model->remove(['id' => $id])){
                session()->setFlashdata('message_success', "Lead Status Deleted Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }else{
            session()->setFlashdata('message_danger', "Something went wrong! Try Again");
        }
        return redirect()->to(base_url('app/lead_status/index'));
    }
}
