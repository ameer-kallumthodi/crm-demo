<?php

namespace App\Exports;

use App\Models\Lead;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LeadSourceReportExport
{
    protected $leads;
    protected $fromDate;
    protected $toDate;

    public function __construct($leads, $fromDate, $toDate)
    {
        $this->leads = $leads;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set title
        $sheet->setTitle('Lead Source Report');
        
        $row = 1;
        
        // Header
        $sheet->setCellValue('A' . $row, 'Lead Source Report');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(16);
        $sheet->mergeCells('A' . $row . ':H' . $row);
        $row += 2;
        
        $sheet->setCellValue('A' . $row, 'Report Period: ' . \Carbon\Carbon::parse($this->fromDate)->format('M d, Y') . ' to ' . \Carbon\Carbon::parse($this->toDate)->format('M d, Y'));
        $row += 3;
        
        // Headers
        $headers = ['S.No', 'Name', 'Phone', 'Email', 'Status', 'Source', 'Telecaller', 'Created Date'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $col++;
        }
        $row++;
        
        // Data
        $serialNumber = 1;
        foreach ($this->leads as $lead) {
            $sheet->setCellValue('A' . $row, $serialNumber);
            $sheet->setCellValue('B' . $row, $lead->title);
            $sheet->setCellValue('C' . $row, $lead->phone);
            $sheet->setCellValue('D' . $row, $lead->email ?? '-');
            $sheet->setCellValue('E' . $row, $lead->leadStatus->title ?? 'Unknown');
            $sheet->setCellValue('F' . $row, $lead->leadSource->title ?? 'Unknown');
            $sheet->setCellValue('G' . $row, $lead->telecaller->name ?? '-');
            $sheet->setCellValue('H' . $row, $lead->created_at->format('Y-m-d H:i:s'));
            $row++;
            $serialNumber++;
        }
        
        // Set column widths
        $columnWidths = [
            'A' => 10,
            'B' => 25,
            'C' => 15,
            'D' => 30,
            'E' => 15,
            'F' => 15,
            'G' => 20,
            'H' => 20,
        ];
        
        foreach ($columnWidths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
        
        return $spreadsheet;
    }
}