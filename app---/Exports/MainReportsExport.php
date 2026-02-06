<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MainReportsExport
{
    protected $reports;
    protected $fromDate;
    protected $toDate;

    public function __construct($reports, $fromDate, $toDate)
    {
        $this->reports = $reports;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set title
        $sheet->setTitle('Main Reports');
        
        $row = 1;
        
        // Header
        $sheet->setCellValue('A' . $row, 'Main Reports');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(16);
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $row += 2;
        
        $sheet->setCellValue('A' . $row, 'Report Period: ' . \Carbon\Carbon::parse($this->fromDate)->format('M d, Y') . ' to ' . \Carbon\Carbon::parse($this->toDate)->format('M d, Y'));
        $row += 2;
        
        // Summary Section
        $sheet->setCellValue('A' . $row, 'Report Summary');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $row += 2;
        
        $summaryData = [
            ['Total Leads', $this->reports['lead_status']->sum('count')],
            ['Converted Leads', $this->reports['lead_status']->where('title', 'Converted')->first()->count ?? 0],
            ['Lead Sources', $this->reports['lead_source']->count()],
            ['Active Teams', $this->reports['team']->count()],
            ['Active Telecallers', $this->reports['telecaller']->count()],
        ];
        
        foreach ($summaryData as $data) {
            $sheet->setCellValue('A' . $row, $data[0]);
            $sheet->setCellValue('B' . $row, $data[1]);
            $row++;
        }
        
        $row += 2;
        
        // Lead Status Report
        $this->addReportSection($sheet, $row, 'Lead Status Report', $this->reports['lead_status'], ['Status', 'Count', 'Percentage']);
        $row += $this->reports['lead_status']->count() + 4;
        
        // Lead Source Report
        $this->addReportSection($sheet, $row, 'Lead Source Report', $this->reports['lead_source'], ['Source', 'Count', 'Percentage']);
        $row += $this->reports['lead_source']->count() + 4;
        
        // Team Report
        $this->addReportSection($sheet, $row, 'Team Report', $this->reports['team'], ['Team', 'Count', 'Percentage']);
        $row += $this->reports['team']->count() + 4;
        
        // Telecaller Report
        $this->addTelecallerReportSection($sheet, $row, 'Telecaller Report');
        
        // Auto-size columns
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        return $spreadsheet;
    }
    
    private function addReportSection($sheet, $startRow, $title, $data, $headers)
    {
        $row = $startRow;
        
        // Title
        $sheet->setCellValue('A' . $row, $title);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $row += 2;
        
        // Headers
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $col++;
        }
        $row++;
        
        // Data
        $total = $data->sum('count');
        foreach ($data as $item) {
            $percentage = $total > 0 ? round(($item->count / $total) * 100, 1) : 0;
            $sheet->setCellValue('A' . $row, $item->title);
            $sheet->setCellValue('B' . $row, $item->count);
            $sheet->setCellValue('C' . $row, $percentage . '%');
            $row++;
        }
    }
    
    private function addTelecallerReportSection($sheet, $startRow, $title)
    {
        $row = $startRow;
        
        // Title
        $sheet->setCellValue('A' . $row, $title);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $row += 2;
        
        // Headers
        $headers = ['S.No', 'Telecaller', 'Phone', 'Team', 'Count', 'Percentage'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $col++;
        }
        $row++;
        
        // Data
        $total = $this->reports['telecaller']->sum('count');
        $serialNumber = 1;
        foreach ($this->reports['telecaller'] as $telecaller) {
            $percentage = $total > 0 ? round(($telecaller->count / $total) * 100, 1) : 0;
            $sheet->setCellValue('A' . $row, $serialNumber);
            $sheet->setCellValue('B' . $row, $telecaller->name);
            $sheet->setCellValue('C' . $row, $telecaller->phone ?? 'N/A');
            $sheet->setCellValue('D' . $row, $telecaller->team_name ?? 'No Team');
            $sheet->setCellValue('E' . $row, $telecaller->count);
            $sheet->setCellValue('F' . $row, $percentage . '%');
            $row++;
            $serialNumber++;
        }
    }
}
