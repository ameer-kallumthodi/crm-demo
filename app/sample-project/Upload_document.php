<?php
namespace App\Controllers\App;
use App\Models\Upload_document_model;
use App\Models\Document_type_model;


class Upload_document extends AppBaseController
{
    private $upload_model;
    private $document_type_model;

    public function __construct()
    {
        parent::__construct();
        $this->upload_model = new Upload_document_model();
        $this->document_type_model = new Document_type_model();
    }

    public function index($id){
        
        $this->data['canditate'] = $id;
        $this->data['list_items'] = $this->upload_model->get(['candidate_id'=> $id])->getResultArray();
        $doc_type = $this->document_type_model->get()->getResultArray();
        $this->data['doc_type'] = array_column($doc_type,'title','id');
        $this->data['page_title'] = 'Upload Document';
        $this->data['page_name'] = 'Upload_document/index';
        return view('App/index', $this->data);
    }

    public function ajax_add($id){
        // print_r($id); exit();
        $this->data['canditate'] = $id;
        $this->data['document_type'] = $this->document_type_model->get()->getResultArray();
        echo view('App/Upload_document/ajax_add', $this->data);
    }

    public function add(){
        $id = $this->request->getPost('candidate_id');
        
        if ($this->request->getMethod() === 'post')
        {
            $data = [
                'candidate_id' => $id,
                'title' => $this->request->getPost('title'),
                'document_type' => $this->request->getPost('document_type'),
                'created_by' => get_user_id(),
                'created_at' => date('Y-m-d H:i:s'),
            
            ];
            $document = $this->upload_file('canditates/documents','document');
            
            if($document && valid_file($document['file'])){
				$data['document'] = $document['file'];
			}
            
            $upload_id = $this->upload_model->add($data);
            if ($upload_id){
                session()->setFlashdata('message_success', "Document Added Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/upload_document/index/'.$id));
    }

    public function ajax_edit($id){
        $this->data['document_type'] = $this->document_type_model->get()->getResultArray();

        $this->data['edit_data'] = $this->upload_model->get(['id' => $id])->getRowArray();
        echo view('App/Upload_document/ajax_edit', $this->data);
    }

    public function edit($id){
        
        $canditate =  $this->request->getPost('candidate_id');
        if ($this->request->getMethod() === 'post'){
            $data = [
                'title' => $this->request->getPost('title'),
                'document_type' => $this->request->getPost('document_type'),
                'updated_by' => get_user_id(),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            
            $document = $this->upload_file('canditates/documents','document');
            if($document && valid_file($document['file'])){
				$data['document'] = $document['file'];
			}
            
            $response = $this->upload_model->edit($data, ['id' => $id]);
            if ($response){
                session()->setFlashdata('message_success', "Document Updated Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/upload_document/index/'.$canditate));
    }

    public function ajax_view($id){
        $this->data['view_data'] = $this->upload_model->get(['id' => $id])->getRowArray();
        echo view('App/Upload_document/ajax_view', $this->data);
    }

    public function delete($id){
        $view_data = $this->upload_model->get(['id' => $id])->getRowArray();

        if ($id > 0)
        {
            if ($this->upload_model->remove(['id' => $id])){
                session()->setFlashdata('message_success', "Document Deleted Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }else{
            session()->setFlashdata('message_danger', "Something went wrong! Try Again");
        }
        return redirect()->to(base_url('app/upload_document/index/'.$view_data['candidate_id']));
    }
}
