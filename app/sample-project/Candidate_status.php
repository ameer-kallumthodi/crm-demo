<?php
namespace App\Controllers\App;
use App\Models\Candidate_status_model;
class Candidate_status extends AppBaseController
{
    private $candidate_status_model;
    public function __construct()
    {
        parent::__construct();
        $this->candidate_status_model = new Candidate_status_model();

    }

    public function index(){
        $this->data['list_items'] = $this->candidate_status_model->get()->getResultArray();
        $this->data['page_title'] = 'Admission Status';
        $this->data['page_name'] = 'Candidate_status/index';
        return view('App/index', $this->data);
    }

    public function ajax_add(){
        echo view('App/Candidate_status/ajax_add', $this->data);
    }

    public function add(){
        if ($this->request->getMethod() === 'post'){
            $data = [
                'title' => $this->request->getPost('title'),
                'created_by' => get_user_id(),
                'created_on' => date('Y-m-d H:i:s'),
                
            ];
            $lead_status_id = $this->candidate_status_model->add($data);
            if ($lead_status_id){
                session()->setFlashdata('message_success', "Admission Status Added Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/candidate_status/index'));
    }

    public function ajax_edit($id){
        $this->data['edit_data'] = $this->candidate_status_model->get(['id' => $id])->getRowArray();
        echo view('App/Candidate_status/ajax_edit', $this->data);
    }

    public function edit($id){
        if ($this->request->getMethod() === 'post'){
            $data = [
                'title' => $this->request->getPost('title'),
                'updated_by' => get_user_id(),
                'updated_on' => date('Y-m-d H:i:s'),
            ];
            $response = $this->candidate_status_model->edit($data, ['id' => $id]);
            if ($response){
                session()->setFlashdata('message_success', "Admission Status Updated Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/candidate_status/index'));
    }

    public function ajax_view($id){
        $this->data['view_data'] = $this->candidate_status_model->get(['id' => $id])->getRowArray();
        echo view('App/Candidate_status/ajax_view', $this->data);
    }

    public function delete($id){
        if ($id > 0){
            if ($this->candidate_status_model->remove(['id' => $id])){
                session()->setFlashdata('message_success', "Admission Status Deleted Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }else{
            session()->setFlashdata('message_danger', "Something went wrong! Try Again");
        }
        return redirect()->to(base_url('app/candidate_status/index'));
    }
}
