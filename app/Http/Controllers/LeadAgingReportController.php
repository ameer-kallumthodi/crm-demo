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

class LeadAgingReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth');
    }

    /**
     * Lead Aging Report
     * Shows how long leads are staying idle in each stage
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
        $leadStatusId = $request->get('lead_status_id', '');
        
        // Get all lead statuses for filter
        $leadStatuses = LeadStatus::select('id', 'title')->get();
        
        // Get all lead sources for filter
        $leadSources = \App\Models\LeadSource::select('id', 'title')->get();
        
        // Get aging report data
        $agingData = $this->getLeadAgingData($fromDate, $toDate, $leadSourceId, $leadStatusId);
        
        return view('admin.reports.lead-aging', compact(
            'agingData', 'leadStatuses', 'leadSources', 'fromDate', 'toDate', 'leadSourceId', 'leadStatusId'
        ));
    }

    /**
     * Get Lead Aging Data
     */
    private function getLeadAgingData($fromDate, $toDate, $leadSourceId = '', $leadStatusId = '')
    {
        // Get non-converted leads in the date range with filters
        $query = Lead::select('leads.*', 'lead_statuses.title as status_name', 'users.name as telecaller_name', 'lead_sources.title as source_name')
            ->join('lead_statuses', 'leads.lead_status_id', '=', 'lead_statuses.id')
            ->leftJoin('users', 'leads.telecaller_id', '=', 'users.id')
            ->leftJoin('lead_sources', 'leads.lead_source_id', '=', 'lead_sources.id')
            ->whereBetween('leads.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->where('leads.is_converted', 0); // Only non-converted leads
        
        // Apply source filter
        if (!empty($leadSourceId)) {
            $query->where('leads.lead_source_id', $leadSourceId);
        }
        
        // Apply status filter
        if (!empty($leadStatusId)) {
            $query->where('leads.lead_status_id', $leadStatusId);
        }
        
        $leads = $query->get()
            ->map(function ($lead) {
                // Get the last status update activity to determine when lead entered current status
                $lastStatusUpdate = LeadActivity::where('lead_id', $lead->id)
                    ->where('activity_type', 'status_update')
                    ->where('lead_status_id', $lead->lead_status_id)
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                // Calculate days in current status based on when lead entered this status
                $statusEntryDate = $lastStatusUpdate ? $lastStatusUpdate->created_at : $lead->created_at;
                $daysInCurrentStatus = Carbon::parse($statusEntryDate)->diffInDays(now());
                
                // Get last activity date (any activity, not just status updates)
                $lastActivity = LeadActivity::where('lead_id', $lead->id)
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                $lastActivityDate = $lastActivity ? $lastActivity->created_at : $lead->created_at;
                $daysSinceLastActivity = Carbon::parse($lastActivityDate)->diffInDays(now());
                
                // Round to whole days
                $daysInCurrentStatus = (int) $daysInCurrentStatus;
                $daysSinceLastActivity = (int) $daysSinceLastActivity;
                
                $lead->days_in_current_status = $daysInCurrentStatus;
                $lead->days_since_last_activity = $daysSinceLastActivity;
                $lead->first_activity_in_status = $statusEntryDate; // When lead entered current status
                $lead->last_activity_date = Carbon::parse($lastActivityDate); // Convert to Carbon instance
                
                // Categorize aging based on idle time in current status
                if ($daysInCurrentStatus <= 1) {
                    $lead->aging_category = 'Active (0-1 days)';
                } elseif ($daysInCurrentStatus <= 3) {
                    $lead->aging_category = 'Recent (2-3 days)';
                } elseif ($daysInCurrentStatus <= 7) {
                    $lead->aging_category = 'Idle (4-7 days)';
                } elseif ($daysInCurrentStatus <= 14) {
                    $lead->aging_category = 'Stale (8-14 days)';
                } else {
                    $lead->aging_category = 'Abandoned (15+ days)';
                }
                
                return $lead;
            });

        // Group by status
        $statusGroups = $leads->groupBy('lead_status_id')->map(function ($leads, $statusId) {
            $status = LeadStatus::find($statusId);
            $leadsArray = $leads->toArray();
            
            return [
                'status_id' => $statusId,
                'status_name' => $status ? $status->title : 'Unknown',
                'total_leads' => $leads->count(),
                'avg_days_in_status' => round($leads->avg('days_in_current_status'), 1),
                'max_days_in_status' => $leads->max('days_in_current_status'),
                'min_days_in_status' => $leads->min('days_in_current_status'),
                'aging_breakdown' => [
                    'fresh' => $leads->where('aging_category', 'Fresh (0-1 days)')->count(),
                    'recent' => $leads->where('aging_category', 'Recent (2-3 days)')->count(),
                    'moderate' => $leads->where('aging_category', 'Moderate (4-7 days)')->count(),
                    'old' => $leads->where('aging_category', 'Old (8-14 days)')->count(),
                    'very_old' => $leads->where('aging_category', 'Very Old (15+ days)')->count(),
                ],
                'leads' => $leads->sortByDesc('days_in_current_status')
            ];
        });

        // Get overall statistics
        $totalLeads = $leads->count();
        $avgDaysInStatus = $leads->avg('days_in_current_status');
        $maxDaysInStatus = $leads->max('days_in_current_status');
        $leadsOver7Days = $leads->where('days_in_current_status', '>', 7)->count();
        $leadsOver14Days = $leads->where('days_in_current_status', '>', 14)->count();

        // Get converted leads data
        $convertedLeads = $this->getConvertedLeadsData($fromDate, $toDate, $leadSourceId, $leadStatusId);

        return [
            'status_groups' => $statusGroups,
            'converted_leads' => $convertedLeads,
            'summary' => [
                'total_leads' => $totalLeads,
                'avg_days_in_status' => round($avgDaysInStatus, 1),
                'max_days_in_status' => $maxDaysInStatus,
                'leads_over_7_days' => $leadsOver7Days,
                'leads_over_14_days' => $leadsOver14Days,
                'over_7_days_percentage' => $totalLeads > 0 ? round(($leadsOver7Days / $totalLeads) * 100, 2) : 0,
                'over_14_days_percentage' => $totalLeads > 0 ? round(($leadsOver14Days / $totalLeads) * 100, 2) : 0,
            ]
        ];
    }

    /**
     * Get Converted Leads Data
     */
    private function getConvertedLeadsData($fromDate, $toDate, $leadSourceId = '', $leadStatusId = '')
    {
        // Get converted leads with converted_leads table join
        $query = Lead::select('leads.*', 'leads.created_at as lead_created_at', 'lead_statuses.title as status_name', 'users.name as telecaller_name', 'lead_sources.title as source_name', 'converted_leads.created_at as conversion_date')
            ->join('lead_statuses', 'leads.lead_status_id', '=', 'lead_statuses.id')
            ->leftJoin('users', 'leads.telecaller_id', '=', 'users.id')
            ->leftJoin('lead_sources', 'leads.lead_source_id', '=', 'lead_sources.id')
            ->join('converted_leads', 'leads.id', '=', 'converted_leads.lead_id')
            ->whereBetween('leads.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->where('leads.is_converted', 1); // Only converted leads
        
        // Apply source filter
        if (!empty($leadSourceId)) {
            $query->where('leads.lead_source_id', $leadSourceId);
        }
        
        // Apply status filter
        if (!empty($leadStatusId)) {
            $query->where('leads.lead_status_id', $leadStatusId);
        }
        
        $convertedLeads = $query->get()
            ->map(function ($lead) {
                // Calculate days from creation to conversion
                $leadCreatedAt = $lead->lead_created_at; // From leads table
                $conversionDate = $lead->conversion_date; // From converted_leads table
                
                // Calculate days between lead creation and conversion
                $daysToConversion = Carbon::parse($leadCreatedAt)->diffInDays(Carbon::parse($conversionDate));
                
                // Calculate days since lead creation (similar to non-converted leads)
                $daysSinceCreation = Carbon::parse($leadCreatedAt)->diffInDays(now());
                
                // Round to whole days
                $daysToConversion = (int) $daysToConversion;
                $daysSinceCreation = (int) $daysSinceCreation;
                
                $lead->days_to_conversion = $daysToConversion;
                $lead->days_since_creation = $daysSinceCreation;
                $lead->conversion_date = Carbon::parse($conversionDate); // Convert to Carbon instance
                $lead->created_at = Carbon::parse($leadCreatedAt); // Convert to Carbon instance
                
                return $lead;
            });

        return $convertedLeads;
    }

    /**
     * Export Lead Aging Report to Excel
     */
    public function exportExcel(Request $request)
    {
        if (!RoleHelper::is_super_admin()) {
            abort(403, 'Access denied. Super admin access required.');
        }

        $fromDate = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $leadSourceId = $request->get('lead_source_id', '');
        $leadStatusId = $request->get('lead_status_id', '');
        
        $agingData = $this->getLeadAgingData($fromDate, $toDate, $leadSourceId, $leadStatusId);
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Lead Aging Report');
        
        // Headers
        $headers = [
            'SL No', 'Lead Title', 'Phone', 'Source', 'Telecaller', 'Current Status', 
            'Days In Current Status', 'Aging Category', 'First Activity In Status', 'Last Activity Date', 'Created At'
        ];
        
        $sheet->fromArray($headers, null, 'A1');
        
        // Data
        $row = 2;
        $serialNumber = 1;
        foreach ($agingData['status_groups'] as $statusGroup) {
            foreach ($statusGroup['leads'] as $lead) {
                $sheet->setCellValue('A' . $row, $serialNumber++);
                $sheet->setCellValue('B' . $row, $lead->title);
                $sheet->setCellValue('C' . $row, \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone));
                $sheet->setCellValue('D' . $row, $lead->source_name ?: 'Unknown');
                $sheet->setCellValue('E' . $row, $lead->telecaller_name ?: 'Unassigned');
                $sheet->setCellValue('F' . $row, $lead->status_name);
                $sheet->setCellValue('G' . $row, $lead->days_in_current_status);
                $sheet->setCellValue('H' . $row, $lead->aging_category);
                $sheet->setCellValue('I' . $row, $lead->first_activity_in_status->format('d-m-Y h:i A'));
                $sheet->setCellValue('J' . $row, $lead->last_activity_date->format('d-m-Y h:i A'));
                $sheet->setCellValue('K' . $row, $lead->created_at->format('d-m-Y h:i A'));
                $row++;
            }
        }
        
        // Auto-size columns
        foreach (range('A', 'K') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        $writer = new Xlsx($spreadsheet);
        
        $filename = 'lead_aging_' . $fromDate . '_to_' . $toDate . '.xlsx';
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    /**
     * Show detailed lead activity report
     */
    public function leadDetail($leadId)
    {
        if (!RoleHelper::is_super_admin()) {
            abort(403, 'Access denied. Super admin access required.');
        }

        // Get lead details
        $lead = Lead::with(['leadStatus', 'leadSource', 'telecaller'])
            ->where('id', $leadId)
            ->firstOrFail();

        // Get all status update activities for this lead
        $activities = LeadActivity::where('lead_id', $leadId)
            ->where('activity_type', 'status_update')
            ->whereNotNull('lead_status_id')
            ->with('leadStatus')
            ->orderBy('created_at', 'asc')
            ->get();

        // Check if lead is converted
        $isConverted = $lead->is_converted;
        $conversionDate = null;
        
        if ($isConverted) {
            // Get conversion date from converted_leads table
            $convertedLead = \DB::table('converted_leads')
                ->where('lead_id', $leadId)
                ->first();
            $conversionDate = $convertedLead ? $convertedLead->created_at : null;
        }

        // Calculate days spent in each status
        $statusHistory = [];
        $previousDate = $lead->created_at;
        
        foreach ($activities as $index => $activity) {
            $statusEntryDate = $activity->created_at;
            
            // For converted leads, don't process activities after conversion date
            if ($isConverted && $conversionDate && Carbon::parse($statusEntryDate)->gt(Carbon::parse($conversionDate))) {
                break;
            }
            
            $daysInStatus = Carbon::parse($previousDate)->startOfDay()->diffInDays(Carbon::parse($statusEntryDate)->startOfDay());
            
            // Add the previous status period
            if ($index > 0) {
                $statusHistory[] = [
                    'status_name' => $activities[$index - 1]->leadStatus->title ?? 'Unknown',
                    'status_id' => $activities[$index - 1]->lead_status_id,
                    'entry_date' => $previousDate,
                    'exit_date' => $statusEntryDate,
                    'days_in_status' => (int) $daysInStatus,
                    'description' => $activities[$index - 1]->description,
                    'remarks' => $activities[$index - 1]->remarks,
                ];
            }
            
            $previousDate = $statusEntryDate;
        }
        
        // Add final status period
        if ($isConverted && $conversionDate) {
            // For converted leads, show the last status until conversion
            $finalDays = Carbon::parse($previousDate)->startOfDay()->diffInDays(Carbon::parse($conversionDate)->startOfDay());
            $statusHistory[] = [
                'status_name' => $lead->leadStatus->title ?? 'Unknown',
                'status_id' => $lead->lead_status_id,
                'entry_date' => $previousDate,
                'exit_date' => $conversionDate,
                'days_in_status' => (int) $finalDays,
                'description' => 'Status before conversion',
                'remarks' => null,
            ];
            
            // Add conversion status
            $conversionDays = Carbon::parse($conversionDate)->startOfDay()->diffInDays(now()->startOfDay());
            $statusHistory[] = [
                'status_name' => 'Converted',
                'status_id' => null, // No specific status ID for converted
                'entry_date' => $conversionDate,
                'exit_date' => null, // Current status
                'days_in_status' => (int) $conversionDays,
                'description' => 'Lead Converted',
                'remarks' => null,
            ];
            
            $totalDays = (int) Carbon::parse($lead->created_at)->startOfDay()->diffInDays(now()->startOfDay());
        } else {
            // For non-converted leads, show current status
            $currentDays = Carbon::parse($previousDate)->startOfDay()->diffInDays(now()->startOfDay());
            $statusHistory[] = [
                'status_name' => $lead->leadStatus->title ?? 'Unknown',
                'status_id' => $lead->lead_status_id,
                'entry_date' => $previousDate,
                'exit_date' => null, // Current status, no exit date
                'days_in_status' => (int) $currentDays,
                'description' => 'Current Status',
                'remarks' => null,
            ];
            
            $totalDays = (int) Carbon::parse($lead->created_at)->startOfDay()->diffInDays(now()->startOfDay());
        }

        $averageDaysPerStatus = count($statusHistory) > 0 ? round($totalDays / count($statusHistory), 1) : 0;

        return view('admin.reports.lead-detail', compact('lead', 'statusHistory', 'totalDays', 'averageDaysPerStatus'));
    }

    /**
     * Export Lead Aging Report to PDF
     */
    public function exportPdf(Request $request)
    {
        if (!RoleHelper::is_super_admin()) {
            abort(403, 'Access denied. Super admin access required.');
        }

        $fromDate = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $leadSourceId = $request->get('lead_source_id', '');
        $leadStatusId = $request->get('lead_status_id', '');
        
        $agingData = $this->getLeadAgingData($fromDate, $toDate, $leadSourceId, $leadStatusId);
        
        $pdf = Pdf::loadView('admin.reports.pdf.lead-aging', [
            'agingData' => $agingData,
            'fromDate' => $fromDate,
            'toDate' => $toDate
        ]);
        
        $filename = 'lead_aging_' . $fromDate . '_to_' . $toDate . '.pdf';
        
        return $pdf->download($filename);
    }
}
