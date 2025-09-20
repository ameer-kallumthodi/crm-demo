<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadStatus extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'color',
        'interest_status',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function leadActivities()
    {
        return $this->hasMany(LeadActivity::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getInterestStatusLabelAttribute()
    {
        switch ($this->interest_status) {
            case 1:
                return 'Hot';
            case 2:
                return 'Warm';
            case 3:
                return 'Cold';
            default:
                return 'Not Set';
        }
    }

    public function getInterestStatusColorAttribute()
    {
        switch ($this->interest_status) {
            case 1:
                return 'danger'; // Red for Hot
            case 2:
                return 'warning'; // Yellow for Warm
            case 3:
                return 'info'; // Blue for Cold
            default:
                return 'secondary';
        }
    }
}
