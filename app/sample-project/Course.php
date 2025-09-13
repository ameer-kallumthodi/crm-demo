<?php
namespace App\Controllers\App;
use App\Models\Course_model;
class Course extends AppBaseController
{
    private $course_model;
    public function __construct()
    {
        parent::__construct();
        $this->course_model = new Course_model();

    }

    public function index(){
        $this->data['list_items'] = $this->course_model->get()->getResultArray();
        $this->data['page_title'] = 'Course';
        $this->data['page_name'] = 'Course/index';
        return view('App/index', $this->data);
    }

    public function ajax_add(){
        echo view('App/Course/ajax_add', $this->data);
    }

    public function add(){
        if ($this->request->getMethod() === 'post'){
            $data = [
                'title' => $this->request->getPost('title'),
                'amount' => $this->request->getPost('amount'),
                'created_by' => get_user_id(),
                'created_at' => date('Y-m-d H:i:s'),
            
            ];
            $course_id = $this->course_model->add($data);
            if ($course_id){
                session()->setFlashdata('message_success', "Course Added Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/course/index'));
    }

    public function ajax_edit($id){
        $this->data['edit_data'] = $this->course_model->get(['id' => $id])->getRowArray();
        echo view('App/Course/ajax_edit', $this->data);
    }

    public function edit($id){
        if ($this->request->getMethod() === 'post'){
            $data = [
                'title' => $this->request->getPost('title'),
                'amount' => $this->request->getPost('amount'),
                'updated_by' => get_user_id(),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $response = $this->course_model->edit($data, ['id' => $id]);
            if ($response){
                session()->setFlashdata('message_success', "Course Updated Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/course/index'));
    }

    public function ajax_view($id){
        $this->data['view_data'] = $this->course_model->get(['id' => $id])->getRowArray();
        echo view('App/Course/ajax_view', $this->data);
    }

    public function delete($id){
        if ($id > 0){
            if ($this->course_model->remove(['id' => $id])){
                session()->setFlashdata('message_success', "Course Deleted Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }else{
            session()->setFlashdata('message_danger', "Something went wrong! Try Again");
        }
        return redirect()->to(base_url('app/course/index'));
    }
}
