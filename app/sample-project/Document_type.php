<?php
namespace App\Controllers\App;
use App\Models\Document_type_model;
class Document_type extends AppBaseController
{
    private $document_type_model;
    public function __construct()
    {
        parent::__construct();
        $this->document_type_model = new Document_type_model();

    }

    public function index(){
        $this->data['list_items'] = $this->document_type_model->get()->getResultArray();
        $this->data['page_title'] = 'Document Type';
        $this->data['page_name'] = 'Document_type/index';
        return view('App/index', $this->data);
    }

    public function ajax_add(){
        echo view('App/Document_type/ajax_add', $this->data);
    }

    public function add(){
        if ($this->request->getMethod() === 'post'){
            $data = [
                'title' => $this->request->getPost('title'),
             
                'created_by' => get_user_id(),
                'created_at' => date('Y-m-d H:i:s'),
            
            ];
            $doc_id = $this->document_type_model->add($data);
            if ($doc_id){
                session()->setFlashdata('message_success', "Document type Added Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/document_type/index'));
    }

    public function ajax_edit($id){
        $this->data['edit_data'] = $this->document_type_model->get(['id' => $id])->getRowArray();
        echo view('App/Document_type/ajax_edit', $this->data);
    }

    public function edit($id){
        if ($this->request->getMethod() === 'post'){
            $data = [
                'title' => $this->request->getPost('title'),
     
                'updated_by' => get_user_id(),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $response = $this->document_type_model->edit($data, ['id' => $id]);
            if ($response){
                session()->setFlashdata('message_success', "Document type Updated Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/document_type/index'));
    }

    public function ajax_view($id){
        $this->data['view_data'] = $this->document_type_model->get(['id' => $id])->getRowArray();
        echo view('App/Document_type/ajax_view', $this->data);
    }

    public function delete($id){
        if ($id > 0){
            if ($this->document_type_model->remove(['id' => $id])){
                session()->setFlashdata('message_success', "Document type Deleted Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }else{
            session()->setFlashdata('message_danger', "Something went wrong! Try Again");
        }
        return redirect()->to(base_url('app/document_type/index'));
    }
}
