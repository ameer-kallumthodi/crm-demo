<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Batch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'amount',
        'sslc_amount',
        'plustwo_amount',
        'b2b_amount',
        'course_id',
        'is_active',
        'postpone_batch_id',
        'postpone_start_date',
        'postpone_end_date',
        'batch_postpone_amount',
        'is_postpone_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_postpone_active' => 'boolean',
        'amount' => 'decimal:2',
        'sslc_amount' => 'decimal:2',
        'plustwo_amount' => 'decimal:2',
        'b2b_amount' => 'decimal:2',
        'batch_postpone_amount' => 'decimal:2',
        'postpone_start_date' => 'date',
        'postpone_end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

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

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function convertedLeads()
    {
        return $this->hasMany(ConvertedLead::class);
    }

    public function admissionBatches()
    {
        return $this->hasMany(AdmissionBatch::class);
    }

    public function postponeBatch()
    {
        return $this->belongsTo(Batch::class, 'postpone_batch_id');
    }

    public function postponedBatches()
    {
        return $this->hasMany(Batch::class, 'postpone_batch_id');
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
