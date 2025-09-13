<?php
namespace App\Controllers\App;
use App\Models\Country_model;
class Country extends AppBaseController
{
    private $country_model;
    public function __construct()
    {
        parent::__construct();
        $this->country_model = new Country_model();

    }

    public function index(){
        $this->data['list_items'] = $this->country_model->get()->getResultArray();
        $this->data['page_title'] = 'Country';
        $this->data['page_name'] = 'Country/index';
        return view('App/index', $this->data);
    }

    public function ajax_add(){
        echo view('App/Country/ajax_add', $this->data);
    }

    public function add(){
        if ($this->request->getMethod() === 'post'){
            $data = [
                'title' => $this->request->getPost('title'),
                'short_description' => $this->request->getPost('short_description'),
                'created_by' => get_user_id(),
                'created_at' => date('Y-m-d H:i:s'),
            
            ];
            $country_id = $this->country_model->add($data);
            if ($country_id){
                session()->setFlashdata('message_success', "Country Added Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/country/index'));
    }

    public function ajax_edit($id){
        $this->data['edit_data'] = $this->country_model->get(['id' => $id])->getRowArray();
        echo view('App/Country/ajax_edit', $this->data);
    }

    public function edit($id){
        if ($this->request->getMethod() === 'post'){
            $data = [
                'title' => $this->request->getPost('title'),
                'short_description' => $this->request->getPost('short_description'),
                'updated_by' => get_user_id(),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $response = $this->country_model->edit($data, ['id' => $id]);
            if ($response){
                session()->setFlashdata('message_success', "Country Updated Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/country/index'));
    }

    public function ajax_view($id){
        $this->data['view_data'] = $this->country_model->get(['id' => $id])->getRowArray();
        echo view('App/Country/ajax_view', $this->data);
    }

    public function delete($id){
        if ($id > 0){
            if ($this->country_model->remove(['id' => $id])){
                session()->setFlashdata('message_success', "Country Deleted Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }else{
            session()->setFlashdata('message_danger', "Something went wrong! Try Again");
        }
        return redirect()->to(base_url('app/country/index'));
    }
}
