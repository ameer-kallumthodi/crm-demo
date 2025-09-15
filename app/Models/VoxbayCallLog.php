<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VoxbayCallLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'voxbay_call_logs';

    protected $fillable = [
        'type',
        'call_uuid',
        'calledNumber',
        'callerNumber',
        'AgentNumber',
        'extensionNumber',
        'destinationNumber',
        'callerid',
        'duration',
        'status',
        'date',
        'start_time',
        'end_time',
        'recording_URL',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // Scopes
    public function scopeIncoming($query)
    {
        return $query->where('type', 'incoming');
    }

    public function scopeOutgoing($query)
    {
        return $query->where('type', 'outgoing');
    }

    public function scopeMissedCall($query)
    {
        return $query->where('type', 'missedcall');
    }

    public function scopeByDateRange($query, $fromDate, $toDate)
    {
        return $query->whereBetween('date', [$fromDate, $toDate]);
    }

    public function scopeByAgentNumber($query, $agentNumber)
    {
        return $query->where('AgentNumber', $agentNumber);
    }

    public function scopeByDestinationNumber($query, $destinationNumber)
    {
        return $query->where('destinationNumber', $destinationNumber);
    }

    // Accessors
    public function getFormattedDurationAttribute()
    {
        if (!$this->duration) {
            return 'N/A';
        }
        
        $seconds = (int) $this->duration;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
        
        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        } else {
            return sprintf('%02d:%02d', $minutes, $seconds);
        }
    }

    public function getCallStatusBadgeAttribute()
    {
        $status = strtoupper($this->status ?? 'UNKNOWN');
        
        $badgeClasses = [
            'ANSWER' => 'badge-success',
            'CANCEL' => 'badge-warning',
            'BUSY' => 'badge-danger',
            'cancelled' => 'badge-warning',
            'NO ANSWER' => 'badge-secondary',
        ];

        $class = $badgeClasses[$status] ?? 'badge-secondary';
        
        return "<span class='badge {$class}'>{$status}</span>";
    }

    // Methods
    public function getTelecallerName()
    {
        if (!$this->AgentNumber) {
            return 'Unknown';
        }

        $countryCode = substr($this->AgentNumber, 0, 2);
        $mobileNumber = substr($this->AgentNumber, 2);
        
        $user = User::where('code', $countryCode)
                   ->where('phone', $mobileNumber)
                   ->whereHas('role', function($query) {
                       $query->where('title', 'Telecaller');
                   })
                   ->first();
        
        return $user ? $user->name : 'Unknown';
    }

    public function getLeadByPhone()
    {
        if (!$this->destinationNumber) {
            return null;
        }

        // Try to find lead by full phone number
        $lead = Lead::whereRaw("CONCAT(code, phone) = ?", [$this->destinationNumber])->first();
        
        if (!$lead) {
            // Try to find by called number
            $lead = Lead::whereRaw("CONCAT(code, phone) = ?", [$this->calledNumber])->first();
        }
        
        return $lead;
    }
}
