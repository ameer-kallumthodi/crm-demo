<?php
namespace App\Controllers\App;
use App\Models\Lead_source_model;
class Lead_source extends AppBaseController
{
    private $lead_source_model;
    public function __construct()
    {
        parent::__construct();
        $this->lead_source_model = new Lead_source_model();

    }

    public function index(){
        $this->data['list_items'] = $this->lead_source_model->get()->getResultArray();
        $this->data['page_title'] = 'Lead Source';
        $this->data['page_name'] = 'Lead_source/index';
        return view('App/index', $this->data);
    }

    public function ajax_add(){
        echo view('App/Lead_source/ajax_add', $this->data);
    }

    public function add(){
        if ($this->request->getMethod() === 'post'){
            $data = [
                'title' => $this->request->getPost('title'),
                'created_by' => get_user_id(),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_by' => get_user_id(),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $lead_source_id = $this->lead_source_model->add($data);
            if ($lead_source_id){
                session()->setFlashdata('message_success', "Lead Source Added Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/lead_source/index'));
    }

    public function ajax_edit($id){
        $this->data['edit_data'] = $this->lead_source_model->get(['id' => $id])->getRowArray();
        echo view('App/Lead_source/ajax_edit', $this->data);
    }

    public function edit($id){
        if ($this->request->getMethod() === 'post'){
            $data = [
                'title' => $this->request->getPost('title'),
                'updated_by' => get_user_id(),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $response = $this->lead_source_model->edit($data, ['id' => $id]);
            if ($response){
                session()->setFlashdata('message_success', "Lead Source Updated Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/lead_source/index'));
    }

    public function ajax_view($id){
        $this->data['view_data'] = $this->lead_source_model->get(['id' => $id])->getRowArray();
        echo view('App/Lead_source/ajax_view', $this->data);
    }

    public function delete($id){
        if ($id > 0){
            if ($this->lead_source_model->remove(['id' => $id])){
                session()->setFlashdata('message_success', "Lead Source Deleted Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }else{
            session()->setFlashdata('message_danger', "Something went wrong! Try Again");
        }
        return redirect()->to(base_url('app/lead_source/index'));
    }
}
