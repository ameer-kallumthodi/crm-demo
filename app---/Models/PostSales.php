<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostSales extends User
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
        'is_head',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::addGlobalScope('post_sales', function ($builder) {
            $builder->where('role_id', 7); // Post-sales role ID
        });
    }

    /**
     * Get the user role that owns the post-sales user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(UserRole::class, 'role_id');
    }

    /**
     * Get the leads for the post-sales user.
     */
    public function leads()
    {
        return $this->hasMany(Lead::class, 'telecaller_id');
    }

    /**
     * Scope a query to only include active post-sales users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include head post-sales users.
     */
    public function scopeHead($query)
    {
        return $query->where('is_head', true);
    }
}
