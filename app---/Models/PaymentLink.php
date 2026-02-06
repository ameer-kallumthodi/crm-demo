<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class PaymentLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'amount',
        'currency',
        'status',
        'reference_id',
        'razorpay_id',
        'razorpay_payment_id',
        'short_url',
        'token',
        'description',
        'customer_name',
        'customer_email',
        'customer_phone',
        'paid_at',
        'expires_at',
        'meta',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'meta' => 'array',
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['paid', 'cancelled']);
    }

    public function getAmountFormattedAttribute(): string
    {
        return '₹' . number_format($this->amount, 2);
    }

    public function getShortUrlAttribute($value): ?string
    {
        return $value;
    }
}

