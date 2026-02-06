<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TeamWiseReportExport implements FromArray, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $reportData;
    protected $fromDate;
    protected $toDate;

    public function __construct($reportData, $fromDate, $toDate)
    {
        $this->reportData = $reportData;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function array(): array
    {
        $data = [];
        
        foreach ($this->reportData as $teamData) {
            $team = $teamData['team'];
            
            // Team summary row
            $data[] = [
                'type' => 'Team Summary',
                'team_name' => $team->name,
                'team_lead' => $team->teamLead ? $team->teamLead->name : 'N/A',
                'total_members' => $team->total_members,
                'active_members' => $team->active_members,
                'total_leads' => $teamData['total_leads'],
                'converted_leads' => $teamData['converted_leads'],
                'conversion_rate' => $teamData['conversion_rate'] . '%',
                'telecaller_name' => '',
                'experience_level' => '',
                'total_calls' => '',
                'avg_call_duration' => '',
            ];
            
            // Individual telecaller performance
            foreach ($teamData['telecaller_performance'] as $perf) {
                $data[] = [
                    'type' => 'Telecaller',
                    'team_name' => '',
                    'team_lead' => '',
                    'total_members' => '',
                    'active_members' => '',
                    'total_leads' => $perf['total_leads'],
                    'converted_leads' => $perf['converted_leads'],
                    'conversion_rate' => $perf['conversion_rate'] . '%',
                    'telecaller_name' => $perf['user']->name,
                    'experience_level' => $perf['experience_level'],
                    'total_calls' => $perf['total_calls'],
                    'avg_call_duration' => $perf['avg_call_duration'] . ' min',
                ];
            }
            
            // Top performers
            if (!empty($teamData['segments']['top_performers'])) {
                $data[] = [
                    'type' => 'Top Performers',
                    'team_name' => '',
                    'team_lead' => '',
                    'total_members' => count($teamData['segments']['top_performers']),
                    'active_members' => '',
                    'total_leads' => collect($teamData['segments']['top_performers'])->sum('total_leads'),
                    'converted_leads' => collect($teamData['segments']['top_performers'])->sum('converted_leads'),
                    'conversion_rate' => '',
                    'telecaller_name' => '',
                    'experience_level' => '',
                    'total_calls' => '',
                    'avg_call_duration' => '',
                ];
            }
            
            // New joiners
            if (!empty($teamData['segments']['new_joiners'])) {
                $data[] = [
                    'type' => 'New Joiners',
                    'team_name' => '',
                    'team_lead' => '',
                    'total_members' => count($teamData['segments']['new_joiners']),
                    'active_members' => '',
                    'total_leads' => collect($teamData['segments']['new_joiners'])->sum('total_leads'),
                    'converted_leads' => collect($teamData['segments']['new_joiners'])->sum('converted_leads'),
                    'conversion_rate' => '',
                    'telecaller_name' => '',
                    'experience_level' => '',
                    'total_calls' => '',
                    'avg_call_duration' => '',
                ];
            }
            
            // Time analysis
            $data[] = [
                'type' => 'Morning Performance',
                'team_name' => '',
                'team_lead' => '',
                'total_members' => '',
                'active_members' => '',
                'total_leads' => $teamData['time_analysis']['morning']['leads'] ?? 0,
                'converted_leads' => $teamData['time_analysis']['morning']['conversions'] ?? 0,
                'conversion_rate' => ($teamData['time_analysis']['morning']['conversion_rate'] ?? 0) . '%',
                'telecaller_name' => '',
                'experience_level' => '',
                'total_calls' => '',
                'avg_call_duration' => '',
            ];
            
            $data[] = [
                'type' => 'Evening Performance',
                'team_name' => '',
                'team_lead' => '',
                'total_members' => '',
                'active_members' => '',
                'total_leads' => $teamData['time_analysis']['evening']['leads'] ?? 0,
                'converted_leads' => $teamData['time_analysis']['evening']['conversions'] ?? 0,
                'conversion_rate' => ($teamData['time_analysis']['evening']['conversion_rate'] ?? 0) . '%',
                'telecaller_name' => '',
                'experience_level' => '',
                'total_calls' => '',
                'avg_call_duration' => '',
            ];
            
            // Add empty row for separation
            $data[] = [
                'type' => '',
                'team_name' => '',
                'team_lead' => '',
                'total_members' => '',
                'active_members' => '',
                'total_leads' => '',
                'converted_leads' => '',
                'conversion_rate' => '',
                'telecaller_name' => '',
                'experience_level' => '',
                'total_calls' => '',
                'avg_call_duration' => '',
            ];
        }
        
        return $data;
    }

    public function headings(): array
    {
        return [
            'Type',
            'Team Name',
            'Team Lead',
            'Total Members',
            'Active Members',
            'Total Leads',
            'Converted Leads',
            'Conversion Rate',
            'Telecaller Name',
            'Experience Level',
            'Total Calls',
            'Avg Call Duration',
        ];
    }

    public function map($row): array
    {
        return [
            $row['type'],
            $row['team_name'],
            $row['team_lead'],
            $row['total_members'],
            $row['active_members'],
            $row['total_leads'],
            $row['converted_leads'],
            $row['conversion_rate'],
            $row['telecaller_name'],
            $row['experience_level'],
            $row['total_calls'],
            $row['avg_call_duration'],
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
            'A' => 20,
            'B' => 25,
            'C' => 20,
            'D' => 15,
            'E' => 15,
            'F' => 15,
            'G' => 15,
            'H' => 15,
            'I' => 25,
            'J' => 20,
            'K' => 15,
            'L' => 20,
        ];
    }
}
