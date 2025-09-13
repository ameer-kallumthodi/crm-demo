<?php namespace App\Models;

use CodeIgniter\Model;

class Voxbay_calllogs_model extends Base_model
{
    protected $table         = 'voxbay_call_logs';
    protected $primaryKey    = 'id';
    protected $returnType    = 'App\Entities\Voxbay_calllogs';
    protected $useTimestamps = true;
    protected $allowedFields = [
                                    
                                ];



    protected $validationRules = [];
}
