<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TelecallerReportExport
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
        $sheet->setTitle('Telecaller Report');
        
        $row = 1;
        
        // Header
        $sheet->setCellValue('A' . $row, 'Telecaller Report');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(16);
        $sheet->mergeCells('A' . $row . ':E' . $row);
        $row += 2;
        
        $sheet->setCellValue('A' . $row, 'Report Period: ' . \Carbon\Carbon::parse($this->fromDate)->format('M d, Y') . ' to ' . \Carbon\Carbon::parse($this->toDate)->format('M d, Y'));
        $row += 3;
        
        // Headers
        $headers = ['S.No', 'Telecaller Name', 'Phone', 'Team Name', 'Total Leads', 'Percentage'];
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
        
        // Auto-size columns
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        return $spreadsheet;
    }
}
