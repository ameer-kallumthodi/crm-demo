<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarketingLead extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'marketing_leads';

    protected $fillable = [
        'marketing_bde_id',
        'date_of_visit',
        'location',
        'house_number',
        'lead_name',
        'code',
        'phone',
        'whatsapp_code',
        'whatsapp',
        'address',
        'lead_type',
        'interested_courses',
        'remarks',
        'is_telecaller_assigned',
        'assigned_at',
        'assigned_by',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'date_of_visit' => 'date',
        'interested_courses' => 'array',
        'is_telecaller_assigned' => 'boolean',
        'assigned_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function marketingBde()
    {
        return $this->belongsTo(User::class, 'marketing_bde_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
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

    /**
     * Override the delete method to set deleted_by
     */
    public function delete()
    {
        $this->deleted_by = \App\Helpers\AuthHelper::getCurrentUserId();
        $this->save();
        
        return parent::delete();
    }
}
