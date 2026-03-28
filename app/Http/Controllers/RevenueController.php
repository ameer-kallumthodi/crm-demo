<?php

namespace App\Http\Controllers;

use App\Helpers\RoleHelper;
use App\Services\RevenueReportService;

class RevenueController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth');
    }

    public function index(RevenueReportService $revenueReportService)
    {
        if (RoleHelper::is_auditor()) {
            abort(403);
        }

        $totals = $revenueReportService->getTotalsForCurrentUser();
        $showTeamBreakdown = RoleHelper::is_admin_or_super_admin();
        $teamBreakdown = $showTeamBreakdown
            ? $revenueReportService->getTeamBreakdownForAdmin()
            : [];

        return view('revenue.index', compact('totals', 'showTeamBreakdown', 'teamBreakdown'));
    }
}
