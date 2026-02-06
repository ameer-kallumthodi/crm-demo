<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionCounsellor extends User
{
    use SoftDeletes;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'code',
        'password',
        'role_id',
        'is_active',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::addGlobalScope('admission_counsellor', function ($builder) {
            $builder->where('role_id', 4);
        });
    }

    /**
     * Get the user role that owns the admission counsellor.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(UserRole::class, 'role_id');
    }


    /**
     * Get the leads for the admission counsellor.
     */
    public function leads()
    {
        return $this->hasMany(Lead::class, 'telecaller_id');
    }

    /**
     * Scope a query to only include active admission counsellors.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
