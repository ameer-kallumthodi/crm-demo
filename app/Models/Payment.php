<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_id',
        'amount_paid',
        'previous_balance',
        'payment_type',
        'transaction_id',
        'file_upload',
        'status',
        'approved_date',
        'approved_by',
        'rejected_date',
        'rejected_by',
        'rejection_remarks',
        'created_by',
        'updated_by',
        'deleted_by',
        'collected_by',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'previous_balance' => 'decimal:2',
        'approved_date' => 'datetime',
        'rejected_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
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

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function collectedBy()
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPaymentType($query, $paymentType)
    {
        return $query->where('payment_type', $paymentType);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'Pending Approval');
    }

    // Methods
    public function approve()
    {
        $this->status = 'Approved';
        $this->approved_date = now();
        $this->approved_by = \App\Helpers\AuthHelper::getCurrentUserId();
        $this->updated_by = \App\Helpers\AuthHelper::getCurrentUserId();
        $this->save();

        // Recalculate invoice paid amount from all approved payments
        $this->invoice->recalculatePaidAmount();
        
        // Update invoice status after approval
        $this->invoice->updateStatus();
    }

    public function reject($remarks = null)
    {
        $this->status = 'Rejected';
        $this->rejected_date = now();
        $this->rejected_by = \App\Helpers\AuthHelper::getCurrentUserId();
        $this->updated_by = \App\Helpers\AuthHelper::getCurrentUserId();
        if ($remarks) {
            $this->rejection_remarks = $remarks;
        }
        $this->save();

        // Recalculate invoice paid amount from all approved payments
        $this->invoice->recalculatePaidAmount();
        
        // Update invoice status after rejection
        $this->invoice->updateStatus();
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
