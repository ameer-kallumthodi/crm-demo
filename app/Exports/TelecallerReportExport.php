<?php

namespace App\Exports;

use App\Models\TelecallerSession;
use App\Models\TelecallerTask;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class TelecallerReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $startDate;
    protected $endDate;
    protected $telecallerId;

    public function __construct($startDate, $endDate, $telecallerId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->telecallerId = $telecallerId;
    }

    public function collection()
    {
        $query = TelecallerSession::with(['user', 'idleTimes'])
            ->whereBetween('login_time', [$this->startDate, $this->endDate]);

        if ($this->telecallerId) {
            $query->where('user_id', $this->telecallerId);
        }

        return $query->orderBy('login_time', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Telecaller Name',
            'Email',
            'Login Time',
            'Logout Time',
            'Total Duration (Hours)',
            'Active Duration (Hours)',
            'Idle Duration (Hours)',
            'Logout Type',
            'IP Address',
            'Sessions Count',
            'Tasks Assigned',
            'Tasks Completed',
            'Productivity Score (%)'
        ];
    }

    public function map($session): array
    {
        $totalHours = $session->total_duration_minutes ? 
            round($session->total_duration_minutes / 60, 2) : 
            round($session->calculateTotalDuration() / 60, 2);

        $activeHours = $session->active_duration_minutes ? 
            round($session->active_duration_minutes / 60, 2) : 
            round($session->calculateActiveDuration() / 60, 2);

        $idleHours = $session->idle_duration_minutes ? 
            round($session->idle_duration_minutes / 60, 2) : 
            round($session->idleTimes()->sum('idle_duration_seconds') / 3600, 2);

        // Get tasks for this user in the date range
        $tasksQuery = TelecallerTask::where('user_id', $session->user_id)
            ->whereBetween('created_at', [$this->startDate, $this->endDate]);
        
        $totalTasks = $tasksQuery->count();
        $completedTasks = $tasksQuery->where('status', 'completed')->count();
        $productivityScore = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;

        // Get total sessions for this user in the date range
        $sessionsCount = TelecallerSession::where('user_id', $session->user_id)
            ->whereBetween('login_time', [$this->startDate, $this->endDate])
            ->count();

        return [
            $session->user->name,
            $session->user->email,
            $session->login_time->format('Y-m-d H:i:s'),
            $session->logout_time ? $session->logout_time->format('Y-m-d H:i:s') : 'Active',
            $totalHours,
            $activeHours,
            $idleHours,
            ucfirst($session->logout_type),
            $session->ip_address,
            $sessionsCount,
            $totalTasks,
            $completedTasks,
            $productivityScore
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // Telecaller Name
            'B' => 25, // Email
            'C' => 20, // Login Time
            'D' => 20, // Logout Time
            'E' => 18, // Total Duration
            'F' => 18, // Active Duration
            'G' => 18, // Idle Duration
            'H' => 15, // Logout Type
            'I' => 15, // IP Address
            'J' => 15, // Sessions Count
            'K' => 15, // Tasks Assigned
            'L' => 15, // Tasks Completed
            'M' => 18, // Productivity Score
        ];
    }
}