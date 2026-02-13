<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class B2bService extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Get the user who created the record.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class , 'created_by');
    }

    /**
     * Get the user who last updated the record.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class , 'updated_by');
    }

    /**
     * Get the user who deleted the record.
     */
    public function deletedBy()
    {
        return $this->belongsTo(User::class , 'deleted_by');
    }

    /**
     * Override the delete method to set deleted_by
     */
    public function delete()
    {
        $this->deleted_by = \App\Helpers\AuthHelper::getCurrentUserId();
        $this->save();

        return parent::delete();
    }
}
