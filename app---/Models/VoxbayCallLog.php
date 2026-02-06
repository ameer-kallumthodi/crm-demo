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

        // IMPORTANT: these additional fields are in your controller operations
        'destination',      // outgoing call event
        'extension',        // outgoing call event (used as fallback)
        'callUUlD',         // typo alias from API
        'call_UUID',        // some events use this format
        'callUUID',         // incoming/outgoing connect/disconnect
        'caller_number',    // fallback input from API
        'called_number',    // fallback input from API
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
        if (!$this->duration) return 'N/A';

        $seconds = (int) $this->duration;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function getCallStatusBadgeAttribute()
    {
        $status = strtoupper($this->status ?? 'UNKNOWN');

        $badgeClasses = [
            'ANSWER' => 'badge-success',
            'CANCEL' => 'badge-warning',
            'BUSY' => 'badge-danger',
            'CANCELLED' => 'badge-warning',
            'NO ANSWER' => 'badge-secondary',
        ];

        $class = $badgeClasses[$status] ?? 'badge-secondary';

        return "<span class='badge {$class}'>{$status}</span>";
    }

    public function getTelecallerName()
    {
        // Try to match by ext_no with AgentNumber or extensionNumber
        $extensionNumber = $this->extensionNumber ?? $this->AgentNumber;
        if (!$extensionNumber) {
            return 'Unknown';
        }

        // Match ext_no with AgentNumber or extensionNumber where role_id = 3 (telecaller)
        $user = User::where('ext_no', $extensionNumber)
            ->where('role_id', 3)
            ->first();

        if ($user) {
            return $user->name;
        }

        // Fallback: Try matching by phone number (original logic) if ext_no doesn't match
        if ($this->AgentNumber && strlen($this->AgentNumber) >= 2) {
            $countryCode = substr($this->AgentNumber, 0, 2);
            $mobileNumber = substr($this->AgentNumber, 2);

            $user = User::where('code', $countryCode)
                ->where('phone', $mobileNumber)
                ->where('role_id', 3)
                ->first();

            if ($user) {
                return $user->name;
            }
        }

        return 'Unknown';
    }

    public function getLeadByPhone()
    {
        if (!$this->destinationNumber) return null;

        $lead = Lead::whereRaw("CONCAT(code, phone) = ?", [$this->destinationNumber])->first();

        if (!$lead) {
            $lead = Lead::whereRaw("CONCAT(code, phone) = ?", [$this->calledNumber])->first();
        }

        return $lead;
    }
}
