<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VoxbayCallLog;
use App\Models\User;
use App\Models\Lead;
use App\Models\Team;
use App\Helpers\AuthHelper;
use App\Helpers\RoleHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;

class VoxbayReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth');
    }

    /**
     * Display Voxbay call logs report
     */
    public function index(Request $request)
    {
        // Check if user has access to call logs
        if (!RoleHelper::is_admin() && !RoleHelper::is_super_admin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        // Default date range (last 30 days)
        $fromDate = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $teamId = $request->get('team_id');
        $telecallerId = $request->get('telecaller_id');

        // Get filter options
        $teams = Team::select('id', 'name')->get();
        $telecallers = User::whereHas('role', function($query) {
            $query->where('title', 'Telecaller');
        })->select('id', 'name')->get();

        // Get call logs report data
        $reportData = $this->getCallLogsReportData($fromDate, $toDate, $teamId, $telecallerId);

        return view('admin.reports.voxbay-call-logs', compact(
            'reportData', 'teams', 'telecallers', 'fromDate', 'toDate', 'teamId', 'telecallerId'
        ));
    }

    /**
     * Get call logs report data
     */
    private function getCallLogsReportData($fromDate, $toDate, $teamId = null, $telecallerId = null)
    {
        $query = VoxbayCallLog::query();

        // Apply date filter
        $query->whereBetween('date', [$fromDate, $toDate]);

        // Apply team filter
        if ($teamId) {
            $query->whereHas('telecaller', function($q) use ($teamId) {
                $q->where('team_id', $teamId);
            });
        }

        // Apply telecaller filter
        if ($telecallerId) {
            $telecaller = User::find($telecallerId);
            if ($telecaller) {
                $agentNumber = $telecaller->code . $telecaller->phone;
                $query->where('AgentNumber', $agentNumber);
            }
        }

        // Get basic statistics
        $stats = [
            'total_calls' => $query->count(),
            'incoming_calls' => $query->clone()->where('type', 'incoming')->count(),
            'outgoing_calls' => $query->clone()->where('type', 'outgoing')->count(),
            'missed_calls' => $query->clone()->where('type', 'missedcall')->count(),
            'answered_calls' => $query->clone()->where('status', 'ANSWER')->count(),
            'busy_calls' => $query->clone()->where('status', 'BUSY')->count(),
            'cancelled_calls' => $query->clone()->where('status', 'CANCEL')->count(),
            'no_answer_calls' => $query->clone()->where('status', 'NO ANSWER')->count(),
            'total_duration' => $query->clone()->sum('duration'),
            'average_duration' => $query->clone()->avg('duration'),
        ];

        // Get call logs by telecaller
        $telecallerStats = $this->getTelecallerCallStats($fromDate, $toDate, $teamId);

        // Get call logs by team
        $teamStats = $this->getTeamCallStats($fromDate, $toDate);

        // Get call logs by date
        $dailyStats = $this->getDailyCallStats($fromDate, $toDate, $teamId, $telecallerId);

        // Get recent call logs
        $recentCalls = $query->clone()
            ->with(['createdBy', 'updatedBy'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($callLog) {
                $callLog->telecaller_name = $callLog->getTelecallerName();
                return $callLog;
            });

        return [
            'stats' => $stats,
            'telecaller_stats' => $telecallerStats,
            'team_stats' => $teamStats,
            'daily_stats' => $dailyStats,
            'recent_calls' => $recentCalls
        ];
    }

    /**
     * Get call statistics by telecaller
     */
    private function getTelecallerCallStats($fromDate, $toDate, $teamId = null)
    {
        $query = VoxbayCallLog::whereBetween('date', [$fromDate, $toDate])
            ->whereNotNull('AgentNumber');

        if ($teamId) {
            $query->whereHas('telecaller', function($q) use ($teamId) {
                $q->where('team_id', $teamId);
            });
        }

        return $query->select('AgentNumber', DB::raw('COUNT(*) as total_calls'))
            ->selectRaw('SUM(CASE WHEN type = "incoming" THEN 1 ELSE 0 END) as incoming_calls')
            ->selectRaw('SUM(CASE WHEN type = "outgoing" THEN 1 ELSE 0 END) as outgoing_calls')
            ->selectRaw('SUM(CASE WHEN status = "ANSWER" THEN 1 ELSE 0 END) as answered_calls')
            ->selectRaw('SUM(duration) as total_duration')
            ->selectRaw('AVG(duration) as avg_duration')
            ->groupBy('AgentNumber')
            ->orderBy('total_calls', 'desc')
            ->get()
            ->map(function ($item) {
                $countryCode = substr($item->AgentNumber, 0, 2);
                $mobileNumber = substr($item->AgentNumber, 2);
                
                $user = User::where('code', $countryCode)
                           ->where('phone', $mobileNumber)
                           ->whereHas('role', function($query) {
                               $query->where('title', 'Telecaller');
                           })
                           ->with('team')
                           ->first();
                
                return [
                    'agent_number' => $item->AgentNumber,
                    'telecaller_name' => $user ? $user->name : 'Unknown',
                    'team_name' => $user && $user->team ? $user->team->name : 'N/A',
                    'total_calls' => $item->total_calls,
                    'incoming_calls' => $item->incoming_calls,
                    'outgoing_calls' => $item->outgoing_calls,
                    'answered_calls' => $item->answered_calls,
                    'total_duration' => $item->total_duration,
                    'avg_duration' => $item->avg_duration,
                    'answer_rate' => $item->total_calls > 0 ? round(($item->answered_calls / $item->total_calls) * 100, 2) : 0
                ];
            });
    }

    /**
     * Get call statistics by team
     */
    private function getTeamCallStats($fromDate, $toDate)
    {
        return Team::with(['users' => function($query) {
            $query->whereHas('role', function($q) {
                $q->where('title', 'Telecaller');
            });
        }])
        ->get()
        ->map(function ($team) use ($fromDate, $toDate) {
            $agentNumbers = $team->users->map(function ($user) {
                return $user->code . $user->phone;
            })->toArray();

            $callStats = VoxbayCallLog::whereBetween('date', [$fromDate, $toDate])
                ->whereIn('AgentNumber', $agentNumbers)
                ->selectRaw('COUNT(*) as total_calls')
                ->selectRaw('SUM(CASE WHEN type = "incoming" THEN 1 ELSE 0 END) as incoming_calls')
                ->selectRaw('SUM(CASE WHEN type = "outgoing" THEN 1 ELSE 0 END) as outgoing_calls')
                ->selectRaw('SUM(CASE WHEN status = "ANSWER" THEN 1 ELSE 0 END) as answered_calls')
                ->selectRaw('SUM(duration) as total_duration')
                ->selectRaw('AVG(duration) as avg_duration')
                ->first();

            return [
                'team_id' => $team->id,
                'team_name' => $team->name,
                'total_members' => $team->users->count(),
                'total_calls' => $callStats->total_calls ?? 0,
                'incoming_calls' => $callStats->incoming_calls ?? 0,
                'outgoing_calls' => $callStats->outgoing_calls ?? 0,
                'answered_calls' => $callStats->answered_calls ?? 0,
                'total_duration' => $callStats->total_duration ?? 0,
                'avg_duration' => $callStats->avg_duration ?? 0,
                'answer_rate' => $callStats->total_calls > 0 ? round((($callStats->answered_calls ?? 0) / $callStats->total_calls) * 100, 2) : 0
            ];
        })
        ->filter(function ($team) {
            return $team['total_calls'] > 0;
        })
        ->sortByDesc('total_calls')
        ->values();
    }

    /**
     * Get daily call statistics
     */
    private function getDailyCallStats($fromDate, $toDate, $teamId = null, $telecallerId = null)
    {
        $query = VoxbayCallLog::whereBetween('date', [$fromDate, $toDate]);

        if ($teamId) {
            $query->whereHas('telecaller', function($q) use ($teamId) {
                $q->where('team_id', $teamId);
            });
        }

        if ($telecallerId) {
            $telecaller = User::find($telecallerId);
            if ($telecaller) {
                $agentNumber = $telecaller->code . $telecaller->phone;
                $query->where('AgentNumber', $agentNumber);
            }
        }

        return $query->select('date', DB::raw('COUNT(*) as total_calls'))
            ->selectRaw('SUM(CASE WHEN type = "incoming" THEN 1 ELSE 0 END) as incoming_calls')
            ->selectRaw('SUM(CASE WHEN type = "outgoing" THEN 1 ELSE 0 END) as outgoing_calls')
            ->selectRaw('SUM(CASE WHEN status = "ANSWER" THEN 1 ELSE 0 END) as answered_calls')
            ->selectRaw('SUM(duration) as total_duration')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();
    }

    /**
     * Export call logs to Excel
     */
    public function exportExcel(Request $request)
    {
        $fromDate = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $teamId = $request->get('team_id');
        $telecallerId = $request->get('telecaller_id');

        $reportData = $this->getCallLogsReportData($fromDate, $toDate, $teamId, $telecallerId);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'Voxbay Call Logs Report');
        $sheet->setCellValue('A2', 'Date Range: ' . $fromDate . ' to ' . $toDate);
        $sheet->setCellValue('A4', 'Summary Statistics');
        $sheet->setCellValue('A5', 'Total Calls: ' . $reportData['stats']['total_calls']);
        $sheet->setCellValue('A6', 'Incoming Calls: ' . $reportData['stats']['incoming_calls']);
        $sheet->setCellValue('A7', 'Outgoing Calls: ' . $reportData['stats']['outgoing_calls']);
        $sheet->setCellValue('A8', 'Answered Calls: ' . $reportData['stats']['answered_calls']);
        $sheet->setCellValue('A9', 'Total Duration: ' . gmdate('H:i:s', $reportData['stats']['total_duration']));

        // Telecaller statistics
        $sheet->setCellValue('A12', 'Telecaller Statistics');
        $sheet->setCellValue('A13', 'Telecaller Name');
        $sheet->setCellValue('B13', 'Team');
        $sheet->setCellValue('C13', 'Total Calls');
        $sheet->setCellValue('D13', 'Answered Calls');
        $sheet->setCellValue('E13', 'Answer Rate (%)');
        $sheet->setCellValue('F13', 'Total Duration');

        $row = 14;
        foreach ($reportData['telecaller_stats'] as $stat) {
            $sheet->setCellValue('A' . $row, $stat['telecaller_name']);
            $sheet->setCellValue('B' . $row, $stat['team_name']);
            $sheet->setCellValue('C' . $row, $stat['total_calls']);
            $sheet->setCellValue('D' . $row, $stat['answered_calls']);
            $sheet->setCellValue('E' . $row, $stat['answer_rate']);
            $sheet->setCellValue('F' . $row, gmdate('H:i:s', $stat['total_duration']));
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'voxbay-call-logs-report-' . $fromDate . '-to-' . $toDate . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Export call logs to PDF
     */
    public function exportPdf(Request $request)
    {
        $fromDate = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $teamId = $request->get('team_id');
        $telecallerId = $request->get('telecaller_id');

        $reportData = $this->getCallLogsReportData($fromDate, $toDate, $teamId, $telecallerId);

        $pdf = Pdf::loadView('admin.reports.pdf.voxbay-call-logs', [
            'reportData' => $reportData,
            'fromDate' => $fromDate,
            'toDate' => $toDate
        ]);

        return $pdf->download('voxbay-call-logs-report-' . $fromDate . '-to-' . $toDate . '.pdf');
    }
}
