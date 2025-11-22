<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassTime extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'from_time',
        'to_time',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'from_time' => 'datetime',
        'to_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }
}
