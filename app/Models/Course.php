<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'duration',
        'fees',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'fees' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
