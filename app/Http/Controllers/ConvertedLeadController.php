<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConvertedLead;
use App\Models\Lead;
use App\Helpers\AuthHelper;
use App\Helpers\RoleHelper;

class ConvertedLeadController extends Controller
{
    /**
     * Display a listing of converted leads
     */
    public function index(Request $request)
    {
        $query = ConvertedLead::with(['lead', 'course', 'academicAssistant', 'createdBy']);

        // Apply role-based filtering
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            // Check team lead first (highest priority)
            if (RoleHelper::is_team_lead()) {
                // Team Lead: Can see converted leads from their team
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    $query->whereIn('created_by', $teamMemberIds);
                } else {
                    // If no team assigned, only show their own leads
                    $query->where('created_by', AuthHelper::getCurrentUserId());
                }
            } elseif (RoleHelper::is_admission_counsellor()) {
                // Admission Counsellor: Can see ALL converted leads
                // No additional filtering needed - show all
            } elseif (RoleHelper::is_academic_assistant()) {
                // Academic Assistant: Can only see converted leads assigned to them
                $query->where('academic_assistant_id', AuthHelper::getCurrentUserId());
            } elseif (RoleHelper::is_telecaller()) {
                // Telecaller: Can only see converted leads they created
                $query->where('created_by', AuthHelper::getCurrentUserId());
            }
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }


        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $convertedLeads = $query->orderBy('created_at', 'desc')->get();

        // Get filter data
        $courses = \App\Models\Course::where('is_active', 1)->get();

        return view('admin.converted-leads.index', compact('convertedLeads', 'courses'));
    }

    /**
     * Display the specified converted lead
     */
    public function show($id)
    {
        $convertedLead = ConvertedLead::with([
            'lead', 
            'course', 
            'academicAssistant', 
            'createdBy'
        ])->findOrFail($id);

        // Apply role-based access control
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            // Check team lead first (highest priority)
            if (RoleHelper::is_team_lead()) {
                // Team Lead: Can see converted leads from their team
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    if (!in_array($convertedLead->created_by, $teamMemberIds)) {
                        return redirect()->route('admin.converted-leads.index')
                            ->with('message_danger', 'Access denied. You can only view converted leads from your team.');
                    }
                } else {
                    // If no team assigned, only show their own leads
                    if ($convertedLead->created_by != AuthHelper::getCurrentUserId()) {
                        return redirect()->route('admin.converted-leads.index')
                            ->with('message_danger', 'Access denied. You can only view converted leads you created.');
                    }
                }
            } elseif (RoleHelper::is_admission_counsellor()) {
                // Admission Counsellor: Can see ALL converted leads
                // No additional filtering needed
            } elseif (RoleHelper::is_academic_assistant()) {
                // Academic Assistant: Can only see converted leads assigned to them
                if ($convertedLead->academic_assistant_id != AuthHelper::getCurrentUserId()) {
                    return redirect()->route('admin.converted-leads.index')
                        ->with('message_danger', 'Access denied. You can only view converted leads assigned to you.');
                }
            } elseif (RoleHelper::is_telecaller()) {
                // Telecaller: Can only see converted leads they created
                if ($convertedLead->created_by != AuthHelper::getCurrentUserId()) {
                    return redirect()->route('admin.converted-leads.index')
                        ->with('message_danger', 'Access denied. You can only view converted leads you created.');
                }
            }
        }

        // Get lead activities for this converted lead
        $leadActivities = \App\Models\LeadActivity::where('lead_id', $convertedLead->lead_id)
            ->select('id', 'lead_id', 'reason', 'created_at', 'activity_type', 'description', 'remarks', 'rating', 'followup_date', 'created_by', 'lead_status_id')
            ->with(['leadStatus:id,title', 'createdBy:id,name'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.converted-leads.show', compact('convertedLead', 'leadActivities'));
    }
}
