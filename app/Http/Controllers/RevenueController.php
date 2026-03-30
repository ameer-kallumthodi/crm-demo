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
        $showTeamBreakdown = RoleHelper::is_admin_or_super_admin() || RoleHelper::is_finance();
        $teamBreakdown = $showTeamBreakdown
            ? $revenueReportService->getTeamBreakdownForAdmin()
            : [];

        return view('revenue.index', compact('totals', 'showTeamBreakdown', 'teamBreakdown'));
    }

    /**
     * Ajax-loaded modal content: show selected B2B team revenue details by course.
     */
    public function teamDetails(int $teamId, RevenueReportService $revenueReportService)
    {
        if (!(RoleHelper::is_admin_or_super_admin() || RoleHelper::is_finance())) {
            abort(403);
        }

        $details = $revenueReportService->getTeamDetailsForAdmin($teamId);

        return view('revenue.team-details', [
            'details' => $details,
        ]);
    }
}
