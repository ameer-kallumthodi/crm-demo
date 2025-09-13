<?php

namespace App\Exports;

use App\Models\Lead;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class LeadSourceReportPdf
{
    protected $leads;
    protected $fromDate;
    protected $toDate;
    protected $reportType;

    public function __construct($leads, $fromDate, $toDate, $reportType = 'Lead Source')
    {
        $this->leads = $leads;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->reportType = $reportType;
    }

    public function download()
    {
        $data = [
            'leads' => $this->leads,
            'fromDate' => $this->fromDate,
            'toDate' => $this->toDate,
            'reportType' => $this->reportType,
            'generatedAt' => now()->format('Y-m-d H:i:s')
        ];

        $pdf = Pdf::loadView('admin.reports.exports.lead-source-pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        
        $filename = strtolower(str_replace(' ', '_', $this->reportType)) . '_report_' . $this->fromDate . '_to_' . $this->toDate . '.pdf';
        
        return $pdf->download($filename);
    }
}
