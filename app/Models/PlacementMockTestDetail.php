<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlacementMockTestDetail extends Model
{
    use HasFactory;

    protected $table = 'placement_mock_test_details';

    protected $fillable = [
        'converted_lead_id',
        'speaking_capacity',
        'presentation_skill',
        'character',
        'dedication',
        'remark',
    ];

    protected $casts = [
        'speaking_capacity' => 'integer',
        'presentation_skill' => 'integer',
        'character' => 'integer',
        'dedication' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function convertedLead()
    {
        return $this->belongsTo(ConvertedLead::class, 'converted_lead_id');
    }
}
