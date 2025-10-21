<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\User;
use App\Helpers\AuthHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TelecallerTaskController extends Controller
{
    /**
     * Constructor - Check if user is super admin
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!AuthHelper::isSuperAdmin()) {
                abort(403, 'Access denied. Super admin access required.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of leads (tasks) assigned to telecallers
     */
    public function index(Request $request)
    {
        $query = Lead::with(['telecaller', 'leadStatus', 'leadSource'])
            ->whereNotNull('telecaller_id');

        // Filter by telecaller
        if ($request->has('telecaller_id') && $request->telecaller_id) {
            $query->where('telecaller_id', $request->telecaller_id);
        }

        // Filter by conversion status
        if ($request->has('is_converted') && $request->is_converted !== '' && $request->is_converted !== null) {
            $isConverted = filter_var($request->is_converted, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($isConverted !== null) {
                $query->where('is_converted', $isConverted);
            }
        }

        // Filter by lead status
        if ($request->has('lead_status_id') && $request->lead_status_id) {
            $query->where('lead_status_id', $request->lead_status_id);
        }

        // Filter by lead source
        if ($request->has('lead_source_id') && $request->lead_source_id) {
            $query->where('lead_source_id', $request->lead_source_id);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $tasks = $query->orderBy('created_at', 'desc')->get();
        $telecallers = User::where('role_id', 3)->get();

        return view('admin.telecaller-tracking.tasks', compact('tasks', 'telecallers'));
    }

    /**
     * Show the form for creating a new lead assignment
     */
    public function create()
    {
        $telecallers = User::where('role_id', 3)->get();
        $leadStatuses = \App\Models\LeadStatus::all();
        $leadSources = \App\Models\LeadSource::all();
        return view('admin.telecaller-tracking.create-task', compact('telecallers', 'leadStatuses', 'leadSources'));
    }

    /**
     * Store a newly created lead assignment
     */
    public function store(Request $request)
    {
        $request->validate([
            'telecaller_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'lead_status_id' => 'nullable|exists:lead_statuses,id',
            'lead_source_id' => 'nullable|exists:lead_sources,id',
            'followup_date' => 'nullable|date|after:now',
        ]);

        Lead::create([
            'telecaller_id' => $request->telecaller_id,
            'title' => $request->title,
            'phone' => $request->phone,
            'email' => $request->email,
            'lead_status_id' => $request->lead_status_id,
            'lead_source_id' => $request->lead_source_id,
            'followup_date' => $request->followup_date,
            'created_by' => AuthHelper::getUserId(),
        ]);

        return redirect()->route('admin.telecaller-tasks.index')
            ->with('success', 'Lead assigned successfully.');
    }

    /**
     * Display the specified lead
     */
    public function show(Lead $task)
    {
        $task->load(['telecaller', 'leadStatus', 'leadSource', 'createdBy']);
        return view('admin.telecaller-tracking.show-task', compact('task'));
    }

    /**
     * Show the form for editing the specified lead
     */
    public function edit(Lead $task)
    {
        $telecallers = User::where('role_id', 3)->get();
        $leadStatuses = \App\Models\LeadStatus::all();
        $leadSources = \App\Models\LeadSource::all();
        return view('admin.telecaller-tracking.edit-task', compact('task', 'telecallers', 'leadStatuses', 'leadSources'));
    }

    /**
     * Update the specified lead
     */
    public function update(Request $request, Lead $task)
    {
        $request->validate([
            'telecaller_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'lead_status_id' => 'nullable|exists:lead_statuses,id',
            'lead_source_id' => 'nullable|exists:lead_sources,id',
            'followup_date' => 'nullable|date',
            'remarks' => 'nullable|string',
        ]);

        $task->update([
            'telecaller_id' => $request->telecaller_id,
            'title' => $request->title,
            'phone' => $request->phone,
            'email' => $request->email,
            'lead_status_id' => $request->lead_status_id,
            'lead_source_id' => $request->lead_source_id,
            'followup_date' => $request->followup_date,
            'remarks' => $request->remarks,
            'updated_by' => AuthHelper::getUserId(),
        ]);

        return redirect()->route('admin.telecaller-tasks.index')
            ->with('success', 'Lead updated successfully.');
    }

    /**
     * Mark lead as converted
     */
    public function complete(Request $request, Lead $task)
    {
        $request->validate([
            'remarks' => 'nullable|string',
        ]);

        $task->update([
            'is_converted' => true,
            'remarks' => $request->remarks,
            'updated_by' => AuthHelper::getUserId(),
        ]);

        return redirect()->back()
            ->with('success', 'Lead marked as converted.');
    }

    /**
     * Remove the specified lead
     */
    public function destroy(Lead $task)
    {
        $task->delete();

        return redirect()->route('admin.telecaller-tasks.index')
            ->with('success', 'Lead deleted successfully.');
    }

    /**
     * Get overdue leads
     */
    public function overdue()
    {
        // Get leads that haven't been updated in the last 7 days
        $overdueTasks = Lead::with(['telecaller', 'leadStatus', 'leadActivities'])
            ->whereNotNull('telecaller_id')
            ->where('is_converted', false)
            ->where(function($query) {
                // Either no activities at all, or last activity was more than 7 days ago
                $query->whereDoesntHave('leadActivities')
                      ->orWhereHas('leadActivities', function($subQuery) {
                          $subQuery->where('created_at', '<', now()->subDays(7));
                      });
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.telecaller-tracking.overdue-tasks', compact('overdueTasks'));
    }

    /**
     * Get leads created today
     */
    public function dueToday()
    {
        $dueTodayTasks = Lead::with(['telecaller', 'leadStatus'])
            ->whereNotNull('telecaller_id')
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.telecaller-tracking.due-today-tasks', compact('dueTodayTasks'));
    }

    /**
     * Get lead statistics
     */
    public function statistics(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $telecallerId = $request->get('telecaller_id');

        $query = Lead::whereNotNull('telecaller_id')
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($telecallerId) {
            $query->where('telecaller_id', $telecallerId);
        }

        $totalTasks = $query->count();
        $completedTasks = $query->clone()->where('is_converted', true)->count();
        $pendingTasks = $query->clone()->where('is_converted', false)->count();
        $overdueTasks = $query->clone()->where('is_converted', false)->where('created_at', '<', now()->subDays(7))->count();

        $tasksByStatus = $query->clone()
            ->join('lead_statuses', 'leads.lead_status_id', '=', 'lead_statuses.id')
            ->selectRaw('lead_statuses.title as status_name, COUNT(*) as count')
            ->groupBy('lead_statuses.id', 'lead_statuses.title')
            ->get();

        $tasksBySource = $query->clone()
            ->join('lead_sources', 'leads.lead_source_id', '=', 'lead_sources.id')
            ->selectRaw('lead_sources.title as source_name, COUNT(*) as count')
            ->groupBy('lead_sources.id', 'lead_sources.title')
            ->get();

        $telecallers = User::where('role_id', 3)->get();

        return view('admin.telecaller-tracking.task-statistics', compact(
            'totalTasks',
            'completedTasks',
            'pendingTasks',
            'overdueTasks',
            'tasksByStatus',
            'tasksBySource',
            'telecallers',
            'startDate',
            'endDate',
            'telecallerId'
        ));
    }
}
