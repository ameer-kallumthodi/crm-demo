<?php
namespace App\Controllers\App;
use App\Models\Subjects_model;
use App\Models\Course_model;

class Subjects extends AppBaseController
{
    private $subjects_model;
    private $course_model;
    public function __construct()
    {
        parent::__construct();
        $this->subjects_model = new Subjects_model();
        $this->course_model = new Course_model();
    }

    public function index(){
        $this->data['list_items'] = $this->subjects_model->get()->getResultArray();
        $this->data['course_list'] = $this->course_model->get()->getResultArray();
        
        $this->data['course_name'] = array_column($this->data['course_list'], 'title', 'id');
        $this->data['page_title'] = 'Subjects';
        $this->data['page_name'] = 'Subjects/index';
        return view('App/index', $this->data);
    }

    public function ajax_add(){
        $this->data['course_list'] = $this->course_model->get()->getResultArray();
        echo view('App/Subjects/ajax_add', $this->data);
    }

    public function add(){
        if ($this->request->getMethod() === 'post'){
            $data = [
                'title' => $this->request->getPost('title'),
                'course_id' => $this->request->getPost('course_id'),
                // 'session_amount' => $this->request->getPost('session_amount'),
                'created_by' => get_user_id(),
                'created_at' => date('Y-m-d H:i:s'),
            
            ];
            $course_id = $this->subjects_model->add($data);
            if ($course_id){
                session()->setFlashdata('message_success', "Subject Added Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/subjects/index'));
    }

    public function ajax_edit($id){
        $this->data['course_list'] = $this->course_model->get()->getResultArray();
        $this->data['edit_data'] = $this->subjects_model->get(['id' => $id])->getRowArray();
        echo view('App/Subjects/ajax_edit', $this->data);
    }

    public function edit($id){
        if ($this->request->getMethod() === 'post'){
            $data = [
                'title' => $this->request->getPost('title'),
                'course_id' => $this->request->getPost('course_id'),
                // 'session_amount' => $this->request->getPost('session_amount'),
                'updated_by' => get_user_id(),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $response = $this->subjects_model->edit($data, ['id' => $id]);
            if ($response){
                session()->setFlashdata('message_success', "Subject Updated Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/subjects/index'));
    }

    public function ajax_view($id){
        $this->data['view_data'] = $this->subjects_model->get(['id' => $id])->getRowArray();
        $course_data = $this->course_model->get()->getResultArray();
        $this->data['course_names'] = array_columns($course_data, 'title', 'id');
        echo view('App/Subjects/ajax_view', $this->data);
    }

    public function delete($id){
        if ($id > 0){
            if ($this->subjects_model->remove(['id' => $id])){
                session()->setFlashdata('message_success', "Subject Deleted Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }else{
            session()->setFlashdata('message_danger', "Something went wrong! Try Again");
        }
        return redirect()->to(base_url('app/subjects/index'));
    }
    
    public function get_subject_by_course(){
        $course_id = $this->request->getPost('course_id');
        $subjects = $this->subjects_model->get(['course_id' => $course_id])->getResultArray();
        
        $options = '<option value="">Choose Subject</option>';
        log_message('error', print_r($this->request->getPost('selected_subject_id'), true));
        // If there are subjects available, append them to the options
        if (!empty($subjects)) {
            foreach ($subjects as $subject) {
                // Check if the subject ID matches the one sent via AJAX
                $selected = ($subject['id'] == $this->request->getPost('selected_subject_id')) ? 'selected' : '';
                $options .= '<option value="' . $subject['id'] . '" ' . $selected . '>' . $subject['title'] . '</option>';
            }
        }
    
        // Pass the options to the view
        echo $options;
    }


}
