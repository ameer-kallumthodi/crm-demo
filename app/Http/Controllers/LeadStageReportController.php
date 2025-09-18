<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadStatus;
use App\Helpers\AuthHelper;
use App\Helpers\RoleHelper;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;

class LeadStageReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth');
    }

    /**
     * Lead Stage Movement Report
     * Shows leads getting stuck in early stages and not moving to Follow-Up or Converted
     */
    public function index(Request $request)
    {
        // Check if user is super admin
        if (!RoleHelper::is_super_admin()) {
            abort(403, 'Access denied. Super admin access required.');
        }

        // Default date range (last 30 days)
        $fromDate = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $leadSourceId = $request->get('lead_source_id', '');
        
        // Get all lead statuses
        $leadStatuses = LeadStatus::select('id', 'title')->get();
        
        // Get all lead sources for filter
        $leadSources = \App\Models\LeadSource::select('id', 'title')->get();
        
        // Get stage movement report data
        $stageData = $this->getLeadStageMovementData($fromDate, $toDate, $leadSourceId);
        
        return view('admin.reports.lead-stage-movement', compact(
            'stageData', 'leadStatuses', 'leadSources', 'fromDate', 'toDate', 'leadSourceId'
        ));
    }

    /**
     * Get Lead Stage Movement Data
     */
    private function getLeadStageMovementData($fromDate, $toDate, $leadSourceId = '')
    {
        // Get leads that are stuck in early stages (not Follow-Up or Converted)
        // Include leads that were either created OR updated in the date range
        $query = Lead::select('leads.*', 'lead_statuses.title as status_name', 'users.name as telecaller_name', 'lead_sources.title as source_name')
            ->join('lead_statuses', 'leads.lead_status_id', '=', 'lead_statuses.id')
            ->leftJoin('users', 'leads.telecaller_id', '=', 'users.id')
            ->leftJoin('lead_sources', 'leads.lead_source_id', '=', 'lead_sources.id')
            ->where(function($q) use ($fromDate, $toDate) {
                $q->whereBetween('leads.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
                  ->orWhereBetween('leads.updated_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
            })
            ->whereNotIn('leads.lead_status_id', [2, 4]) // Not Follow-Up (2) or Converted (4)
            ->where('leads.is_converted', 0); // Not converted
        
        // Add source filter if provided
        if (!empty($leadSourceId)) {
            $query->where('leads.lead_source_id', $leadSourceId);
        }
        
        $stuckLeads = $query->get()
            ->map(function ($lead) {
                // Get last activity date for this lead
                $lastActivity = LeadActivity::where('lead_id', $lead->id)
                    ->where('activity_type', 'status_update')
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                $daysSinceLastUpdate = $lastActivity 
                    ? Carbon::parse($lastActivity->created_at)->diffInDays(now())
                    : Carbon::parse($lead->created_at)->diffInDays(now());
                
                // Round to whole days
                $daysSinceLastUpdate = (int) $daysSinceLastUpdate;
                
                $lead->days_since_last_update = $daysSinceLastUpdate;
                $lead->last_activity_date = $lastActivity ? $lastActivity->created_at : $lead->created_at;
                $lead->is_stuck = $daysSinceLastUpdate >= 5; // Stuck if no update for 5+ days
                
                return $lead;
            });

        // Group by status
        $statusGroups = $stuckLeads->groupBy('lead_status_id')->map(function ($leads, $statusId) {
            $status = LeadStatus::find($statusId);
            return [
                'status_id' => $statusId,
                'status_name' => $status ? $status->title : 'Unknown',
                'total_leads' => $leads->count(),
                'stuck_leads' => $leads->where('is_stuck', true)->count(),
                'leads' => $leads->sortByDesc('days_since_last_update')
            ];
        });

        // Get overall statistics (include both created and updated leads)
        $totalLeadsQuery = Lead::where(function($q) use ($fromDate, $toDate) {
            $q->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
              ->orWhereBetween('updated_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        });
        
        if (!empty($leadSourceId)) {
            $totalLeadsQuery->where('lead_source_id', $leadSourceId);
        }
        
        $totalLeads = $totalLeadsQuery->count();
        $totalStuckLeads = $stuckLeads->where('is_stuck', true)->count();
        
        $followUpQuery = Lead::where(function($q) use ($fromDate, $toDate) {
            $q->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
              ->orWhereBetween('updated_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        })->where('lead_status_id', 2);
        
        if (!empty($leadSourceId)) {
            $followUpQuery->where('lead_source_id', $leadSourceId);
        }
        
        $totalFollowUpLeads = $followUpQuery->count();
        
        $convertedQuery = Lead::where(function($q) use ($fromDate, $toDate) {
            $q->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
              ->orWhereBetween('updated_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        })->where('is_converted', 1);
        
        if (!empty($leadSourceId)) {
            $convertedQuery->where('lead_source_id', $leadSourceId);
        }
        
        $totalConvertedLeads = $convertedQuery->count();

        return [
            'status_groups' => $statusGroups,
            'summary' => [
                'total_leads' => $totalLeads,
                'total_stuck_leads' => $totalStuckLeads,
                'total_follow_up_leads' => $totalFollowUpLeads,
                'total_converted_leads' => $totalConvertedLeads,
                'stuck_percentage' => $totalLeads > 0 ? round(($totalStuckLeads / $totalLeads) * 100, 2) : 0,
                'follow_up_percentage' => $totalLeads > 0 ? round(($totalFollowUpLeads / $totalLeads) * 100, 2) : 0,
                'conversion_percentage' => $totalLeads > 0 ? round(($totalConvertedLeads / $totalLeads) * 100, 2) : 0,
            ]
        ];
    }

    /**
     * Export Lead Stage Movement Report to Excel
     */
    public function exportExcel(Request $request)
    {
        if (!RoleHelper::is_super_admin()) {
            abort(403, 'Access denied. Super admin access required.');
        }

        $fromDate = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $leadSourceId = $request->get('lead_source_id', '');
        
        $stageData = $this->getLeadStageMovementData($fromDate, $toDate, $leadSourceId);
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Lead Stage Movement Report');
        
        // Headers
        $headers = [
            'SL No', 'Lead ID', 'Lead Title', 'Phone', 'Source', 'Telecaller', 'Current Status', 'Days Since Last Update', 
            'Last Activity Date', 'Is Stuck', 'Created At'
        ];
        
        $sheet->fromArray($headers, null, 'A1');
        
        // Data
        $row = 2;
        $serialNumber = 1;
        foreach ($stageData['status_groups'] as $statusGroup) {
            foreach ($statusGroup['leads'] as $lead) {
                $sheet->setCellValue('A' . $row, $serialNumber++);
                $sheet->setCellValue('B' . $row, $lead->id);
                $sheet->setCellValue('C' . $row, $lead->title);
                $sheet->setCellValue('D' . $row, \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone));
                $sheet->setCellValue('E' . $row, $lead->source_name ?: 'Unknown');
                $sheet->setCellValue('F' . $row, $lead->telecaller_name ?: 'Unassigned');
                $sheet->setCellValue('G' . $row, $statusGroup['status_name']);
                $sheet->setCellValue('H' . $row, $lead->days_since_last_update);
                $sheet->setCellValue('I' . $row, $lead->last_activity_date->format('d-m-Y h:i A'));
                $sheet->setCellValue('J' . $row, $lead->is_stuck ? 'Yes' : 'No');
                $sheet->setCellValue('K' . $row, $lead->created_at->format('d-m-Y h:i A'));
                $row++;
            }
        }
        
        // Auto-size columns
        foreach (range('A', 'K') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        $writer = new Xlsx($spreadsheet);
        
        $filename = 'lead_stage_movement_' . $fromDate . '_to_' . $toDate . '.xlsx';
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    /**
     * Export Lead Stage Movement Report to PDF
     */
    public function exportPdf(Request $request)
    {
        if (!RoleHelper::is_super_admin()) {
            abort(403, 'Access denied. Super admin access required.');
        }

        $fromDate = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $leadSourceId = $request->get('lead_source_id', '');
        
        $stageData = $this->getLeadStageMovementData($fromDate, $toDate, $leadSourceId);
        
        $pdf = Pdf::loadView('admin.reports.pdf.lead-stage-movement', [
            'stageData' => $stageData,
            'fromDate' => $fromDate,
            'toDate' => $toDate
        ]);
        
        $filename = 'lead_stage_movement_' . $fromDate . '_to_' . $toDate . '.pdf';
        
        return $pdf->download($filename);
    }
}
