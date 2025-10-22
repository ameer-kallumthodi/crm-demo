<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class MetaLead extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'meta_leads';

    protected $fillable = [
        'lead_id',
        'created_time',
        'email',
        'full_name',
        'phone_number',
        'other_details',
        'form_no',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'created_time' => 'datetime',
        'other_details' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the last inserted lead ID
     */
    public static function getLastInsertedLeadId()
    {
        return self::orderBy('id', 'desc')->first();
    }

    /**
     * Check if lead exists by lead_id and form_no
     */
    public static function leadExists($leadId, $formNo = null)
    {
        $query = self::where('lead_id', $leadId);
        
        if ($formNo !== null) {
            $query->where('form_no', $formNo);
        }
        
        return $query->exists();
    }

    /**
     * Get new leads after a specific ID
     */
    public static function getNewLeads($lastLeadId)
    {
        return self::where('id', '>', $lastLeadId)->get();
    }

    /**
     * Insert multiple leads with duplicate checking
     */
    public static function insertLeads($leads)
    {
        if (!is_array($leads) || empty($leads)) {
            \Log::error('MetaLead: insertLeads() received invalid data');
            return false;
        }

        $newLeads = [];

        foreach ($leads as $lead) {
            if (!isset($lead['lead_id']) || !is_array($lead)) {
                \Log::error('MetaLead: Invalid lead data - ' . print_r($lead, true));
                continue;
            }

            if (!self::leadExists($lead['lead_id'], $lead['form_no'] ?? null)) {
                // Convert other_details array to JSON string
                if (isset($lead['other_details']) && is_array($lead['other_details'])) {
                    $lead['other_details'] = json_encode($lead['other_details']);
                }
                
                $newLeads[] = $lead;
            }
        }

        if (!empty($newLeads)) {
            return self::insert($newLeads);
        }

        return false;
    }

    /**
     * Get leads by form number
     */
    public function scopeByForm($query, $formNo)
    {
        return $query->where('form_no', $formNo);
    }

    /**
     * Get leads with phone numbers
     */
    public function scopeWithPhone($query)
    {
        return $query->whereNotNull('phone_number')->where('phone_number', '!=', '');
    }

    /**
     * Get leads with email
     */
    public function scopeWithEmail($query)
    {
        return $query->whereNotNull('email')->where('email', '!=', '');
    }

    /**
     * Get leads created today
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Get leads created in date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
    }

    /**
     * Accessor for formatted phone number
     */
    public function getFormattedPhoneAttribute()
    {
        if (empty($this->phone_number)) {
            return 'N/A';
        }
        
        return $this->phone_number;
    }

    /**
     * Accessor for formatted other details
     */
    public function getFormattedOtherDetailsAttribute()
    {
        if (empty($this->other_details)) {
            return [];
        }
        
        return is_array($this->other_details) ? $this->other_details : json_decode((string)$this->other_details, true);
    }

    /**
     * Get specific other detail by key
     */
    public function getOtherDetail($key, $default = null)
    {
        $details = $this->formatted_other_details;
        return $details[$key] ?? $default;
    }

    /**
     * Check if lead has specific other detail
     */
    public function hasOtherDetail($key)
    {
        $details = $this->formatted_other_details;
        return isset($details[$key]) && !empty($details[$key]);
    }

    /**
     * Get city from other details
     */
    public function getCityAttribute()
    {
        return $this->getOtherDetail('city', '');
    }

    /**
     * Get job title from other details
     */
    public function getJobTitleAttribute()
    {
        return $this->getOtherDetail('job_title', '');
    }

    /**
     * Get curriculum type from other details
     */
    public function getCurriculumTypeAttribute()
    {
        return $this->getOtherDetail('curriculum_type', '');
    }

    /**
     * Get child name from other details
     */
    public function getChildNameAttribute()
    {
        return $this->getOtherDetail('please_enter_your_child\'s_name', '');
    }

    /**
     * Get child grade from other details
     */
    public function getChildGradeAttribute()
    {
        return $this->getOtherDetail('what_is_your_child\'s_grade/class?', '');
    }

    /**
     * Check if phone number is verified
     */
    public function isPhoneVerified()
    {
        return $this->getOtherDetail('phone_number_verified') === 'true';
    }

    /**
     * Generate remarks from other details
     */
    public function generateRemarks()
    {
        $remarksArray = [];
        
        if ($this->city) {
            $remarksArray[] = "<li><strong>City:</strong> " . htmlspecialchars($this->city) . "</li>";
        }
        
        if ($this->job_title) {
            $remarksArray[] = "<li><strong>Job Title:</strong> " . htmlspecialchars($this->job_title) . "</li>";
        }
        
        if ($this->curriculum_type) {
            $remarksArray[] = "<li><strong>Curriculum:</strong> " . htmlspecialchars($this->curriculum_type) . "</li>";
        }
        
        if ($this->child_name) {
            $remarksArray[] = "<li><strong>Child Name:</strong> " . htmlspecialchars($this->child_name) . "</li>";
        }
        
        if ($this->child_grade) {
            $remarksArray[] = "<li><strong>Child Grade:</strong> " . htmlspecialchars($this->child_grade) . "</li>";
        }
        
        if (!empty($remarksArray)) {
            return "<ul>" . implode('', $remarksArray) . "</ul>";
        }
        
        return '';
    }
}
