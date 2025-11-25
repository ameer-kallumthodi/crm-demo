<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MarketingLeadsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected Collection $marketingLeads;

    public function __construct(Collection $marketingLeads)
    {
        $this->marketingLeads = $marketingLeads;
    }

    public function collection(): Collection
    {
        return $this->marketingLeads;
    }

    public function headings(): array
    {
        return [
            'S.No',
            'Date of Visit',
            'BDE Name',
            'Lead Name',
            'Phone',
            'WhatsApp',
            'Address',
            'Location',
            'House Number',
            'Lead Type',
            'Interested Courses',
            'Marketing Remarks',
            'Telecaller Remarks',
            'Lead Status',
            'Converted',
            'Telecaller Name',
            'Assignment Status',
            'Assigned At',
            'Created At',
        ];
    }

    public function map($lead): array
    {
        static $serialNumber = 0;
        $serialNumber++;

        $relatedLead = $lead->lead;

        $telecallerName = $relatedLead && $relatedLead->telecaller
            ? ($relatedLead->telecaller->name ?? '-')
            : '-';

        $telecallerRemarks = $relatedLead && $relatedLead->remarks
            ? $relatedLead->remarks
            : '-';

        $leadStatus = $relatedLead && $relatedLead->leadStatus
            ? ($relatedLead->leadStatus->title ?? '-')
            : '-';

        $converted = $relatedLead && ($relatedLead->is_converted || $relatedLead->convertedLead)
            ? 'Yes'
            : 'No';

        $assignmentStatus = $lead->is_telecaller_assigned ? 'Assigned' : 'Not Assigned';

        $interestedCourses = $lead->interested_courses;
        if (is_array($interestedCourses) && count($interestedCourses) > 0) {
            $interestedCourses = implode(', ', $interestedCourses);
        } else {
            $interestedCourses = '-';
        }

        $marketingRemarks = $lead->remarks ?: '-';

        return [
            $serialNumber,
            $lead->date_of_visit ? $lead->date_of_visit->format('d-m-Y') : '-',
            $lead->marketingBde ? ($lead->marketingBde->name ?? '-') : '-',
            $lead->lead_name ?? '-',
            $lead->code && $lead->phone ? ($lead->code . ' ' . $lead->phone) : ($lead->phone ?? '-'),
            $lead->whatsapp ? (($lead->whatsapp_code ? $lead->whatsapp_code . ' ' : '') . $lead->whatsapp) : '-',
            $lead->address ?: '-',
            $lead->location ?: '-',
            $lead->house_number ?: '-',
            $lead->lead_type ?: '-',
            $interestedCourses,
            $marketingRemarks,
            $telecallerRemarks,
            $leadStatus,
            $converted,
            $telecallerName,
            $assignmentStatus,
            $lead->assigned_at ? $lead->assigned_at->format('d-m-Y h:i A') : '-',
            $lead->created_at ? $lead->created_at->format('d-m-Y h:i A') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2A71D0']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 14,
            'C' => 25,
            'D' => 25,
            'E' => 18,
            'F' => 18,
            'G' => 35,
            'H' => 20,
            'I' => 15,
            'J' => 18,
            'K' => 30,
            'L' => 35,
            'M' => 35,
            'N' => 20,
            'O' => 10,
            'P' => 22,
            'Q' => 15,
            'R' => 20,
            'S' => 20,
        ];
    }
}

