<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Notification extends Model
{
    protected $fillable = [
        'title',
        'message',
        'type',
        'target_type',
        'role_id',
        'user_id',
        'created_by',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get the role that owns the notification.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(UserRole::class, 'role_id');
    }

    /**
     * Get the user that owns the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who created the notification.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the notification reads for this notification.
     */
    public function reads(): HasMany
    {
        return $this->hasMany(NotificationRead::class);
    }

    /**
     * Check if a specific user has read this notification.
     */
    public function isReadBy($userId): bool
    {
        return $this->reads()->where('user_id', $userId)->exists();
    }

    /**
     * Mark notification as read by a user.
     */
    public function markAsReadBy($userId): void
    {
        $this->reads()->updateOrCreate(
            ['user_id' => $userId],
            ['read_at' => now()]
        );
    }

    /**
     * Scope to get notifications for a specific user based on their role.
     */
    public function scopeForUser($query, $userId, $roleId)
    {
        return $query->where('is_active', true)
            ->where(function ($q) use ($userId, $roleId) {
                $q->where('target_type', 'all')
                  ->orWhere('target_type', 'all_role')
                  ->orWhere(function ($subQ) use ($roleId) {
                      $subQ->where('target_type', 'role')
                           ->where('role_id', $roleId);
                  })
                  ->orWhere(function ($subQ) use ($userId) {
                      $subQ->where('target_type', 'user')
                           ->where('user_id', $userId);
                  });
            });
    }
}
