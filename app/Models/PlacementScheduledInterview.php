<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlacementScheduledInterview extends Model
{
    use HasFactory;

    protected $table = 'placement_scheduled_interviews';

    protected $fillable = [
        'converted_lead_id',
        'company_name',
        'place',
        'interview_date',
        'status',
    ];

    protected $casts = [
        'interview_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_PLACED = 'placed';
    public const STATUS_NOT_PLACED = 'not_placed';

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PLACED => 'Placed',
            self::STATUS_NOT_PLACED => 'Not Placed',
        ];
    }

    public function convertedLead()
    {
        return $this->belongsTo(ConvertedLead::class, 'converted_lead_id');
    }
}
