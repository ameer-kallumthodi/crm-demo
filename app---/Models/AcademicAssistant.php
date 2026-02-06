<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicAssistant extends User
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
        
        static::addGlobalScope('academic_assistant', function ($builder) {
            $builder->where('role_id', 5);
        });
    }

    /**
     * Get the user role that owns the academic assistant.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(UserRole::class, 'role_id');
    }


    /**
     * Get the leads for the academic assistant.
     */
    public function leads()
    {
        return $this->hasMany(Lead::class, 'telecaller_id');
    }

    /**
     * Scope a query to only include active academic assistants.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
