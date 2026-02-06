<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConvertedLeadIdCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'converted_lead_id',
        'file_path',
        'file_name',
        'generated_at',
        'generated_by',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    public function convertedLead()
    {
        return $this->belongsTo(ConvertedLead::class);
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
