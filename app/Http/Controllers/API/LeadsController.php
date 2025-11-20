<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\LeadSource;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LeadsController extends Controller
{
    /**
     * Get leads list with lazy loading (pagination)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // Base query for leads
        $query = Lead::with([
            'leadStatus:id,title',
            'leadSource:id,title',
            'course:id,title',
            'telecaller:id,name',
            'studentDetails:id,lead_id,status'
        ]);

        // Apply role-based filtering
        $this->applyRoleBasedFilter($query, $user);

        // Apply filters
        if ($request->filled('lead_status_id')) {
            $query->where('lead_status_id', $request->lead_status_id);
        }

        if ($request->filled('lead_source_id')) {
            $query->where('lead_source_id', $request->lead_source_id);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('telecaller_id')) {
            $query->where('telecaller_id', $request->telecaller_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Date range filter
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $fromDate = Carbon::parse($request->date_from)->startOfDay();
            $toDate = Carbon::parse($request->date_to)->endOfDay();
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        }

        // Order by created_at desc
        $query->orderBy('created_at', 'desc');

        // Pagination - lazy loading
        $perPage = $request->get('per_page', 15);
        $leads = $query->paginate($perPage);

        // Format leads data
        $formattedLeads = $leads->map(function ($lead) {
            return $this->formatLeadData($lead);
        });

        return response()->json([
            'status' => true,
            'data' => $formattedLeads,
            'pagination' => [
                'current_page' => $leads->currentPage(),
                'per_page' => $leads->perPage(),
                'total' => $leads->total(),
                'last_page' => $leads->lastPage(),
                'from' => $leads->firstItem(),
                'to' => $leads->lastItem()
            ]
        ], 200);
    }

    /**
     * Get leads filter data (statuses, sources, courses, rating, telecallers)
     */
    public function filters(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $leadStatuses = LeadStatus::select('id', 'title')
            ->orderBy('title')
            ->get()
            ->map(function ($status) {
                return [
                    'id' => $status->id,
                    'title' => $status->title,
                ];
            });

        $leadSources = LeadSource::select('id', 'title')
            ->orderBy('title')
            ->get()
            ->map(function ($source) {
                return [
                    'id' => $source->id,
                    'title' => $source->title,
                ];
            });

        $courses = Course::select('id', 'title')
            ->orderBy('title')
            ->get()
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                ];
            });

        $ratings = collect(range(1, 10))->map(function ($rating) {
            return [
                'value' => $rating,
                'label' => $rating . '/10',
            ];
        });

        $telecallers = $this->getTelecallersForUser($user)->map(function ($telecaller) {
            return [
                'id' => $telecaller->id,
                'name' => $telecaller->name,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => [
                'lead_statuses' => $leadStatuses,
                'lead_sources' => $leadSources,
                'courses' => $courses,
                'ratings' => $ratings,
                'telecallers' => $telecallers,
            ]
        ], 200);
    }

    /**
     * Format lead data for API response
     */
    private function formatLeadData($lead)
    {
        // Format phone with code
        $phone = '';
        if ($lead->code && $lead->phone) {
            $phone = '+' . $lead->code . ' ' . $lead->phone;
        } elseif ($lead->phone) {
            $phone = $lead->phone;
        }

        // Calculate profile completeness
        $requiredFields = [
            'title', 'gender', 'age', 'phone', 'code', 'whatsapp', 'whatsapp_code',
            'email', 'qualification', 'country_id', 'interest_status', 'lead_status_id',
            'lead_source_id', 'address', 'telecaller_id', 'team_id', 'place'
        ];
        $completedFields = 0;
        
        foreach ($requiredFields as $field) {
            if (!empty($lead->$field)) {
                $completedFields++;
            }
        }
        
        $profileCompletedPercentage = round(($completedFields / count($requiredFields)) * 100);

        // Determine show_lead_reg_form_link (1 if course has registration form, 0 otherwise)
        $courseRoutes = [
            1 => 'public.lead.nios.register',
            2 => 'public.lead.bosse.register',
            3 => 'public.lead.medical-coding.register',
            4 => 'public.lead.hospital-admin.register',
            5 => 'public.lead.eschool.register',
            6 => 'public.lead.eduthanzeel.register',
            7 => 'public.lead.ttc.register',
            8 => 'public.lead.hotel-mgmt.register',
            9 => 'public.lead.ugpg.register',
            10 => 'public.lead.python.register',
            11 => 'public.lead.digital-marketing.register',
            12 => 'public.lead.ai-automation.register',
            13 => 'public.lead.web-dev.register',
            14 => 'public.lead.vibe-coding.register',
            15 => 'public.lead.graphic-designing.register',
            16 => 'public.lead.gmvss.register'
        ];
        
        $showLeadRegFormLink = isset($courseRoutes[$lead->course_id]) ? 1 : 0;
        
        // Get registration form link
        $regFormLink = '';
        if ($showLeadRegFormLink && $lead->course_id) {
            $routeName = $courseRoutes[$lead->course_id];
            try {
                $regFormLink = route($routeName, $lead->id);
            } catch (\Exception $e) {
                // If route doesn't exist, set to null
                $regFormLink = '';
            }
        }

        // Check if registration form is submitted
        $isLeadRegFormSubmitted = $lead->studentDetails ? 1 : 0;

        // Format follow_up_date
        $followUpDate = $lead->followup_date ? Carbon::parse($lead->followup_date)->format('d-m-Y') : '';

        // Split created_at into date and time
        $createdAt = Carbon::parse($lead->created_at);
        $date = $createdAt->format('d-m-Y');
        $time = $createdAt->format('h:i A');

        $registrationDetailsStatus = $isLeadRegFormSubmitted ? $this->getRegistrationDetailsStatus($lead) : '';

        return [
            'id' => $lead->id,
            'name' => $lead->title ?? '',
            'profile_completed_percentage' => $profileCompletedPercentage,
            'phone' => $phone,
            'email' => $lead->email ?? '',
            'lead_status' => $lead->leadStatus ? $lead->leadStatus->title : '',
            'rating' => $lead->rating ?? '',
            'lead_source' => $lead->leadSource ? $lead->leadSource->title : '',
            'course_name' => $lead->course ? $lead->course->title : '',
            'telecaller_name' => $lead->telecaller ? $lead->telecaller->name : '',
            'is_lead_reg_form_submitted' => $isLeadRegFormSubmitted,
            'show_lead_reg_form_link' => $showLeadRegFormLink,
            'reg_form_link' => $regFormLink,
            'remarks' => $this->stripHtmlContent($lead->remarks ?? ''),
            'date' => $date,
            'time' => $time,
            'follow_up_date' => $followUpDate,
            'registration_details_status' => $registrationDetailsStatus,
            'can_convert' => $this->canConvertLead($lead),
            'created_at' => $createdAt->format('d-m-Y H:i:s')
        ];
    }


    /**
     * Apply role-based filtering to leads queries
     */
    private function applyRoleBasedFilter($query, $user)
    {
        // Roles that can see all leads (admin, super admin, managers, etc.)
        if ($this->canViewAllLeads($user)) {
            // Can see all leads
            return $query;
        }

        if ($user->is_team_lead) {
            // Team Lead: Can see their own leads + their team members' leads
            $teamId = $user->team_id;
            if ($teamId) {
                $teamMemberIds = User::where('team_id', $teamId)
                    ->where('role_id', 3)
                    ->whereNull('deleted_at')
                    ->pluck('id')
                    ->toArray();
                $teamMemberIds[] = $user->id;
                $query->whereIn('telecaller_id', $teamMemberIds);
            } else {
                // If no team assigned, only show their own leads
                $query->where('telecaller_id', $user->id);
            }
        } elseif ($user->role_id == 3) {
            // Telecaller: Can only see their own leads
            $query->where('telecaller_id', $user->id);
        }

        return $query;
    }

    /**
     * Check if user can view all leads/telecallers
     */
    private function canViewAllLeads($user)
    {
        return $user->role_id == 1 || // Super Admin
            $user->role_id == 2 || // Admin
            $user->is_senior_manager ||
            in_array($user->role_id, [4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14]);
    }

    /**
     * Get telecaller list based on user role (same logic as web)
     */
    private function getTelecallersForUser($user)
    {
        $telecallerQuery = User::select('id', 'name', 'team_id')
            ->where('role_id', 3)
            ->whereNull('deleted_at')
            ->orderBy('name');

        if ($this->canViewAllLeads($user)) {
            return $telecallerQuery->get();
        }

        if ($user->is_team_lead) {
            $teamId = $user->team_id;
            if ($teamId) {
                $teamMemberIds = User::where('team_id', $teamId)
                    ->where('role_id', 3)
                    ->whereNull('deleted_at')
                    ->pluck('id')
                    ->toArray();

                $teamMemberIds[] = $user->id;

                return User::select('id', 'name')
                    ->whereIn('id', array_unique($teamMemberIds))
                    ->orderBy('name')
                    ->get();
            }

            return User::select('id', 'name')
                ->where('id', $user->id)
                ->get();
        }

        if ($user->role_id == 3) {
            return User::select('id', 'name')
                ->where('id', $user->id)
                ->get();
        }

        return collect();
    }

    /**
     * Determine whether a lead can be converted (matches admin view logic)
     */
    private function canConvertLead($lead): int
    {
        if ($lead->is_converted || !$lead->studentDetails) {
            return 0;
        }

        $studentStatus = strtolower($lead->studentDetails->status ?? '');

        return $studentStatus === 'approved' ? 1 : 0;
    }

    /**
     * Get registration details status label for API consumers
     */
    private function getRegistrationDetailsStatus($lead): string
    {
        $status = strtolower($lead->studentDetails->status ?? '');

        return match ($status) {
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => 'Pending',
        };
    }

    /**
     * Remove HTML content from text and normalize whitespace
     */
    private function stripHtmlContent(string $text): string
    {
        $stripped = strip_tags($text);

        // Normalize line endings and collapse consecutive blank lines
        $stripped = preg_replace("/\r\n|\r/", "\n", $stripped);
        $stripped = preg_replace("/\n{2,}/", "\n", $stripped);

        // Replace newlines with a single space and collapse extra whitespace
        $stripped = str_replace("\n", ' ', $stripped);
        $stripped = preg_replace("/[ \t]+/", ' ', $stripped);

        return trim($stripped);
    }
}

