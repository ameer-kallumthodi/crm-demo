<?php
namespace App\Controllers\App;
use App\Models\Invoice_model;
use App\Models\Course_model;
use App\Models\Payment_model;

use App\Models\Leads_model;
use App\Models\Fee_type_model;



class Invoice extends AppBaseController
{
    private $invoice_model;
    private $course_model;
    private $leads_model;
    private $payment_model;
    private $fee_type_model;



    public function __construct()
    {
        parent::__construct();
        $this->invoice_model = new Invoice_model();
        $this->course_model = new Course_model();
        $this->leads_model = new Leads_model();
        $this->fee_type_model = new Fee_type_model();
        $this->payment_model = new Payment_model();


    }

    public function index(){
       
        $canditates                         = $this->leads_model->get(['is_converted' => 1])->getResultArray();
        $this->data['student_list']      = array_column($canditates,'title','id');
        
        $course                              = $this->course_model->get()->getResultArray();
        $this->data['course_name']           = array_column($course, 'title', 'id');
        
        $fee_type                              = $this->fee_type_model->get()->getResultArray();
        $this->data['fee_type']           = array_column($fee_type, 'title', 'id');
        
        $this->data['course_list']  = $course;
        $this->data['type_list']    = $fee_type;
        $this->data['canditates']   = $canditates;
        
        $invoices = $this->invoice_model->get()->getResultArray();
        
        if(!empty($invoices))
        {
            
            foreach($invoices as $key => $val)
            {
                $paid = 0;
                $payments = $this->payment_model->get(['invoice_id' => $val['id']])->getResultArray();
                
                if(!empty($payments))
                {
                    foreach($payments as $pay)
                    { 
                        $paid += $pay['paid_amount'];
                    }
                }
                $invoices[$key]['payment_count'] = sizeof($payments);
                $invoices[$key]['total_paid'] = $paid;
            }
        }
        
        $this->data['list_items']   = $invoices;
        
        
        
        
     

        $this->data['page_title'] = 'Invoice';
        $this->data['page_name'] = 'Invoice/index';
        return view('App/index', $this->data);
    }
    
    public function ajax_add(){
        
        $this->data['course_list'] = $this->course_model->get()->getResultArray();
        $this->data['type_list'] = $this->fee_type_model->get()->getResultArray();
        $this->data['canditates']   = $this->leads_model->get(['is_converted' => 1])->getResultArray();

        
        echo view('App/Invoice/ajax_add', $this->data);
    }
    
    
    public function getStudentsbycourse()
    {
        $logger = service('logger');
        $course = $_POST['course'];
        $users =  $this->leads_model->get(['is_converted' => 1,'course_id'=>$course])->getResultArray();
    //  $logger->error('Database Error: ' . db_connect()->getLastQuery());
        $course = $this->course_model->get(['id' => $course])->getRowArray();
        if (!empty($users))
        {
            $data = array('status' => 1,'users' => $users,'amount'=>$course['amount']);
		}
		else
		{
		     $data = array('status' => 0);
		}
		
		echo json_encode($data);
    }
    
   


