<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LeadsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithColumnFormatting
{
    protected $leads;

    /**
     * @param  \Illuminate\Support\Collection|array<int, \App\Models\Lead>  $leads
     * @param  array<int, string|null>  $sourceTitles  lead_source_id => title
     * @param  array<int, string|null>  $courseTitles  course_id => title
     */
    public function __construct($leads, protected array $sourceTitles = [], protected array $courseTitles = [])
    {
        $this->leads = $leads;
    }

    private function titleFromMapOrRelation(?int $id, array $titles, $relation): string
    {
        $fromMap = $this->lookupTitleMap($id, $titles);
        if ($fromMap !== null && $fromMap !== '') {
            return $this->excelText($fromMap);
        }

        if ($relation) {
            $label = trim((string) ($relation->title ?? ''));
            if ($label === '' && isset($relation->description)) {
                $label = trim((string) $relation->description);
            }
            if ($label === '' && isset($relation->code)) {
                $label = trim((string) $relation->code);
            }
            if ($label !== '') {
                return $this->excelText($label);
            }
        }

        return '-';
    }

    /**
     * @param  array<int|string, string|null>  $titles
     */
    private function lookupTitleMap(?int $id, array $titles): ?string
    {
        if ($id === null) {
            return null;
        }
        foreach ([$id, (string) $id] as $key) {
            if (! array_key_exists($key, $titles)) {
                continue;
            }
            $t = $titles[$key];
            if ($t === null) {
                continue;
            }
            $t = trim((string) $t);
            if ($t !== '') {
                return $t;
            }
        }

        return null;
    }

    /**
     * Prefix so Excel / PhpSpreadsheet keeps purely numeric labels as text (not number cells).
     */
    private function excelText(string $value): string
    {
        if ($value === '') {
            return '';
        }
        if (preg_match('/^\s*-?\d+(\.\d+)?\s*$/', $value)) {
            return "\u{200B}".$value;
        }

        return $value;
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
            'J' => NumberFormat::FORMAT_TEXT,
        ];
    }

    private function nullableId(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function formatPhoneForExport(?string $code, ?string $phone): string
    {
        if ($phone === null || $phone === '') {
            return '-';
        }

        return ($code !== null && $code !== '') ? ($code.$phone) : $phone;
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
            $this->formatPhoneForExport($lead->code ?? null, $lead->phone ?? null),
            $lead->email ?? '-',
            $lead->leadStatus ? ($lead->leadStatus->title ?? '-') : '-',
            $lead->interest_status ? ($lead->interest_status == 1 ? 'Hot' : ($lead->interest_status == 2 ? 'Warm' : 'Cold')) : 'Not Set',
            $lead->rating ? ($lead->rating . '/10') : 'Not Rated',
            $this->titleFromMapOrRelation(
                $this->nullableId($lead->lead_source_id),
                $this->sourceTitles,
                $lead->leadSource
            ),
            $this->titleFromMapOrRelation(
                $this->nullableId($lead->course_id),
                $this->courseTitles,
                $lead->course
            ),
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

