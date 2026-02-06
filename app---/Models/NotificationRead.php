<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationRead extends Model
{
    protected $fillable = [
        'notification_id',
        'user_id',
        'read_at'
    ];

    protected $casts = [
        'read_at' => 'datetime'
    ];

    /**
     * Get the notification that owns the read record.
     */
    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class);
    }

    /**
     * Get the user that owns the read record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
