<?php
namespace App\Controllers\App;
use App\Models\Lead_stage_model;
class Lead_stage extends AppBaseController
{
    private $lead_stage_model;
    public function __construct()
    {
        parent::__construct();
        $this->lead_stage_model = new Lead_stage_model();

    }

    public function index(){
        $this->data['list_items'] = $this->lead_stage_model->get()->getResultArray();
        $this->data['page_title'] = 'Lead Stage';
        $this->data['page_name'] = 'Lead_stage/index';
        return view('App/index', $this->data);
    }

    public function ajax_add(){
        echo view('App/Lead_stage/ajax_add', $this->data);
    }

    public function add(){
        if ($this->request->getMethod() === 'post'){
            $data = [
                'title' => $this->request->getPost('title'),
                'created_by' => get_user_id(),
                'created_on' => date('Y-m-d H:i:s'),
                
            ];
            $lead_stage_id = $this->lead_stage_model->add($data);
            if ($lead_stage_id){
                session()->setFlashdata('message_success', "Lead Stage Added Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/Lead_stage/index'));
    }

    public function ajax_edit($id){
        $this->data['edit_data'] = $this->lead_stage_model->get(['id' => $id])->getRowArray();
        echo view('App/Lead_stage/ajax_edit', $this->data);
    }

    public function edit($id){
        if ($this->request->getMethod() === 'post'){
            $data = [
                'title' => $this->request->getPost('title'),
                'updated_by' => get_user_id(),
                'updated_on' => date('Y-m-d H:i:s'),
            ];
            $response = $this->lead_stage_model->edit($data, ['id' => $id]);
            if ($response){
                session()->setFlashdata('message_success', "Lead Stage Updated Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/lead_stage/index'));
    }

    public function ajax_view($id){
        $this->data['view_data'] = $this->lead_stage_model->get(['id' => $id])->getRowArray();
        echo view('App/Lead_stage/ajax_view', $this->data);
    }

    public function delete($id){
        if ($id > 0){
            if ($this->lead_stage_model->remove(['id' => $id])){
                session()->setFlashdata('message_success', "Lead Stage Deleted Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }else{
            session()->setFlashdata('message_danger', "Something went wrong! Try Again");
        }
        return redirect()->to(base_url('app/lead_stage/index'));
    }
}
