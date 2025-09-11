<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'team_lead_id',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the team lead that owns the team.
     */
    public function teamLead()
    {
        return $this->belongsTo(User::class, 'team_lead_id');
    }

    /**
     * Get the users for the team.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
