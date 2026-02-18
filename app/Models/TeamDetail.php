<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamDetail extends Model
{
    protected $fillable = [
        'team_id',
        'legal_name',
        'institution_category',
        'registration_number',
        'building_name',
        'street_name',
        'locality_name',
        'city',
        'pin_code',
        'district',
        'state',
        'country',
        'comm_officer_name',
        'comm_officer_mobile',
        'comm_officer_alt_mobile',
        'comm_officer_whatsapp',
        'comm_officer_email',
        'auth_person_name',
        'auth_person_designation',
        'auth_person_mobile',
        'auth_person_email',
        'interested_courses_details',
    ];

    protected $casts = [
        'interested_courses_details' => 'array',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
