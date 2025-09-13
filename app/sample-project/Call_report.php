<?php

namespace App\Controllers\App;

use App\Models\Call_report_model;
use App\Models\Teams_model;
use App\Models\Users_model;

class Call_report extends AppBaseController
{
    private $call_report_model;
    private $teams_model;
    private $users_model;
    
    public function __construct()
    {
        parent::__construct();
        $this->call_report_model = new Call_report_model();
        $this->teams_model = new Teams_model();
        $this->users_model = new Users_model();
    }
    
    public function index()
    {
        // Get filter parameters
        $filters = [];
        
        // Single date filter (default to today)
        $filters['date'] = $this->request->getGet('date') ?? date('Y-m-d');
        
        // Team filter
        $filters['team_id'] = $this->request->getGet('team_id');
        
        // User filter
        $filters['user_id'] = $this->request->getGet('user_id');
        
        // Check if user is admin
        $isAdmin = is_admin();
        
        // If not admin, show only logged in user's data
        if (!$isAdmin) {
            $filters['logged_user_id'] = get_user_id();
            $filters['is_not_admin'] = true;
        }
        
        // Get all telecallers (role_id = 6)
        $all_telecallers = $this->users_model->get(['role_id' => 6])->getResultArray();
        
        // Get call report data for the specific date from voxbay_call_logs only
        $call_report_data = $this->call_report_model->getVoxbayCallReportByDate($filters);
        
        // Create a comprehensive report including all telecallers
        $this->data['report_data'] = [];
        foreach ($all_telecallers as $telecaller) {
            // Find if this telecaller has data in the report
            $telecaller_data = null;
            foreach ($call_report_data as $data) {
                if ($data['user_id'] == $telecaller['id']) {
                    $telecaller_data = $data;
                    break;
                }
            }
            
            // If no data found, create empty record
            if (!$telecaller_data) {
                $telecaller_data = [
                    'user_id' => $telecaller['id'],
                    'name' => $telecaller['name'],
                    'role_id' => $telecaller['role_id'],
                    'team_id' => $telecaller['team_id'] ?? 0,
                    'ext_no' => $telecaller['ext_no'] ?? '',
                    'agent_number' => $telecaller['code'] . $telecaller['phone'],
                    'total_outgoing_calls' => 0,
                    'answered_outgoing' => 0,
                    'no_answered_outgoing' => 0,
                    'busy_outgoing' => 0,
                    'cancel_outgoing' => 0,
                    'outgoing_duration' => 0,
                    'total_calls_incoming_missed' => 0,
                    'total_missed_calls' => 0,
                    'incoming_duration' => 0,
                    'number_of_calls_handled' => 0,
                    'average_call_handling_time' => 0
                ];
            } else {
                // Add extension number
                $telecaller_data['ext_no'] = $telecaller['ext_no'] ?? '';
            }
            
            // Add team name
            $team_name = 'Not Assigned';
            if (!empty($telecaller_data['team_id'])) {
                $team_info = $this->teams_model->get(['id' => $telecaller_data['team_id']])->getRowArray();
                if ($team_info) {
                    $team_name = $team_info['title'];
                }
            }
            $telecaller_data['team_name'] = $team_name;
            
            // Apply team filter if set
            if (!empty($filters['team_id']) && $telecaller_data['team_id'] != $filters['team_id']) {
                continue;
            }
            
            // Apply user filter if set
            if (!empty($filters['user_id']) && $telecaller_data['user_id'] != $filters['user_id']) {
                continue;
            }
            
            // If not admin, show only logged in user
            if (!$isAdmin && $telecaller_data['user_id'] != get_user_id()) {
                continue;
            }
            
            $this->data['report_data'][] = $telecaller_data;
        }
        
        // Get filter options
        $this->data['teams'] = $this->teams_model->get()->getResultArray();
        $this->data['users'] = $this->users_model->get(['role_id' => 6])->getResultArray();
        
        // Pass filters to view
        $this->data['filters'] = $filters;
        $this->data['is_admin'] = $isAdmin;
        
        // Calculate totals
        $totals = [
            'total_outgoing_calls' => 0,
            'answered_outgoing' => 0,
            'no_answered_outgoing' => 0,
            'busy_outgoing' => 0,
            'cancel_outgoing' => 0,
            'outgoing_duration' => 0,
            'total_calls_incoming_missed' => 0,
            'total_missed_calls' => 0,
            'incoming_duration' => 0,
            'number_of_calls_handled' => 0,
            'average_call_handling_time' => 0
        ];
        
        $totalHandledCalls = 0;
        $totalDuration = 0;
        
        foreach ($this->data['report_data'] as $row) {
            $totals['total_outgoing_calls'] += $row['total_outgoing_calls'];
            $totals['answered_outgoing'] += $row['answered_outgoing'];
            $totals['no_answered_outgoing'] += $row['no_answered_outgoing'];
            $totals['busy_outgoing'] += $row['busy_outgoing'];
            $totals['cancel_outgoing'] += $row['cancel_outgoing'];
            $totals['outgoing_duration'] += $row['outgoing_duration'];
            $totals['total_calls_incoming_missed'] += $row['total_calls_incoming_missed'];
            $totals['total_missed_calls'] += $row['total_missed_calls'];
            $totals['incoming_duration'] += $row['incoming_duration'];
            $totals['number_of_calls_handled'] += $row['number_of_calls_handled'];
            
            // For overall average calculation
            $handledCalls = $row['answered_outgoing'] + ($row['total_calls_incoming_missed'] - $row['total_missed_calls']);
            if ($handledCalls > 0) {
                $totalHandledCalls += $handledCalls;
                $totalDuration += ($row['outgoing_duration'] + $row['incoming_duration']);
            }
        }
        
        // Calculate overall average
        if ($totalHandledCalls > 0) {
            $totals['average_call_handling_time'] = round($totalDuration / $totalHandledCalls, 2);
        }
        
        $this->data['totals'] = $totals;
        
        $this->data['page_title'] = 'Call Report';
        $this->data['page_name'] = 'Call_report/index';
        return view('App/index', $this->data);
    }
    
