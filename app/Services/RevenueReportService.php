<?php

namespace App\Services;

use App\Helpers\AuthHelper;
use App\Helpers\RoleHelper;
use Illuminate\Support\Facades\DB;

class RevenueReportService
{
    /**
     * Invoice-backed revenue for visible converted leads (non-cancelled), after discount.
     * Scope matches DashboardController::applyRoleBasedFilterToConvertedLeads (via leads.telecaller_id).
     * Totals include only partner-channel rows: lead or converted lead marked partner, or lead’s team is a partner team.
     */
    public function getTotalsForCurrentUser(): array
    {
        $query = $this->baseInvoicesQuery();
        $this->applyConvertedLeadVisibilityScope($query);
        $this->applyPartnerChannelRevenueFilter($query);

        return $this->aggregateQuery($query);
    }

    /**
     * Per-team breakdown (full organization, no user scope). Only teams flagged as partner channel; unassigned leads omitted.
     *
     * @return list<array{team_id: int|null, team_name: string, total_payable: float, total_paid: float, total_balance: float, total_discount: float}>
     */
    public function getTeamBreakdownForAdmin(): array
    {
        $query = $this->baseInvoicesQuery()
            ->join('teams', 'leads.team_id', '=', 'teams.id')
            ->where('teams.is_b2b', 1)
            ->whereNull('teams.deleted_at')
            ->selectRaw('
                leads.team_id,
                MAX(teams.name) as team_name,
                COALESCE(SUM(GREATEST(0, invoices.total_amount - COALESCE(invoices.discount_amount, 0))), 0) as total_payable,
                COALESCE(SUM(COALESCE(invoices.paid_amount, 0)), 0) as total_paid,
                COALESCE(SUM(GREATEST(0,
                    (invoices.total_amount - COALESCE(invoices.discount_amount, 0)) - COALESCE(invoices.paid_amount, 0)
                )), 0) as total_balance,
                COALESCE(SUM(COALESCE(invoices.discount_amount, 0)), 0) as total_discount
            ')
            ->groupBy('leads.team_id')
            ->orderByDesc('total_payable');

        return $query->get()->map(function ($r) {
            return [
                'team_id' => $r->team_id,
                'team_name' => (string) $r->team_name,
                'total_payable' => (float) $r->total_payable,
                'total_paid' => (float) $r->total_paid,
                'total_balance' => (float) $r->total_balance,
                'total_discount' => (float) $r->total_discount,
            ];
        })->values()->all();
    }

    private function baseInvoicesQuery()
    {
        return DB::table('invoices')
            ->join('converted_leads', 'invoices.student_id', '=', 'converted_leads.id')
            ->join('leads', 'converted_leads.lead_id', '=', 'leads.id')
            ->whereNull('invoices.deleted_at')
            ->whereNull('converted_leads.deleted_at')
            ->where(function ($q) {
                $q->whereNull('converted_leads.is_cancelled')
                    ->orWhere('converted_leads.is_cancelled', 0);
            });
    }

    private function applyConvertedLeadVisibilityScope($query): void
    {
        $currentUser = AuthHelper::getCurrentUser();
        if (!$currentUser) {
            return;
        }

        if (RoleHelper::is_admin_or_super_admin()
            || RoleHelper::is_general_manager()
            || RoleHelper::is_senior_manager()
            || RoleHelper::is_admission_counsellor()
            || RoleHelper::is_finance()
            || RoleHelper::is_academic_assistant()
            || RoleHelper::is_post_sales()) {
            return;
        }

        if (AuthHelper::isTeamLead()) {
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                $teamMemberIds[] = AuthHelper::getCurrentUserId();
                $query->whereIn('leads.telecaller_id', $teamMemberIds);
            } else {
                $query->where('leads.telecaller_id', AuthHelper::getCurrentUserId());
            }

            return;
        }

        if (AuthHelper::isTelecaller()) {
            $query->where('leads.telecaller_id', AuthHelper::getCurrentUserId());

            return;
        }

        $query->whereRaw('1 = 0');
    }

    /**
     * Restrict to partner-channel revenue (same notion as partner teams / B2B flags on lead and student).
     */
    private function applyPartnerChannelRevenueFilter($query): void
    {
        $query->leftJoin('teams as revenue_teams', 'leads.team_id', '=', 'revenue_teams.id')
            ->where(function ($q) {
                $q->where('converted_leads.is_b2b', 1)
                    ->orWhere('leads.is_b2b', 1)
                    ->orWhere(function ($q2) {
                        $q2->where('revenue_teams.is_b2b', 1)
                            ->whereNull('revenue_teams.deleted_at');
                    });
            });
    }

    private function aggregateQuery($query): array
    {
        $row = $query->selectRaw('
            COALESCE(SUM(GREATEST(0, invoices.total_amount - COALESCE(invoices.discount_amount, 0))), 0) as total_payable,
            COALESCE(SUM(COALESCE(invoices.paid_amount, 0)), 0) as total_paid,
            COALESCE(SUM(GREATEST(0,
                (invoices.total_amount - COALESCE(invoices.discount_amount, 0)) - COALESCE(invoices.paid_amount, 0)
            )), 0) as total_balance,
            COALESCE(SUM(COALESCE(invoices.discount_amount, 0)), 0) as total_discount
        ')->first();

        return [
            'total_payable' => (float) ($row->total_payable ?? 0),
            'total_paid' => (float) ($row->total_paid ?? 0),
            'total_balance' => (float) ($row->total_balance ?? 0),
            'total_discount' => (float) ($row->total_discount ?? 0),
        ];
    }
}
