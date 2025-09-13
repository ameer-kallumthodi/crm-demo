<?php
namespace App\Controllers\App;
use App\Models\Invoice_model;
use App\Models\Course_model;
use App\Models\Payment_model;

use App\Models\Leads_model;
use App\Models\Fee_type_model;


class Payment extends AppBaseController
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

    public function index($id){
        
        $this->data['edit_data'] = $edit_data= $this->invoice_model->get(['id' => $id])->getRowArray();
        $this->data['invoice_id'] = $id;
        $this->data['user_id'] = $edit_data['user_id'];
        
        $this->data['list_items']   = $this->payment_model->get(['invoice_id' => $id])->getResultArray();
   
        echo view('App/Payment/index', $this->data);

    }

    public function ajax_add($id){
        
        $this->data['edit_data'] = $edit_data= $this->invoice_model->get(['id' => $id])->getRowArray();
        $this->data['invoice_id'] = $id;
        
        $paid = 0;
        $payments = $this->payment_model->get(['invoice_id' => $id])->getResultArray();
                
        if(!empty($payments))
        {
            foreach($payments as $pay)
            { 
                $paid += $pay['paid_amount'];
            }
        }
        
        $this->data['paid'] = $paid;
        
        
        
        $this->data['user_id'] = $edit_data['user_id'];
        echo view('App/Payment/ajax_add', $this->data);
    }

        public function add(){

        if ($this->request->getMethod() === 'post')
        {
            $inv_id = $this->request->getPost('invoice_id');
            $amount = $this->request->getPost('paid_amount');
            $p_date = $this->request->getPost('payment_date');
            $p_type = $this->request->getPost('payment_type');
            $ref = $this->request->getPost('reference_no');
            
            
            $data = [                
                'invoice_id' => $this->request->getPost('invoice_id'),
                'user_id' => $this->request->getPost('user_id'),
                'paid_amount' => $amount,
                'payment_date'=> $p_date,
                'payment_type' => $p_type,
                'reference_no' => $ref,
                'remark' => $this->request->getPost('remark'),
                'created_by' => get_user_id(),
                'created_on' => date('Y-m-d H:i:s'),
            
            ];
            
            

            $pay_id = $this->payment_model->add($data);
         
            
            if ($pay_id)
            {
                  $joins = [
                        ['leads', 'leads.id = invoice.user_id'],
                        ['course', 'course.id = invoice.course_id'],
                        ['fee_type', 'fee_type.id = invoice.fee_type']
                    ];
                    
                  $select = [
                        'invoice.*',
                        'leads.title as canditate',
                        'leads.code','leads.phone','leads.email','leads.address','course.title as course','fee_type.title as fee_type'
                    ];
                
        
                $details = $this->invoice_model->get_join($joins, ['invoice.id' => $inv_id], $select,NULL,NULL,NULL)->getRowArray();
                $payments = $this->payment_model->get(['invoice_id' => $inv_id])->getResultArray();
                
                $paid = 0;
                foreach($payments as $pay)
                    { 
                        $paid += $pay['paid_amount'];
                    }
                
                 $emailTo = $details['email'];
                    $canditate = $details['canditate'];
                    $course = $details['course'];
                    $fee_type = $details['fee_type'];
                    $inv_date = $details['inv_date'];
                    $balance = $details['payable_amount'] - $paid;
               
                if($emailTo > 0){
                
                    $subject = "Payment done Successfully";
                    
                    $message ='<div class="container">
                                    <h1>Dear '.$canditate.'!</h1>
                                    <p>I hope this email finds you well. We wanted to inform you that we have received your payment for the following invoice:</p>
                                      <table style="width: 100%; border-collapse: collapse;">
                                        <tr>
                                          <th style="border: 1px solid #dddddd; text-align: left; padding: 8px;">Invoice Number</th>
                                          <td style="border: 1px solid #dddddd; text-align: left; padding: 8px;">#'.$inv_id.'</td>
                                        </tr>
                                        <tr>
                                          <th style="border: 1px solid #dddddd; text-align: left; padding: 8px;">Date of Invoice</th>
                                          <td style="border: 1px solid #dddddd; text-align: left; padding: 8px;">'.date("d M, Y", strtotime($inv_date)).'</td>
                                        </tr>
                                        <tr>
                                          <th style="border: 1px solid #dddddd; text-align: left; padding: 8px;">Course</th>
                                          <td style="border: 1px solid #dddddd; text-align: left; padding: 8px;">'.$course.'</td>
                                        </tr>
                                         <tr>
                                          <th style="border: 1px solid #dddddd; text-align: left; padding: 8px;">Feetype</th>
                                          <td style="border: 1px solid #dddddd; text-align: left; padding: 8px;">'.$fee_type.'</td>
                                        </tr>
                                          <tr>
                                          <th style="border: 1px solid #dddddd; text-align: left; padding: 8px;">Amount Due</th>
                                          <td style="border: 1px solid #dddddd; text-align: left; padding: 8px;">'.number_format($balance,2).'</td>
                                        </tr>
                                      </table>
                                      <p>Your payment has been successfully processed. Here are the payment details:</p>
                                          <table style="width: 100%; border-collapse: collapse;">
                                            <tr>
                                              <th style="border: 1px solid #dddddd; text-align: left; padding: 8px;">Payment Date</th>
                                              <td style="border: 1px solid #dddddd; text-align: left; padding: 8px;">'.date("d M, Y", strtotime($p_date)).'</td>
                                            </tr>
                                            
                                            <tr>
                                              <th style="border: 1px solid #dddddd; text-align: left; padding: 8px;">Payment Amount</th>
                                              <td style="border: 1px solid #dddddd; text-align: left; padding: 8px;">'.number_format($amount,2).'</td>
                                            </tr>
                                             <tr>
                                              <th style="border: 1px solid #dddddd; text-align: left; padding: 8px;">Payment Type</th>
                                              <td style="border: 1px solid #dddddd; text-align: left; padding: 8px;">'.$p_type.'</td>
                                            </tr>
                                             <tr>
                                              <th style="border: 1px solid #dddddd; text-align: left; padding: 8px;">Reference No</th>
                                              <td style="border: 1px solid #dddddd; text-align: left; padding: 8px;">'.$ref.'</td>
                                            </tr>
                                            <!-- Add more payment details if necessary -->
                                          </table>
                                        <div class="note">
                                        <p><strong>Note:</strong> If you have any queries, please do not hesitate to contact our team.</p>
                                    </div>
                                    <br>
                                    <p>Best regards,</p>
                                    <p>Aimbridge Education</p>
                                </div>';
                
                    $this->send_mail($emailTo,$subject,$message);
                }

        
        
        
                session()->setFlashdata('message_success', "Invoice Added Successfully!");
            }else{
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




    public function delete($id){
        if ($id > 0){
            if ($this->payment_model->remove(['id' => $id])){
                session()->setFlashdata('message_success', "Payment Deleted Successfully!");
            }else{
                session()->setFlashdata('message_danger', "Something went wrong! Try Again");
            }
        }else{
            session()->setFlashdata('message_danger', "Something went wrong! Try Again");
        }
        return redirect()->to(base_url('app/invoice/index'));
    }
}
