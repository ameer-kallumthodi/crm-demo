<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PaymentLink;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'invoice_type',
        'course_id',
        'batch_id',
        'student_id',
        'total_amount',
        'fee_pg_amount',
        'fee_ug_amount',
        'fee_plustwo_amount',
        'fee_sslc_amount',
        'paid_amount',
        'status',
        'invoice_date',
        'previous_balance',
        'service_name',
        'service_amount',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'fee_pg_amount' => 'decimal:2',
        'fee_ug_amount' => 'decimal:2',
        'fee_plustwo_amount' => 'decimal:2',
        'fee_sslc_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'previous_balance' => 'decimal:2',
        'service_amount' => 'decimal:2',
        'invoice_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function student()
    {
        return $this->belongsTo(ConvertedLead::class, 'student_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function paymentLinks()
    {
        return $this->hasMany(PaymentLink::class);
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


    // Accessors
    public function getPendingAmountAttribute()
    {
        return $this->total_amount - $this->paid_amount;
    }

    public function getCurrentBalanceAttribute()
    {
        return $this->total_amount - $this->paid_amount;
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    // Methods
    public function updateStatus()
    {
        if ($this->paid_amount == 0) {
            $this->status = 'Not Paid';
        } elseif ($this->paid_amount >= $this->total_amount) {
            $this->status = 'Fully Paid';
        } else {
            $this->status = 'Partially Paid';
        }
        
        $this->save();
    }

    public function recalculatePaidAmount()
    {
        // Calculate total paid amount from all approved payments
        $totalPaid = $this->payments()
            ->where('status', 'Approved')
            ->sum('amount_paid');
        
        // Calculate previous balance (total amount - paid amount)
        $this->previous_balance = $this->total_amount - $totalPaid;
        $this->paid_amount = $totalPaid;
        $this->save();
    }

    public function addPayment($amount, $paymentType, $transactionId = null, $fileUpload = null)
    {
        $previousBalance = $this->current_balance;
        
        $payment = $this->payments()->create([
            'amount_paid' => $amount,
            'payment_type' => $paymentType,
            'transaction_id' => $transactionId,
            'file_upload' => $fileUpload,
            'status' => 'Pending Approval',
            'created_by' => \App\Helpers\AuthHelper::getCurrentUserId(),
        ]);

        // Update invoice paid amount and status
        $this->paid_amount += $amount;
        $this->previous_balance = $previousBalance;
        $this->updateStatus();

        return $payment;
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
