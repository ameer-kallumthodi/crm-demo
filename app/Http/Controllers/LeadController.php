<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\LeadSource;
use App\Models\Country;
use App\Models\Course;
use App\Models\Team;
use App\Models\User;
use App\Models\LeadActivity;
use App\Helpers\AuthHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        // Set execution time limit for this operation
        set_time_limit(config('timeout.max_execution_time', 300));
        
        $query = Lead::with(['leadStatus:id,title', 'leadSource:id,title', 'course:id,title', 'telecaller:id,name'])
                    ->notConverted()
                    ->notDropped();

        // Apply filters
        $fromDate = $request->get('from_date', now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));
        
        $query->byDateRange($fromDate, $toDate);

        if ($request->filled('lead_status')) {
            $query->where('lead_status_id', $request->lead_status);
        }

        if ($request->filled('lead_source')) {
            $query->where('lead_source_id', $request->lead_source);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        $currentUser = AuthHelper::getCurrentUser();
        if ($request->filled('telecaller_id') && $currentUser && !$currentUser->hasRole('telecaller')) {
            $query->where('telecaller_id', $request->telecaller_id);
        } elseif ($currentUser && $currentUser->hasRole('telecaller')) {
            $query->where('telecaller_id', AuthHelper::getCurrentUserId());
        }

        // Add pagination to prevent loading too many records
        $leads = $query->orderBy('id', 'desc')->paginate(50);

        // Get filter options (optimized with select only needed fields)
        $leadStatuses = LeadStatus::select('id', 'title')->get();
        $leadSources = LeadSource::select('id', 'title')->get();
        $countries = Country::select('id', 'title')->get();
        $courses = Course::select('id', 'title')->get();
        $telecallers = User::select('id', 'name')->where('role_id', 3)->get();

        // Create lookup arrays
        $leadStatusList = $leadStatuses->pluck('title', 'id')->toArray();
        $leadSourceList = $leadSources->pluck('title', 'id')->toArray();
        $courseName = $courses->pluck('title', 'id')->toArray();
        $telecallerList = $telecallers->pluck('name', 'id')->toArray();

        return view('admin.leads.index', compact(
            'leads', 'leadStatuses', 'leadSources', 'countries', 'courses', 'telecallers',
            'leadStatusList', 'leadSourceList', 'courseName', 'telecallerList',
            'fromDate', 'toDate'
        ));
    }

    public function create()
    {
        $telecallers = User::where('role_id', 3)->get();
        $leadStatuses = LeadStatus::where('is_active', true)->get();
        $leadSources = LeadSource::where('is_active', true)->get();
        $countries = Country::where('is_active', true)->get();
        $courses = Course::where('is_active', true)->get();
        $teams = Team::all();
        $country_codes = get_country_code();

        return view('admin.leads.create', compact(
            'telecallers', 'leadStatuses', 'leadSources', 'countries', 'courses', 'teams', 'country_codes'
        ));
    }

    public function ajax_add()
    {
        $telecallers = User::where('role_id', 3)->get();
        $leadStatuses = LeadStatus::where('is_active', true)->get();
        $leadSources = LeadSource::where('is_active', true)->get();
        $countries = Country::where('is_active', true)->get();
        $courses = Course::where('is_active', true)->get();
        $teams = Team::all();
        $country_codes = get_country_code();

        return view('admin.leads.add', compact(
            'telecallers', 'leadStatuses', 'leadSources', 'countries', 'courses', 'teams', 'country_codes'
        ));
    }

    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'code' => 'nullable|string|max:10',
            'whatsapp_code' => 'nullable|string|max:10',
            'whatsapp' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'age' => 'nullable|integer|min:1|max:999',
            'place' => 'nullable|string|max:255',
            'qualification' => 'nullable|string|max:255',
            'interest_status' => 'nullable|in:1,2,3',
            'lead_status_id' => 'required|exists:lead_statuses,id',
            'lead_source_id' => 'required|exists:lead_sources,id',
            'country_id' => 'required|exists:countries,id',
            'course_id' => 'nullable|exists:courses,id',
            'team_id' => 'nullable|exists:teams,id',
            'telecaller_id' => 'nullable|exists:users,id',
            'address' => 'nullable|string|max:500',
            'followup_date' => 'nullable|date',
            'remarks' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()->with('message_danger', $firstError)->withInput();
        }

        $lead = Lead::create($request->all());

        return redirect()->route('leads.index')->with('message_success', 'Lead created successfully!');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'code' => 'nullable|string|max:10',
            'whatsapp_code' => 'nullable|string|max:10',
            'whatsapp' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'age' => 'nullable|integer|min:1|max:999',
            'place' => 'nullable|string|max:255',
            'qualification' => 'nullable|string|max:255',
            'interest_status' => 'nullable|in:1,2,3',
            'country_id' => 'required|exists:countries,id',
            'lead_status_id' => 'required|exists:lead_statuses,id',
            'lead_source_id' => 'required|exists:lead_sources,id',
            'team_id' => 'nullable|exists:teams,id',
            'telecaller_id' => 'nullable|exists:users,id',
            'course_id' => 'nullable|exists:courses,id',
            'address' => 'nullable|string|max:500',
            'followup_date' => 'nullable|date',
            'remarks' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('message_danger', $validator->errors()->first())
                ->withInput();
        }

        // Check for duplicate phone
        $existingLead = Lead::where('phone', $request->phone)
                           ->where('code', $request->code)
                           ->first();

        if ($existingLead) {
            return redirect()->back()
                ->with('message_danger', 'Lead with this phone number already exists')
                ->withInput();
        }

        $data = $request->all();
        $data['created_by'] = AuthHelper::getCurrentUserId();
        $data['updated_by'] = AuthHelper::getCurrentUserId();
        $data['is_converted'] = $request->lead_status_id == 4 ? true : false;

        $lead = Lead::create($data);

        if ($lead) {
            // Create lead activity
            LeadActivity::create([
                'lead_id' => $lead->id,
                'lead_status_id' => $request->lead_status_id,
                'followup_date' => $request->followup_date,
                'remarks' => $request->remarks,
                'created_by' => AuthHelper::getCurrentUserId(),
                'updated_by' => AuthHelper::getCurrentUserId()
            ]);

            return redirect()->route('leads.index')
                ->with('message_success', 'Lead added successfully!');
        }

        return redirect()->back()
            ->with('message_danger', 'Something went wrong! Please try again.')
            ->withInput();
    }

    public function show(Lead $lead)
    {
        $lead->load(['leadStatus', 'leadSource', 'course', 'telecaller', 'leadActivities']);
        
        $leadStatusList = LeadStatus::pluck('title', 'id')->toArray();
        $leadSourceList = LeadSource::pluck('title', 'id')->toArray();
        $courseName = Course::pluck('title', 'id')->toArray();
        $telecallerList = User::where('role_id', 6)->pluck('name', 'id')->toArray();

        return view('admin.leads.show', compact(
            'lead', 'leadStatusList', 'leadSourceList', 'courseName', 'telecallerList'
        ));
    }

    public function edit(Lead $lead)
    {
        $telecallers = User::where('role_id', 3)->get();
        $leadStatuses = LeadStatus::all();
        $leadSources = LeadSource::all();
        $countries = Country::all();
        $courses = Course::all();
        $country_codes = get_country_code();

        return view('admin.leads.edit', compact(
            'lead', 'telecallers', 'leadStatuses', 'leadSources', 'countries', 'courses', 'country_codes'
        ));
    }

    public function update(Request $request, Lead $lead)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'code' => 'nullable|string|max:10',
            'whatsapp_code' => 'nullable|string|max:10',
            'whatsapp' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'age' => 'nullable|integer|min:1|max:999',
            'place' => 'nullable|string|max:255',
            'qualification' => 'nullable|string|max:255',
            'interest_status' => 'nullable|in:1,2,3',
            'lead_status_id' => 'required|exists:lead_statuses,id',
            'lead_source_id' => 'required|exists:lead_sources,id',
            'country_id' => 'required|exists:countries,id',
            'course_id' => 'nullable|exists:courses,id',
            'team_id' => 'nullable|exists:teams,id',
            'telecaller_id' => 'nullable|exists:users,id',
            'address' => 'nullable|string|max:500',
            'followup_date' => 'nullable|date',
            'remarks' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check for duplicate phone (excluding current lead)
        $existingLead = Lead::where('phone', $request->phone)
                           ->where('code', $request->code)
                           ->where('id', '!=', $lead->id)
                           ->first();

        if ($existingLead) {
            return redirect()->back()
                ->with('message_danger', 'Lead with this phone number already exists')
                ->withInput();
        }

        $data = $request->all();
        $data['updated_by'] = AuthHelper::getCurrentUserId();
        $data['is_converted'] = $request->lead_status_id == 4 ? true : false;

        if ($lead->update($data)) {
            return redirect()->route('leads.index')
                ->with('message_success', 'Lead updated successfully!');
        }

        return redirect()->back()
            ->with('message_danger', 'Something went wrong! Please try again.')
            ->withInput();
    }

    public function destroy(Lead $lead)
    {
        if ($lead->delete()) {
            return redirect()->route('leads.index')
                ->with('message_success', 'Lead deleted successfully!');
        }

        return redirect()->back()
            ->with('message_danger', 'Something went wrong! Please try again.');
    }

    public function updateStatus(Request $request, Lead $lead)
    {
        $validator = Validator::make($request->all(), [
            'lead_status_id' => 'required|exists:lead_statuses,id',
            'followup_date' => 'nullable|date',
            'remarks' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $lead->updateLeadStatus(
            $request->lead_status_id,
            $request->remarks,
            $request->followup_date
        );

        return redirect()->route('leads.index')
            ->with('message_success', 'Lead status updated successfully!');
    }

    public function bulkReassign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'telecaller_id' => 'required|exists:users,id',
            'lead_ids' => 'required|array|min:1',
            'lead_ids.*' => 'exists:leads,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $leadIds = $request->lead_ids;
        $telecallerId = $request->telecaller_id;

        foreach ($leadIds as $leadId) {
            $lead = Lead::find($leadId);
            if ($lead) {
                $lead->reassignToTelecaller($telecallerId, $lead->telecaller_id);
            }
        }

        return redirect()->route('leads.index')
            ->with('message_success', 'Leads reassigned successfully!');
    }

    public function bulkUpload(Request $request)
    {
        // Handle GET request - return the bulk upload form
        if ($request->isMethod('get')) {
            $leadStatuses = LeadStatus::where('is_active', true)->get();
            $leadSources = LeadSource::where('is_active', true)->get();
            $courses = Course::where('is_active', true)->get();
            $telecallers = User::where('role_id', 3)->get();
            
            return view('admin.leads.bulk-upload', compact(
                'leadStatuses', 'leadSources', 'courses', 'telecallers'
            ));
        }

        // Handle POST request - process the bulk upload
        // Set execution time limit for bulk operations
        set_time_limit(config('timeout.max_execution_time', 300));
        ini_set('memory_limit', config('timeout.memory_limit', '256M'));

        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240',
            'lead_source_id' => 'required|exists:lead_sources,id',
            'course_id' => 'required|exists:courses,id',
            'telecallers' => 'required|array|min:1',
            'telecallers.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        try {
            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();

            $telecallers = $request->telecallers;
            $telecallerIndex = 0;
            $successCount = 0;

            // Limit the number of rows to prevent timeout
            $maxRows = min($highestRow, config('timeout.bulk_upload.max_rows', 1000));
            
            for ($row = 2; $row <= $maxRows; $row++) {
                $title = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                $phone = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                $place = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                $remarks = $worksheet->getCellByColumnAndRow(4, $row)->getValue();

                if (empty($phone)) continue;

                // Check if lead already exists
                $existingLead = Lead::where('phone', $phone)->first();
                if ($existingLead) continue;

                $lead = Lead::create([
                    'title' => $title,
                    'phone' => $phone,
                    'place' => $place,
                    'remarks' => $remarks,
                    'lead_source_id' => $request->lead_source_id,
                    'course_id' => $request->course_id,
                    'lead_status_id' => 1, // New lead status
                    'telecaller_id' => $telecallers[$telecallerIndex],
                    'created_by' => AuthHelper::getCurrentUserId(),
                    'updated_by' => AuthHelper::getCurrentUserId(),
                    'is_converted' => false
                ]);

                if ($lead) {
                    $successCount++;
                    $telecallerIndex = ($telecallerIndex + 1) % count($telecallers);
                }
            }

            return redirect()->route('leads.index')
                ->with('message_success', "{$successCount} leads uploaded successfully!");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('message_danger', 'Error processing file: ' . $e->getMessage());
        }
    }


    public function getTelecallersByTeam(Request $request)
    {
        $teamId = $request->get('team_id');
        
        if (!$teamId) {
            return response()->json(['telecallers' => []]);
        }

        $telecallers = User::where('team_id', $teamId)
                          ->where('role_id', 6) // Assuming role_id 6 is for telecallers
                          ->where('is_active', true)
                          ->select('id', 'name', 'email')
                          ->get();

        return response()->json(['telecallers' => $telecallers]);
    }
}