<?php

namespace App\Models;

use App\Helpers\AuthHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConvertedStudentActivity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'converted_lead_id',
        'status',
        'paid_status',
        'call_status',
        'called_date',
        'activity_type',
        'description',
        'remark',
        'activity_date',
        'activity_time',
        'followup_date',
        'followup_time',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'activity_date' => 'date',
        'followup_date' => 'date',
        'called_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function convertedLead()
    {
        return $this->belongsTo(ConvertedLead::class);
    }

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

    public function scopeForConvertedLead($query, int $convertedLeadId)
    {
        return $query->where('converted_lead_id', $convertedLeadId);
    }

    /**
     * Override the delete method to set deleted_by
     */
    public function delete()
    {
        $this->deleted_by = AuthHelper::getCurrentUserId();
        $this->save();

        return parent::delete();
    }
}

