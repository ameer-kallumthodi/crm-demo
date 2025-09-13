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
        if (RoleHelper::is_team_lead() && !RoleHelper::is_admin()) {
            $teamId = \App\Models\User::where('id', AuthHelper::getCurrentUserId())->value('team_id');
            $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
            $query->whereIn('created_by', $teamMemberIds);
        } elseif (RoleHelper::is_telecaller()) {
            $query->where('created_by', AuthHelper::getCurrentUserId());
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

        $convertedLeads = $query->orderBy('created_at', 'desc')->paginate(20);

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

        return view('admin.converted-leads.show', compact('convertedLead'));
    }
}
