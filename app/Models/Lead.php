<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\AuthHelper;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'gender',
        'age',
        'phone',
        'code',
        'whatsapp',
        'email',
        'qualification',
        'country_id',
        'interest_status',
        'lead_status_id',
        'lead_source_id',
        'address',
        'telecaller_id',
        'team_id',
        'place',
        'created_by',
        'updated_by',
        'course_id',
        'by_meta',
        'meta_lead_id',
        'followup_date',
        'remarks',
        'is_converted'
    ];

    protected $casts = [
        'by_meta' => 'boolean',
        'is_converted' => 'boolean',
        'followup_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function leadStatus()
    {
        return $this->belongsTo(LeadStatus::class, 'lead_status_id');
    }

    public function leadSource()
    {
        return $this->belongsTo(LeadSource::class, 'lead_source_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function telecaller()
    {
        return $this->belongsTo(User::class, 'telecaller_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function leadActivities()
    {
        return $this->hasMany(LeadActivity::class, 'lead_id');
    }

    // Scopes
    public function scopeWithStatusCount($query)
    {
        return $query->selectRaw('lead_status_id, COUNT(*) as count')
                    ->groupBy('lead_status_id');
    }

    // Static methods
    public static function statusWithCount()
    {
        return self::selectRaw('lead_status_id, COUNT(*) as count')
                  ->groupBy('lead_status_id')
                  ->get();
    }

    public function scopeByTelecaller($query, $telecallerId)
    {
        return $query->where('telecaller_id', $telecallerId);
    }

    public function scopeByDateRange($query, $fromDate, $toDate)
    {
        return $query->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
    }

    public function scopeNotConverted($query)
    {
        return $query->where('is_converted', false);
    }

    public function scopeNotDropped($query)
    {
        return $query->where('lead_status_id', '!=', 7);
    }

    // Accessors
    public function getFullPhoneAttribute()
    {
        return $this->code . $this->phone;
    }

    public function getWhatsappUrlAttribute()
    {
        if ($this->phone) {
            return "https://api.whatsapp.com/send/?phone={$this->code}{$this->phone}&text=Hi {$this->title}&type=phone_number&app_absent=0";
        }
        return 'javascript:void(0);';
    }

    // Methods
    public function updateLeadStatus($statusId, $remarks = null, $followupDate = null)
    {
        $this->update([
            'lead_status_id' => $statusId,
            'followup_date' => $followupDate,
            'remarks' => $remarks,
            'is_converted' => $statusId == 4 ? true : false,
            'updated_by' => AuthHelper::getCurrentUserId()
        ]);

        // Create lead activity
        LeadActivity::create([
            'lead_id' => $this->id,
            'lead_status_id' => $statusId,
            'remarks' => $remarks,
            'followup_date' => $followupDate,
            'created_by' => AuthHelper::getCurrentUserId(),
            'updated_by' => AuthHelper::getCurrentUserId()
        ]);
    }

    public function reassignToTelecaller($telecallerId, $fromTelecallerId = null)
    {
        $this->update([
            'telecaller_id' => $telecallerId,
            'updated_by' => AuthHelper::getCurrentUserId()
        ]);

        // Get user names safely
        $fromTelecallerName = 'Unknown';
        if ($fromTelecallerId) {
            $fromUser = User::find($fromTelecallerId);
            $fromTelecallerName = $fromUser ? $fromUser->name : 'Unknown';
        }
        
        $toUser = User::find($telecallerId);
        $toTelecallerName = $toUser ? $toUser->name : 'Unknown';

        // Create activity log
        LeadActivity::create([
            'lead_id' => $this->id,
            'lead_status_id' => 23, // Reassigned status
            'remarks' => 'Lead has been reassigned from telecaller ' . $fromTelecallerName . 
                        ' to telecaller ' . $toTelecallerName . '.',
            'created_by' => AuthHelper::getCurrentUserId(),
            'updated_by' => AuthHelper::getCurrentUserId()
        ]);
    }
}