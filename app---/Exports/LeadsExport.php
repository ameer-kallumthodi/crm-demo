<?php

namespace App\Exports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LeadsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $leads;

    public function __construct($leads)
    {
        $this->leads = $leads;
    }

    public function collection()
    {
        return $this->leads;
    }

    public function headings(): array
    {
        return [
            'S.No',
            'Created At',
            'Name',
            'Phone',
            'Email',
            'Status',
            'Interest',
            'Rating',
            'Source',
            'Course',
            'Telecaller',
            'Place',
            'Followup Date',
            'Remarks',
            'Date',
            'Time'
        ];
    }

    public function map($lead): array
    {
        static $serialNumber = 0;
        $serialNumber++;
        
        return [
            $serialNumber,
            $lead->created_at ? $lead->created_at->format('d-m-Y h:i A') : '-',
            $lead->title ?? '-',
            $lead->code && $lead->phone ? ($lead->code . $lead->phone) : ($lead->phone ?? '-'),
            $lead->email ?? '-',
            $lead->leadStatus ? ($lead->leadStatus->title ?? '-') : '-',
            $lead->interest_status ? ($lead->interest_status == 1 ? 'Hot' : ($lead->interest_status == 2 ? 'Warm' : 'Cold')) : 'Not Set',
            $lead->rating ? ($lead->rating . '/10') : 'Not Rated',
            $lead->leadSource ? ($lead->leadSource->title ?? '-') : '-',
            $lead->course ? ($lead->course->title ?? '-') : '-',
            $lead->telecaller ? ($lead->telecaller->name ?? 'Unassigned') : 'Unassigned',
            $lead->place ?? '-',
            $lead->followup_date ? $lead->followup_date->format('M d, Y') : '-',
            $lead->remarks ?? '-',
            $lead->created_at ? $lead->created_at->format('M d, Y') : '-',
            $lead->created_at ? $lead->created_at->format('h:i A') : '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold with background color
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,  // S.No
            'B' => 18, // Created At
            'C' => 25, // Name
            'D' => 15, // Phone
            'E' => 25, // Email
            'F' => 15, // Status
            'G' => 12, // Interest
            'H' => 10, // Rating
            'I' => 20, // Source
            'J' => 25, // Course
            'K' => 20, // Telecaller
            'L' => 15, // Place
            'M' => 15, // Followup Date
            'N' => 30, // Remarks
            'O' => 15, // Date
            'P' => 12, // Time
        ];
    }
}

