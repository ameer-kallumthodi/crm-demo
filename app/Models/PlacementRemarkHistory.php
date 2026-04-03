<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlacementRemarkHistory extends Model
{
    protected $table = 'placement_remark_histories';

    protected $fillable = [
        'converted_lead_id',
        'remarks',
        'user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function convertedLead(): BelongsTo
    {
        return $this->belongsTo(ConvertedLead::class, 'converted_lead_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
