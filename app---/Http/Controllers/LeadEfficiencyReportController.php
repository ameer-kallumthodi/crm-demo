<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Helpers\AuthHelper;
use App\Helpers\RoleHelper;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;

class LeadEfficiencyReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth');
    }

    /**
     * Lead Source Efficiency Report
     * Shows which marketing or referral source brings in the highest converting leads
     */
    public function index(Request $request)
    {
        // Check if user is super admin or auditor
        if (!RoleHelper::is_super_admin() && !RoleHelper::is_auditor()) {
            abort(403, 'Access denied. Super admin or auditor access required.');
        }

        // Default date range (last 30 days)
        $fromDate = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        // Get all lead sources
        $leadSources = LeadSource::select('id', 'title')->get();
        
        // Get efficiency report data
        $efficiencyData = $this->getLeadSourceEfficiencyData($fromDate, $toDate);
        
        return view('admin.reports.lead-efficiency', compact(
            'efficiencyData', 'leadSources', 'fromDate', 'toDate'
        ));
    }

    /**
     * Get Lead Source Efficiency Data
     */
    private function getLeadSourceEfficiencyData($fromDate, $toDate)
    {
        $data = Lead::select('lead_sources.id', 'lead_sources.title as source_name')
            ->join('lead_sources', 'leads.lead_source_id', '=', 'lead_sources.id')
            ->whereBetween('leads.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->selectRaw('
                lead_sources.id,
                lead_sources.title as source_name,
                COUNT(leads.id) as total_leads,
                COUNT(CASE WHEN leads.is_converted = 1 THEN 1 END) as converted_leads,
                COUNT(CASE WHEN leads.lead_status_id = 2 THEN 1 END) as follow_up_leads,
                COUNT(CASE WHEN leads.lead_status_id = 3 THEN 1 END) as interested_leads,
                COUNT(CASE WHEN leads.lead_status_id = 4 THEN 1 END) as converted_status_leads,
                ROUND(
                    (COUNT(CASE WHEN leads.is_converted = 1 THEN 1 END) * 100.0 / COUNT(leads.id)), 2
                ) as conversion_rate,
                ROUND(
                    (COUNT(CASE WHEN leads.lead_status_id = 2 THEN 1 END) * 100.0 / COUNT(leads.id)), 2
                ) as follow_up_rate,
                ROUND(
                    (COUNT(CASE WHEN leads.lead_status_id = 3 THEN 1 END) * 100.0 / COUNT(leads.id)), 2
                ) as interested_rate
            ')
            ->groupBy('lead_sources.id', 'lead_sources.title')
            ->orderBy('conversion_rate', 'desc')
            ->orderBy('total_leads', 'desc')
            ->get();

        return $data;
    }

    /**
     * Export Lead Source Efficiency Report to Excel
     */
    public function exportExcel(Request $request)
    {
        if (!RoleHelper::is_super_admin() && !RoleHelper::is_auditor()) {
            abort(403, 'Access denied. Super admin or auditor access required.');
        }

        $fromDate = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        $efficiencyData = $this->getLeadSourceEfficiencyData($fromDate, $toDate);
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Lead Source Efficiency Report');
        
        // Headers
        $headers = [
            'Lead Source', 'Total Leads', 'Converted Leads', 'Follow-up Leads', 
            'Interested Leads', 'Converted Status Leads', 'Conversion Rate (%)', 
            'Follow-up Rate (%)', 'Interested Rate (%)'
        ];
        
        $sheet->fromArray($headers, null, 'A1');
        
        // Data
        $row = 2;
        foreach ($efficiencyData as $item) {
            $sheet->setCellValue('A' . $row, $item->source_name);
            $sheet->setCellValue('B' . $row, $item->total_leads);
            $sheet->setCellValue('C' . $row, $item->converted_leads);
            $sheet->setCellValue('D' . $row, $item->follow_up_leads);
            $sheet->setCellValue('E' . $row, $item->interested_leads);
            $sheet->setCellValue('F' . $row, $item->converted_status_leads);
            $sheet->setCellValue('G' . $row, $item->conversion_rate);
            $sheet->setCellValue('H' . $row, $item->follow_up_rate);
            $sheet->setCellValue('I' . $row, $item->interested_rate);
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        $writer = new Xlsx($spreadsheet);
        
        $filename = 'lead_source_efficiency_' . $fromDate . '_to_' . $toDate . '.xlsx';
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    /**
     * Export Lead Source Efficiency Report to PDF
     */
    public function exportPdf(Request $request)
    {
        if (!RoleHelper::is_super_admin() && !RoleHelper::is_auditor()) {
            abort(403, 'Access denied. Super admin or auditor access required.');
        }

        $fromDate = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        $efficiencyData = $this->getLeadSourceEfficiencyData($fromDate, $toDate);
        
        $pdf = Pdf::loadView('admin.reports.pdf.lead-efficiency', [
            'efficiencyData' => $efficiencyData,
            'fromDate' => $fromDate,
            'toDate' => $toDate
        ]);
        
        $filename = 'lead_source_efficiency_' . $fromDate . '_to_' . $toDate . '.pdf';
        
        return $pdf->download($filename);
    }
}