    public function export()
    {
        // Get the same filters as index
        $filters = [];
        
        $filters['date'] = $this->request->getGet('date') ?? date('Y-m-d');
        $filters['team_id'] = $this->request->getGet('team_id');
        $filters['user_id'] = $this->request->getGet('user_id');
        
        $isAdmin = is_admin();
        
        if (!$isAdmin) {
            $filters['logged_user_id'] = get_user_id();
            $filters['is_not_admin'] = true;
        }
        
        // Get all telecallers (role_id = 6)
        $all_telecallers = $this->users_model->get(['role_id' => 6])->getResultArray();
        
        // Get call report data for the specific date from voxbay_call_logs only
        $call_report_data = $this->call_report_model->getVoxbayCallReportByDate($filters);
        
        // Create a comprehensive report including all telecallers
        $reportData = [];
        foreach ($all_telecallers as $telecaller) {
            // Find if this telecaller has data in the report
            $telecaller_data = null;
            foreach ($call_report_data as $data) {
                if ($data['user_id'] == $telecaller['id']) {
                    $telecaller_data = $data;
                    break;
                }
            }
            
            // If no data found, create empty record
            if (!$telecaller_data) {
                $telecaller_data = [
                    'user_id' => $telecaller['id'],
                    'name' => $telecaller['name'],
                    'role_id' => $telecaller['role_id'],
                    'team_id' => $telecaller['team_id'] ?? 0,
                    'ext_no' => $telecaller['ext_no'] ?? '',
                    'agent_number' => $telecaller['code'] . $telecaller['phone'],
                    'total_outgoing_calls' => 0,
                    'answered_outgoing' => 0,
                    'no_answered_outgoing' => 0,
                    'busy_outgoing' => 0,
                    'cancel_outgoing' => 0,
                    'outgoing_duration' => 0,
                    'total_calls_incoming_missed' => 0,
                    'total_missed_calls' => 0,
                    'incoming_duration' => 0,
                    'number_of_calls_handled' => 0,
                    'average_call_handling_time' => 0
                ];
            }
            
            // Apply team filter if set
            if (!empty($filters['team_id']) && $telecaller_data['team_id'] != $filters['team_id']) {
                continue;
            }
            
            // Apply user filter if set
            if (!empty($filters['user_id']) && $telecaller_data['user_id'] != $filters['user_id']) {
                continue;
            }
            
            // If not admin, show only logged in user
            if (!$isAdmin && $telecaller_data['user_id'] != get_user_id()) {
                continue;
            }
            
            $reportData[] = $telecaller_data;
        }
        
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="call_report_' . $filters['date'] . '.csv"');
        
        // Open output stream
        $output = fopen('php://output', 'w');
        
        // Write header row
        fputcsv($output, [
            'Sl.No',
            'Name',
            'Extension',
            'Total Outgoing Calls',
            'Answered Outgoing',
            'No Answered Outgoing',
            'Busy Outgoing',
            'Cancel Outgoing',
            'Outgoing Duration',
            'Total Calls (Incoming+Missed)',
            'Total Missed Calls',
            'Incoming Duration',
            'Number Of Calls Handled',
            'Average Call Handling Time (AHT)'
        ]);
        
        // Write data rows
        foreach ($reportData as $index => $row) {
            fputcsv($output, [
                $index + 1,
                $row['name'],
                $row['ext_no'] ?? '',
                $row['total_outgoing_calls'],
                $row['answered_outgoing'],
                $row['no_answered_outgoing'],
                $row['busy_outgoing'],
                $row['cancel_outgoing'],
                $this->call_report_model->formatDuration($row['outgoing_duration']),
                $row['total_calls_incoming_missed'],
                $row['total_missed_calls'],
                $this->call_report_model->formatDuration($row['incoming_duration']),
                $row['number_of_calls_handled'],
                $this->call_report_model->formatDuration($row['average_call_handling_time'])
            ]);
        }
        
        fclose($output);
        exit;
    }
}