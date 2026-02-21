<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamDetail extends Model
{
    protected $fillable = [
        'team_id',
        'legal_name',
        'institution_category',
        'telephone',
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
        'b2b_partner_id',
        'b2b_code',
        'date_of_joining',
        'partner_status',
        'b2b_officer_name',
        'employee_id',
        'designation',
        'official_contact_number',
        'whatsapp_business_number',
        'official_email_id',
        'working_days',
        'office_hours',
        'break_time',
        'holiday_policy',
        'account_holder_name',
        'bank_name',
        'account_number',
        'ifsc_code',
        'terms_and_conditions',
    ];

    protected $casts = [
        'interested_courses_details' => 'array',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
