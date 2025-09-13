<?php namespace App\Models;

use CodeIgniter\Model;
use App\Models\Users_model;
use App\Models\Lead_activity_model;

class Leads_model extends Base_model
{
    protected $table         = 'leads';      // Database table name
    protected $primaryKey    = 'id';         // Primary key of the table
    protected $returnType    = 'App\Entities\Leads';  // Entity class name
    protected $useTimestamps = true;         // Auto handle timestamps
    protected $allowedFields = ['title','gender','age','phone','whatsapp','email','qualification','country_id','interest_status','lead_status_id','lead_source_id','address','telecaller_id','place','created_by','updated_by'];  // Fields that can be manipulated

    // Optional: Define validation rules
    protected $validationRules    = [
        'title' => 'required|min_length[2]',
        'email'    => 'required|valid_email|is_unique[users.email]',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'This email address is already registered. Please use a different email.'
        ],
        'phone' => [
            'is_unique' => 'This phone number is already registered. Please use a different phone number.'
        ]
    ];
    
    
    public function status_with_count(){ 
        $this->lead_status_model = new Lead_status_model();
        $leads = $this->get()->getResultArray();
        $lead_status = $this->lead_status_model->get()->getResultArray();
        
        $leadStatuses = [];
        foreach ($lead_status as $status) {
            $leadStatuses[$status['id']] = [
                'id' => $status['id'],
                'title' => $status['title'],
                'count' => 0 
            ];
        }
        
        foreach($leads as $lead) {
            $statusId = $lead['lead_status_id'];
            if (array_key_exists($statusId, $leadStatuses)) {
                $leadStatuses[$statusId]['count']++;
            }
        }
        
        return array_values($leadStatuses);
    }
    
    public function telecaller_status_with_count($telecaller_id){ 
        $this->lead_status_model = new Lead_status_model();
        $leads = $this->get(['telecaller_id' => $telecaller_id])->getResultArray();
        $lead_status = $this->lead_status_model->get()->getResultArray();
        
        $leadStatuses = [];
        foreach ($lead_status as $status) {
            $leadStatuses[$status['id']] = [
                'id' => $status['id'],
                'title' => $status['title'],
                'count' => 0 
            ];
        }
        
        foreach($leads as $lead) {
            $statusId = $lead['lead_status_id'];
            if (array_key_exists($statusId, $leadStatuses)) {
                $leadStatuses[$statusId]['count']++;
            }
        }
        
        return array_values($leadStatuses);
    }
    
    
    public function lead_makers(){ 
        $this->users_model = new Users_model();
        $leads = $this->get()->getResultArray();
        $telecallers = $this->users_model->get(['role_id' => 6])->getResultArray();
        
        $leadsByStaff = [];
        foreach ($telecallers as $telecaller) {
            $leadsByStaff[$telecaller['id']] = [
                'id' => $telecaller['id'],
                'profile_picture' => $telecaller['profile_picture'] ?? '',
                'name' => $telecaller['name'],
                'phone' => $telecaller['phone'],
                'count' => 0 
            ];
        }
        
        foreach($leads as $lead) {
            $telecallerId = $lead['telecaller_id'];
            if (array_key_exists($telecallerId, $leadsByStaff)) {
                $leadsByStaff[$telecallerId]['count']++;
            }
        }

        $data = array_values($leadsByStaff);
        
        // Filter out entries with count <= 0
        $dataFiltered = array_filter($data, function($value) {
            return $value['count'] > 0;
        });
        
        // Sort the filtered array based on the 'count' field in descending order
        usort($dataFiltered, function($a, $b) {
            return $b['count'] <=> $a['count'];
        });
        
        // Select the first 5 elements
        $topFive = array_slice($dataFiltered, 0, 5);
        
        return $topFive;
    }
    
    public function emigration_countries(){ 
        $this->country_model = new Country_model();
        $leads = $this->get()->getResultArray();
        $countries = $this->country_model->get()->getResultArray();
        
        $leadCountries = [];
        foreach($countries as $country) {
            $leadCountries[$country['id']] = [
                'id' => $country['id'],
                'title' => $country['title'],
                'count' => 0 
            ];
        }
        
        foreach($leads as $lead) {
            $countryId = $lead['country_id'];
            if (array_key_exists($countryId, $leadCountries)) {
                $leadCountries[$countryId]['count']++;
            }
        }
        
        $data = array_values($leadCountries);
        
        // Filter out entries with count <= 0
        $dataFiltered = array_filter($data, function($value) {
            return $value['count'] > 0;
        });
        
        // Sort the filtered array based on the 'count' field in descending order
        usort($dataFiltered, function($a, $b) {
            return $b['count'] <=> $a['count'];
        });
        
        // Select the first 5 elements
        $topFive = array_slice($dataFiltered, 0, 5);
         
        return $topFive;
    }
   
    public function get_lead_by_phone($phone){
        $query = $this->db->table('leads')
            ->where('CONCAT(code, phone)', $phone)
            ->get();
        return $query->getRowArray();
    }
    
    // public function get_leads($where){
    //     $query = $this->db->table('leads')
    //                   ->select('leads.*, lead_status.title as status_title')
    //                   ->join('lead_status','lead_status.id = lead.lead_status_id')
    //                   ->where()
    //                   ->get();
    //     return $query;
    // }
    
    public function get_leads($where = []) {
        $builder = $this->db->table('leads');
        $builder->select('leads.*, lead_status.title as status_title, course.title as course_name');
        $builder->join('lead_status', 'lead_status.id = leads.lead_status_id');
        $builder->join('course', 'course.id = leads.course_id', 'left');
        $builder->where('leads.deleted_at IS NULL');
        $builder->where('course.deleted_at IS NULL');
        $builder->where('lead_status.deleted_at IS NULL');
    
        if (!empty($where)) {
            $builder->where($where);
        }
    
        $query = $builder->get();
        return $query;
    }
    
    public function re_assign_leads($tele_caller_id, $lead_source_id, $selected_lead_id, $from_tele_caller_id) {
        $query = $this->db->table('leads')
            ->set('telecaller_id', $tele_caller_id)
            ->set('lead_status_id', 23)
            ->where('lead_source_id', $lead_source_id)
            ->where('id', $selected_lead_id)
            ->update();
    
        // Return false if the query fails
        if (!$query) {
            return false;
        }
        $this->update_lead_history($selected_lead_id, $tele_caller_id, $from_tele_caller_id);
    
        return true;
    }
    
    public function update_lead_history($selected_lead_id, $tele_caller_id, $from_tele_caller_id){
        $this->users_model = new Users_model();
        $this->lead_activity_model = new Lead_activity_model();
        $telecaller_name = $this->users_model->get(['id' => $tele_caller_id], ['name'])->getRow()->name;
        $from_telecaller_name = $this->users_model->get(['id' => $from_tele_caller_id], ['name'])->getRow()->name;
        $lead_activity_data = [
            'lead_status_id' => 23,
            'lead_id' => $selected_lead_id, 
            'remarks' => 'Lead has been reassigned from telecaller ' . $from_telecaller_name . ' to telecaller ' . $telecaller_name . '.',
            'created_by' => get_user_id(),
            'updated_by' => get_user_id(),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $lead_activity_id = $this->lead_activity_model->add($lead_activity_data);
        return true;
    }
    
    public function bulk_delete_leads($selected_lead_id) {
        // Attempt to delete the lead by its ID
        if (!$this->remove(['id' => $selected_lead_id])) {
            return false; // Return false if deletion fails
        }
    
        return true; // Successful deletion
    }
    
    

}
