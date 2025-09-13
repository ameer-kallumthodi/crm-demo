<?php
namespace App\Controllers\App;
use App\Models\Visa_type_model;
class Visa_type extends AppBaseController
{
    private $visa_type_model;
    public function __construct()
    {
        parent::__construct();
        $this->visa_type_model = new Visa_type_model();

    }

    public function index(){
        $this->data['list_items'] = $this->visa_type_model->get()->getResultArray();
        $this->data['page_title'] = 'Visatype';
        $this->data['page_name'] = 'Visatype/index';
        return view('App/index', $this->data);
    }

    public function ajax_add(){
        echo view('App/Visatype/ajax_add', $this->data);
    }

    public function add(){
        if ($this->request->getMethod() === 'post'){
            $data = [
                'title' => $this->request->getPost('title'),
             
                'created_by' => get_user_id(),
                'created_at' => date('Y-m-d H:i:s'),
            
            ];
            $country_id = $this->visa_type_model->add($data);
            if ($country_id){
                session()->setFlashdata('message_success', "Visatype Added Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/visa_type/index'));
    }

    public function ajax_edit($id){
        $this->data['edit_data'] = $this->visa_type_model->get(['id' => $id])->getRowArray();
        echo view('App/Visatype/ajax_edit', $this->data);
    }

    public function edit($id){
        if ($this->request->getMethod() === 'post'){
            $data = [
                'title' => $this->request->getPost('title'),
     
                'updated_by' => get_user_id(),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $response = $this->visa_type_model->edit($data, ['id' => $id]);
            if ($response){
                session()->setFlashdata('message_success', "Visatype Updated Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/visa_type/index'));
    }

    public function ajax_view($id){
        $this->data['view_data'] = $this->visa_type_model->get(['id' => $id])->getRowArray();
        echo view('App/Visatype/ajax_view', $this->data);
    }

    public function delete($id){
        if ($id > 0){
            if ($this->visa_type_model->remove(['id' => $id])){
                session()->setFlashdata('message_success', "Visatype Deleted Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }else{
            session()->setFlashdata('message_danger', "Something went wrong! Try Again");
        }
        return redirect()->to(base_url('app/visa_type/index'));
    }
}
