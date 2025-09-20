<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Finance extends User
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
        
        static::addGlobalScope('finance', function ($builder) {
            $builder->where('role_id', 6); // Finance role ID
        });
    }

    /**
     * Get the user role that owns the finance user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(UserRole::class, 'role_id');
    }

    /**
     * Get the leads for the finance user.
     */
    public function leads()
    {
        return $this->hasMany(Lead::class, 'telecaller_id');
    }

    /**
     * Scope a query to only include active finance users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