    public function add(){

        if ($this->request->getMethod() === 'post')
        {
            $total = $this->request->getPost('total_amount');
            $payable = $this->request->getPost('payable_amount');
            
            $discount = $total - $payable;
            $user_id = $this->request->getPost('user_id');
            
            $inv_date = $this->request->getPost('inv_date');
            $due_date = $this->request->getPost('due_date');
            
            $data = [
                'course_id' => $this->request->getPost('course_id'),
                'user_id' => $user_id,
                'fee_type' => $this->request->getPost('fee_type'),
                'total_amount' => $total,
                'payable_amount'=> $payable,
                'discount'=> $discount,
                'inv_date' => $inv_date,
                'due_date' => $due_date,
                'remarks' => $this->request->getPost('remarks'),
                'created_by' => get_user_id(),
                'created_on' => date('Y-m-d H:i:s'),
            
            ];
            
            
       
            
            $inv_id = $this->invoice_model->add($data);
         
            
            if ($inv_id)
            {
                  $joins = [
                        ['leads', 'leads.id = invoice.user_id'],
                        ['course', 'course.id = invoice.course_id'],
                        ['fee_type', 'fee_type.id = invoice.fee_type'],
                        // Add more joins if needed
                    ];
                    
                // Define the select fields, including the sum of duration
                $select = ['invoice.id','leads.title','leads.email',
                        'leads.title as canditate','course.title as course','fee_type.title as fee_type','invoice.total_amount','invoice.payable_amount',
                        'inv_date','due_date'
                    ];
             
        
                $details =$this->invoice_model->get_join($joins, ['invoice.id'=>$inv_id], $select,NULL,NULL,NULL)->getRowArray();
                
                // echo "<pre>";
                // print_r($details); exit();

                    $emailTo = $details['email'];
                    $canditate = $details['title'];
                    $course = $details['course'];
                    $fee_type = $details['fee_type'];
               
                if($emailTo > 0){
                
                    $subject = "Invoice Created Successfully";
                    
                    $message ='<div class="container">
                                    <h1>Dear '.$canditate.'!</h1>
                                    <p>I hope this email finds you well. Please find below the invoice details for your reference:</p>
                                      <table style="width: 100%; border-collapse: collapse;">
                                        <tr>
                                          <th style="border: 1px solid #dddddd; text-align: left; padding: 8px;">Invoice Number</th>
                                          <td style="border: 1px solid #dddddd; text-align: left; padding: 8px;">#'.$inv_id.'</td>
                                        </tr>
                                        <tr>
                                          <th style="border: 1px solid #dddddd; text-align: left; padding: 8px;">Date</th>
                                          <td style="border: 1px solid #dddddd; text-align: left; padding: 8px;">'.date("d M, Y", strtotime($inv_date)).'</td>
                                        </tr>
                                        <tr>
                                          <th style="border: 1px solid #dddddd; text-align: left; padding: 8px;">Due Date</th>
                                          <td style="border: 1px solid #dddddd; text-align: left; padding: 8px;">'.date("d M, Y", strtotime($due_date)).'</td>
                                        </tr>
                                        <tr>
                                          <th style="border: 1px solid #dddddd; text-align: left; padding: 8px;">Course</th>
                                          <td style="border: 1px solid #dddddd; text-align: left; padding: 8px;">'.$course.'</td>
                                        </tr>
                                         <tr>
                                          <th style="border: 1px solid #dddddd; text-align: left; padding: 8px;">Feetype</th>
                                          <td style="border: 1px solid #dddddd; text-align: left; padding: 8px;">'.$fee_type.'</td>
                                        </tr>
                                      </table>
                                      <p>Total: '.number_format($total,2).'</p>
                                      <p>Payable Amount: '.number_format($payable,2).'</p>
                                      <p>Discount Amount: '.number_format($discount,2).'</p>
                                    <div class="note">
                                        <p><strong>Note:</strong> If you have any queries please do not hesitate to contact our team.</p>
                                    </div>
                                    <br>
                                    <p>Best regards,</p>
                                    <p>Aimbridge Education</p>
                                </div>';
                
                    $this->send_mail($emailTo,$subject,$message);
                }
                
                session()->setFlashdata('message_success', "Invoice Added Successfully!");
            }
            else
            {
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/invoice/index'));
    }
    
        private function send_mail($emailTo,$subject,$message)
        {
            if (!empty($emailTo)) {
                $data = [
                    'emailTo' => $emailTo,
                    'subject' => $subject,
                    'message' => $message,
                ];
        
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://trogonmedia.com/send_email_api/aimbridge_invoice.php");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                curl_close($ch);
        
                if ($response === false) {
                   
                   session()->setFlashdata('message_success', 'Error occurred while sending the email!');
                    //  echo "Error occurred while sending the email.";
        
                } 
             } else {
                  session()->setFlashdata('message_success', 'Please fill in all the required fields.');
                //  echo "Please fill in all the required fields.";
                 
             }
    }

    
    public function canditate($id){
        
        $this->data['user_id'] = $id;
        $invoices = $this->invoice_model->get(['user_id'=> $id])->getResultArray();
        

        if(!empty($invoices))
        {
            
            foreach($invoices as $key => $val)
            {
                $paid = 0;
                $payments = $this->payment_model->get(['invoice_id' => $val['id']])->getResultArray();
                
                if(!empty($payments))
                {
                    foreach($payments as $pay)
                    { 
                        $paid += $pay['paid_amount'];
                    }
                }
                $invoices[$key]['payment_count'] = sizeof($payments);
                $invoices[$key]['total_paid'] = $paid;
            }
        }
        
        $this->data['list_items']   = $invoices;
        
        $canditates                         = $this->leads_model->get(['is_converted' => 1])->getResultArray();
        $this->data['student_list']      = array_column($canditates,'title','id');
        
        $course                              = $this->course_model->get()->getResultArray();
        $this->data['course_name']           = array_column($course, 'title', 'id');
        
        // $doc_type = $this->document_type_model->get()->getResultArray();
        // $this->data['doc_type'] = array_column($doc_type,'title','id');
        
        $this->data['page_title'] = 'Student Invoice';
        $this->data['page_name'] = 'Invoice/student_index';
        return view('App/index', $this->data);
    }
    
    
    public function view($id){
         
        $canditates                         = $this->leads_model->get(['is_converted' => 1])->getResultArray();
        $this->data['student_list']      = array_column($canditates,'title','id');
        
        $course                              = $this->course_model->get()->getResultArray();
        $this->data['course_name']           = array_column($course, 'title', 'id');
        
          $joins = [
                ['leads', 'leads.id = invoice.user_id'],
            ];
            
          $select = [
                'invoice.*',
                'leads.title as canditate',
                'leads.code','leads.phone','leads.email','leads.address'
            ];
        

        $this->data['view_data'] = $this->invoice_model->get_join($joins, ['invoice.id' => $id], $select,NULL,NULL,NULL)->getRowArray();
         
        $this->data['payments'] = $this->payment_model->get(['invoice_id' => $id])->getResultArray();
         
        $this->data['page_title'] = 'Invoice View';
        $this->data['page_name'] = 'Invoice/view';
        
        // echo "<pre>";
        // print_r($this->data); exit();
        return view('App/index', $this->data);
        
    }
    
    public function ajax_make_payment($id){

        $this->data['edit_data'] = $edit_data= $this->invoice_model->get(['id' => $id])->getRowArray();
        $this->data['invoice_id'] = $id;
        $this->data['user_id'] = $edit_data['user_id'];
        
        
        echo view('App/Invoice/ajax_make_payment', $this->data);
    }
    
    
   

    public function ajax_edit($id){
        $this->data['course_list'] = $this->course_model->get()->getResultArray();
        $this->data['type_list'] = $this->fee_type_model->get()->getResultArray();
        $this->data['edit_data']= $edit_data = $this->invoice_model->get(['invoice.id' => $id])->getRowArray();
        $this->data['canditates']   = $this->leads_model->get(['is_converted' => 1,'course_id'=> $edit_data['course_id']])->getResultArray();

        
  
        
        
        echo view('App/Invoice/ajax_edit', $this->data);
    }

    public function edit($id){
        
        if ($this->request->getMethod() === 'post'){
           
            $total = $this->request->getPost('total_amount');
            $payable = $this->request->getPost('payable_amount');
            
            $discount = $total - $payable;
            
            $data = [
                'course_id' => $this->request->getPost('course_id'),
                'user_id' => $this->request->getPost('user_id'),
                'fee_type' => $this->request->getPost('fee_type'),
                'total_amount' => $total,
                'payable_amount'=> $payable,
                'discount'=> $discount,
                'inv_date' => $this->request->getPost('inv_date'),
                'due_date' => $this->request->getPost('due_date'),
                'remarks' => $this->request->getPost('remarks'),
                'updated_by' => get_user_id(),
                'updated_on' => date('Y-m-d H:i:s'),
            ];
            
            
            $response = $this->invoice_model->edit($data, ['id' => $id]);
            if ($response){
                session()->setFlashdata('message_success', "Invoice Updated Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }
        return redirect()->to(base_url('app/invoice/index'));
    }

  
    public function delete($id){
        $view_data = $this->invoice_model->get(['id' => $id])->getRowArray();

        if ($id > 0)
        {
            if ($this->invoice_model->remove(['id' => $id])){
                session()->setFlashdata('message_success', "Invoice Deleted Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }else{
            session()->setFlashdata('message_danger', "Something went wrong! Try Again");
        }
        return redirect()->to(base_url('app/invoice/index'));
    }

    


   
}
