<?php namespace App\Models;

use CodeIgniter\Model;

class Call_log_model extends Base_model
{
    protected $table         = 'call_log';      // Database table name
    protected $primaryKey    = 'id';         // Primary key of the table
    protected $returnType    = 'App\Entities\Call_log';  // Entity class name
    protected $useTimestamps = true;         // Auto handle timestamps
    protected $allowedFields = ['title','nice_name'];  // Fields that can be manipulated

    // Optional: Define validation rules
    protected $validationRules    = [
        'title' => 'required|min_length[2]',
  
    ];



}