<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UniversityCourse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'university_id',
        'title',
        'amount',
        'description',
        'course_type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'amount' => 'double',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'university_course_id');
    }

    public function convertedLeads()
    {
        return $this->hasMany(ConvertedLead::class, 'university_course_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByUniversity($query, $universityId)
    {
        return $query->where('university_id', $universityId);
    }
}
