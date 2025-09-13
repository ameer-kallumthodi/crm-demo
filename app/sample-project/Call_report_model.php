<?php namespace App\Models;

use CodeIgniter\Model;

class Call_report_model extends Base_model
{
    protected $table         = 'voxbay_call_logs';      // Database table name
    protected $primaryKey    = 'id';         // Primary key of the table
    protected $useTimestamps = true;         // Auto handle timestamps
    protected $allowedFields = [];  // Fields that can be manipulated

    public function getVoxbayCallReportByDate($filters = [])
    {
        $builder = $this->db->table('voxbay_call_logs vcl');
        $builder->select('
            u.id as user_id,
            u.name,
            u.role_id,
            u.team_id,
            u.ext_no,
            CONCAT(u.code, u.phone) as agent_number,
            COUNT(CASE WHEN vcl.type = "outgoing" THEN 1 END) as total_outgoing_calls,
            COUNT(CASE WHEN vcl.type = "outgoing" AND vcl.status = "ANSWER" THEN 1 END) as answered_outgoing,
            COUNT(CASE WHEN vcl.type = "outgoing" AND vcl.status = "NOANSWER" THEN 1 END) as no_answered_outgoing,
            COUNT(CASE WHEN vcl.type = "outgoing" AND vcl.status = "BUSY" THEN 1 END) as busy_outgoing,
            COUNT(CASE WHEN vcl.type = "outgoing" AND vcl.status = "CANCEL" THEN 1 END) as cancel_outgoing,
            SUM(CASE WHEN vcl.type = "outgoing" AND vcl.duration > 0 THEN CAST(vcl.duration AS UNSIGNED) ELSE 0 END) as outgoing_duration,
            COUNT(CASE WHEN vcl.type IN ("incoming", "missedcall") THEN 1 END) as total_calls_incoming_missed,
            COUNT(CASE WHEN vcl.type = "missedcall" THEN 1 END) as total_missed_calls,
            SUM(CASE WHEN vcl.type = "incoming" AND vcl.duration > 0 THEN CAST(vcl.duration AS UNSIGNED) ELSE 0 END) as incoming_duration,
            COUNT(DISTINCT CASE WHEN vcl.duration > 0 THEN vcl.calledNumber END) as number_of_calls_handled,
            AVG(CASE WHEN vcl.duration > 0 THEN CAST(vcl.duration AS UNSIGNED) END) as average_call_handling_time
        ');
        
        // Join using AgentNumber = code + phone (same logic as voxbay_calllogs/list)
        $builder->join('users u', 'CONCAT(u.code, u.phone) = vcl.AgentNumber', 'inner');
        $builder->where('u.deleted_at IS NULL');
        $builder->where('vcl.deleted_at IS NULL');
        $builder->where('u.role_id', 6); // Only telecallers
        
        // Apply filters
        if (!empty($filters['date'])) {
            $builder->where('vcl.date', $filters['date']);
        }
        
        if (!empty($filters['team_id'])) {
            $builder->where('u.team_id', $filters['team_id']);
        }
        
        if (!empty($filters['user_id'])) {
            $builder->where('u.id', $filters['user_id']);
        }
        
        if (!empty($filters['telecaller_id'])) {
            $builder->where('u.id', $filters['telecaller_id']);
        }
        // If not admin, show only logged in user's data
        if (!empty($filters['logged_user_id']) && !empty($filters['is_not_admin'])) {
            $builder->where('u.id', $filters['logged_user_id']);
        }
        
        $builder->groupBy('u.id, u.name, u.role_id, u.team_id, u.ext_no, agent_number');
        $builder->orderBy('u.name', 'ASC');
        
        return $builder->get()->getResultArray();
    }
    
    public function getCallReport($filters = [])
    {
        $builder = $this->db->table('call_log cl');
        $builder->select('
            u.id as user_id,
            u.name,
            u.role_id,
            COUNT(CASE WHEN cl.type = 2 THEN 1 END) as total_outgoing_calls,
            COUNT(CASE WHEN cl.type = 2 AND cl.duration > 0 THEN 1 END) as answered_outgoing,
            COUNT(CASE WHEN cl.type = 2 AND (cl.duration IS NULL OR cl.duration = 0) THEN 1 END) as no_answered_outgoing,
            COUNT(CASE WHEN cl.type = 0 THEN 1 END) as busy_outgoing,
            COUNT(CASE WHEN cl.type = 2 AND cl.duration < 0 THEN 1 END) as cancel_outgoing,
            SUM(CASE WHEN cl.type = 2 AND cl.duration > 0 THEN cl.duration ELSE 0 END) as outgoing_duration,
            COUNT(CASE WHEN cl.type IN (1, 3) THEN 1 END) as total_calls_incoming_missed,
            COUNT(CASE WHEN cl.type = 3 THEN 1 END) as total_missed_calls,
            SUM(CASE WHEN cl.type = 1 AND cl.duration > 0 THEN cl.duration ELSE 0 END) as incoming_duration,
            COUNT(DISTINCT CASE WHEN cl.duration > 0 THEN cl.lead_phone END) as number_of_calls_handled,
            AVG(CASE WHEN cl.duration > 0 THEN cl.duration END) as average_call_handling_time
        ');
        
        $builder->join('users u', 'u.id = cl.telecaller_id', 'inner');
        $builder->where('u.deleted_at IS NULL');
        $builder->where('cl.deleted_at IS NULL');
        
        // Apply filters
        if (!empty($filters['user_id'])) {
            $builder->where('cl.telecaller_id', $filters['user_id']);
        }
        
        if (!empty($filters['from_date'])) {
            $builder->where('cl.date >=', $filters['from_date']);
        }
        
        if (!empty($filters['to_date'])) {
            $builder->where('cl.date <=', $filters['to_date']);
        }
        
        if (!empty($filters['team_id'])) {
            $builder->where('u.team_id', $filters['team_id']);
        }
        
        if (!empty($filters['role_id'])) {
            $builder->where('u.role_id', $filters['role_id']);
        }
        
        // If not admin, show only logged in user's data
        if (!empty($filters['logged_user_id']) && !empty($filters['is_not_admin'])) {
            $builder->where('cl.telecaller_id', $filters['logged_user_id']);
        }
        
        $builder->groupBy('u.id, u.name, u.role_id');
        $builder->orderBy('u.name', 'ASC');
        
        return $builder->get()->getResultArray();
    }
    
    public function getVoxbayCallReport($filters = [])
    {
        $builder = $this->db->table('voxbay_call_logs vcl');
        $builder->select('
            u.id as user_id,
            u.name,
            u.role_id,
            COUNT(CASE WHEN vcl.type = "outgoing" THEN 1 END) as total_outgoing_calls,
            COUNT(CASE WHEN vcl.type = "outgoing" AND vcl.status = "ANSWER" THEN 1 END) as answered_outgoing,
            COUNT(CASE WHEN vcl.type = "outgoing" AND vcl.status = "NOANSWER" THEN 1 END) as no_answered_outgoing,
            COUNT(CASE WHEN vcl.type = "outgoing" AND vcl.status = "BUSY" THEN 1 END) as busy_outgoing,
            COUNT(CASE WHEN vcl.type = "outgoing" AND vcl.status = "CANCEL" THEN 1 END) as cancel_outgoing,
            SUM(CASE WHEN vcl.type = "outgoing" AND vcl.duration > 0 THEN vcl.duration ELSE 0 END) as outgoing_duration,
            COUNT(CASE WHEN vcl.type IN ("incoming", "missedcall") THEN 1 END) as total_calls_incoming_missed,
            COUNT(CASE WHEN vcl.type = "missedcall" THEN 1 END) as total_missed_calls,
            SUM(CASE WHEN vcl.type = "incoming" AND vcl.duration > 0 THEN vcl.duration ELSE 0 END) as incoming_duration,
            COUNT(DISTINCT CASE WHEN vcl.duration > 0 THEN vcl.calledNumber END) as number_of_calls_handled,
            AVG(CASE WHEN vcl.duration > 0 THEN vcl.duration END) as average_call_handling_time
        ');
        
        $builder->join('users u', 'u.ext_no = vcl.extensionNumber', 'inner');
        $builder->where('u.deleted_at IS NULL');
        $builder->where('vcl.deleted_at IS NULL');
        
        // Apply filters
        if (!empty($filters['user_id'])) {
            $builder->where('u.id', $filters['user_id']);
        }
        
        if (!empty($filters['from_date'])) {
            $builder->where('vcl.date >=', $filters['from_date']);
        }
        
        if (!empty($filters['to_date'])) {
            $builder->where('vcl.date <=', $filters['to_date']);
        }
        
        if (!empty($filters['team_id'])) {
            $builder->where('u.team_id', $filters['team_id']);
        }
        
        if (!empty($filters['role_id'])) {
            $builder->where('u.role_id', $filters['role_id']);
        }
        
        // If not admin, show only logged in user's data
        if (!empty($filters['logged_user_id']) && !empty($filters['is_not_admin'])) {
            $builder->where('u.id', $filters['logged_user_id']);
        }
        
        $builder->groupBy('u.id, u.name, u.role_id');
        $builder->orderBy('u.name', 'ASC');
        
        return $builder->get()->getResultArray();
    }
    
    public function formatDuration($seconds)
    {
        if ($seconds <= 0) return '0s';
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
        
        $parts = [];
        if ($hours > 0) $parts[] = $hours . 'h';
        if ($minutes > 0) $parts[] = $minutes . 'm';
        if ($seconds > 0) $parts[] = $seconds . 's';
        
        return implode(' ', $parts);
    }
}