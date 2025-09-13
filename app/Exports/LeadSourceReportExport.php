<?php

namespace App\Exports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LeadSourceReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
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

    public function collection()
    {
        return $this->leads;
    }

    public function headings(): array
    {
        return [
            'S.No',
            'Name',
            'Phone',
            'Email',
            'Status',
            'Source',
            'Telecaller',
            'Created Date'
        ];
    }

    public function map($lead): array
    {
        return [
            $lead->id,
            $lead->title,
            $lead->phone,
            $lead->email ?? '-',
            $lead->leadStatus->title ?? 'Unknown',
            $lead->leadSource->title ?? 'Unknown',
            $lead->telecaller->name ?? '-',
            $lead->created_at->format('Y-m-d H:i:s')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 25,
            'C' => 15,
            'D' => 30,
            'E' => 15,
            'F' => 15,
            'G' => 20,
            'H' => 20,
        ];
    }
}
