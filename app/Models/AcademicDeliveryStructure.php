<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class AcademicDeliveryStructure extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'course_id',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class , 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class , 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class , 'deleted_by');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (\Auth::check()) {
                $model->created_by = \Auth::id();
            }
        });

        static::updating(function ($model) {
            if (\Auth::check()) {
                $model->updated_by = \Auth::id();
            }
        });

        static::deleting(function ($model) {
            if (\Auth::check()) {
                $model->deleted_by = \Auth::id();
                $model->save();
            }
        });
    }
}
