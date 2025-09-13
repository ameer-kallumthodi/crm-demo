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
use App\Models\ConvertedLead;
use App\Helpers\AuthHelper;
use App\Helpers\RoleHelper;
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
        $fromDate = $request->get('date_from', now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', now()->format('Y-m-d'));
        
        $query->byDateRange($fromDate, $toDate);

        if ($request->filled('lead_status_id')) {
            $query->where('lead_status_id', $request->lead_status_id);
        }

        if ($request->filled('lead_source_id')) {
            $query->where('lead_source_id', $request->lead_source_id);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('telecaller_id')) {
            $query->where('telecaller_id', $request->telecaller_id);
        }

        // Add search functionality
        if ($request->filled('search_key')) {
            $searchKey = $request->search_key;
            $query->where(function($q) use ($searchKey) {
                $q->where('title', 'LIKE', "%{$searchKey}%")
                  ->orWhere('phone', 'LIKE', "%{$searchKey}%")
                  ->orWhere('email', 'LIKE', "%{$searchKey}%");
            });
        }

        $currentUser = AuthHelper::getCurrentUser();
        
        // Role-based lead filtering
        if ($currentUser) {
            if (AuthHelper::isTelecaller()) {
                // Telecaller: Can only see their own leads
                $query->where('telecaller_id', AuthHelper::getCurrentUserId());
            } elseif (AuthHelper::isTeamLead()) {
                // Team Lead: Can see their own leads + their team members' leads
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                    // Include current user's ID in the team member IDs
                    $teamMemberIds[] = AuthHelper::getCurrentUserId();
                    $query->whereIn('telecaller_id', $teamMemberIds);
                } else {
                    // If no team assigned, only show their own leads
                    $query->where('telecaller_id', AuthHelper::getCurrentUserId());
                }
            } elseif ($request->filled('telecaller_id') && !AuthHelper::isTelecaller()) {
                // Admin/Super Admin: Can filter by specific telecaller
                $query->where('telecaller_id', $request->telecaller_id);
            }
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
        ))->with('search_key', $request->search_key);
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
            'title' => 'nullable|string|max:255',
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
            'lead_status_id' => 'nullable|exists:lead_statuses,id',
            'lead_source_id' => 'nullable|exists:lead_sources,id',
            'country_id' => 'nullable|exists:countries,id',
            'course_id' => 'nullable|exists:courses,id',
            'team_id' => 'nullable|exists:teams,id',
            'telecaller_id' => 'nullable|exists:users,id',
            'address' => 'nullable|string|max:500',
            'followup_date' => 'nullable|date',
            'add_date' => 'nullable|date',
            'add_time' => 'nullable|date_format:H:i',
            'remarks' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()->with('message_danger', $firstError)->withInput();
        }

        $leadData = $request->all();
        
        // Set default values
        $leadData['lead_status_id'] = $leadData['lead_status_id'] ?? 1;
        $leadData['add_date'] = $leadData['add_date'] ?? date('Y-m-d');
        $leadData['add_time'] = $leadData['add_time'] ?? date('H:i');
        
        $lead = Lead::create($leadData);

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
        $telecallerList = User::where('role_id', 3)->pluck('name', 'id')->toArray();

        return view('admin.leads.show', compact(
            'lead', 'leadStatusList', 'leadSourceList', 'courseName', 'telecallerList'
        ));
    }

    public function ajax_show(Lead $lead)
    {
        $lead->load(['leadStatus', 'leadSource', 'course', 'telecaller', 'leadActivities']);
        
        return view('admin.leads.show-modal', compact('lead'));
    }

    public function status_update(Lead $lead)
    {
        $leadStatuses = LeadStatus::all();
        $lead->load(['leadActivities' => function($query) {
            $query->with(['leadStatus', 'createdBy'])->orderBy('created_at', 'desc');
        }]);
        return view('admin.leads.status-update-modal', compact('lead', 'leadStatuses'));
    }

    public function status_update_submit(Request $request, Lead $lead)
    {
        try {
            $validator = Validator::make($request->all(), [
                'lead_status_id' => 'required|exists:lead_statuses,id',
                'remarks' => 'nullable|string|max:1000',
                'date' => 'required|date',
                'time' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please correct the errors below.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update lead status
            $lead->update([
                'lead_status_id' => $request->lead_status_id,
                'updated_by' => AuthHelper::getCurrentUserId(),
                'is_converted' => $request->lead_status_id == 4 ? true : false,
            ]);

            // Create lead activity
            LeadActivity::create([
                'lead_id' => $lead->id,
                'lead_status_id' => $request->lead_status_id,
                'activity_type' => 'status_update',
                'description' => 'Status updated to ' . $lead->fresh()->leadStatus->title,
                'followup_date' => $request->date,
                'remarks' => $request->remarks,
                'created_by' => AuthHelper::getCurrentUserId(),
                'updated_by' => AuthHelper::getCurrentUserId(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lead status updated successfully!',
                'data' => $lead->fresh(['leadStatus'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the status. Please try again.'
            ], 500);
        }
    }

    public function edit(Lead $lead)
    {
        $telecallers = User::where('role_id', 3)->get();
        $leadStatuses = LeadStatus::all();
        $leadSources = LeadSource::all();
        $countries = Country::all();
        $courses = Course::all();
        $teams = Team::all();
        $country_codes = get_country_code();

        return view('admin.leads.edit', compact(
            'lead', 'telecallers', 'leadStatuses', 'leadSources', 'countries', 'courses', 'teams', 'country_codes'
        ));
    }

    public function ajax_edit(Lead $lead)
    {
        $telecallers = User::where('role_id', 3)->get();
        $leadStatuses = LeadStatus::all();
        $leadSources = LeadSource::all();
        $countries = Country::all();
        $courses = Course::all();
        $teams = Team::all();
        $country_codes = get_country_code();

        return view('admin.leads.edit-modal', compact(
            'lead', 'telecallers', 'leadStatuses', 'leadSources', 'countries', 'courses', 'teams', 'country_codes'
        ));
    }

    public function delete(Lead $lead)
    {
        return view('admin.leads.delete-modal', compact('lead'));
    }

    public function destroy(Lead $lead)
    {
        try {
            // Set deleted_by before deleting
            $lead->deleted_by = AuthHelper::getCurrentUserId();
            $lead->save();
            
            $lead->delete();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lead deleted successfully!'
                ]);
            }
            
            return redirect()->route('leads.index')->with('message_success', 'Lead deleted successfully!');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while deleting the lead. Please try again.'
                ], 500);
            }
            
            return redirect()->back()->with('message_danger', 'An error occurred while deleting the lead. Please try again.');
        }
    }

    public function update(Request $request, Lead $lead)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'nullable|string|max:255',
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
                'lead_status_id' => 'nullable|exists:lead_statuses,id',
                'lead_source_id' => 'nullable|exists:lead_sources,id',
                'country_id' => 'nullable|exists:countries,id',
                'course_id' => 'nullable|exists:courses,id',
                'team_id' => 'nullable|exists:teams,id',
                'telecaller_id' => 'nullable|exists:users,id',
                'address' => 'nullable|string|max:500',
                'followup_date' => 'nullable|date',
                'add_date' => 'nullable|date',
                'add_time' => 'nullable|date_format:H:i',
                'remarks' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please correct the errors below.',
                        'errors' => $validator->errors()
                    ], 422);
                }
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Check for duplicate phone (excluding current lead)
            $existingLead = Lead::where('phone', $request->phone)
                               ->where('id', '!=', $lead->id)
                               ->first();

            if ($existingLead) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Lead with this phone number already exists'
                    ], 422);
                }
                return redirect()->back()
                    ->with('message_danger', 'Lead with this phone number already exists')
                    ->withInput();
            }

            $data = $request->all();
            
            // Set default values
            $data['lead_status_id'] = $data['lead_status_id'] ?? 1;
            $data['add_date'] = $data['add_date'] ?? date('Y-m-d');
            $data['add_time'] = $data['add_time'] ?? date('H:i');
            
            $data['updated_by'] = AuthHelper::getCurrentUserId();
            $data['is_converted'] = $request->lead_status_id == 4 ? true : false;

            if ($lead->update($data)) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Lead updated successfully!',
                        'data' => $lead
                    ]);
                }
                return redirect()->route('leads.index')
                    ->with('message_success', 'Lead updated successfully!');
            }

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong! Please try again.'
                ], 500);
            }
            return redirect()->back()
                ->with('message_danger', 'Something went wrong! Please try again.')
                ->withInput();
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the lead. Please try again.'
                ], 500);
            }
            return redirect()->back()
                ->with('message_danger', 'An error occurred while updating the lead. Please try again.')
                ->withInput();
        }
    }




    public function bulkUploadView()
    {
        $leadStatuses = LeadStatus::where('is_active', true)->get();
        $leadSources = LeadSource::where('is_active', true)->get();
        $courses = Course::where('is_active', true)->get();
        $teams = Team::where('is_active', true)->get();
        $telecallers = User::where('role_id', 3)->where('is_active', true)->get();
        
        return view('admin.leads.bulk-upload', compact(
            'leadStatuses', 'leadSources', 'courses', 'teams', 'telecallers'
        ));
    }

    public function downloadTemplate()
    {
        $filePath = storage_path('app/public/lead-sample.xlsx');
        
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'Template file not found'], 404);
        }
        
        $currentDateTime = now()->format('Y-m-d_H-i-s');
        $filename = "Lead_Bulk_Upload_{$currentDateTime}.xlsx";
        
        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    public function bulkUploadSubmit(Request $request)
    {

        // Handle POST request - process the bulk upload
        // Set execution time limit for bulk operations
        set_time_limit(config('timeout.max_execution_time', 300));
        ini_set('memory_limit', config('timeout.memory_limit', '256M'));
        
        // Try to set upload limits (may not work on all servers)
        ini_set('upload_max_filesize', '4M');
        ini_set('post_max_size', '8M');
        ini_set('max_input_time', '300');

        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls|max:2048',
            'lead_source_id' => 'required|exists:lead_sources,id',
            'lead_status_id' => 'required|exists:lead_statuses,id',
            'course_id' => 'required|exists:courses,id',
            'team_id' => 'required|string',
            'assign_to_all' => 'boolean',
            'telecallers' => 'required_if:assign_to_all,false|array|min:1',
            'telecallers.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please fix the validation errors.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('excel_file');
            
            // Check if file was uploaded successfully
            if (!$file || !$file->isValid()) {
                $errorMessage = 'File upload failed. ';
                if ($file && $file->getError() === UPLOAD_ERR_INI_SIZE) {
                    $errorMessage .= 'File exceeds server upload limit. Maximum file size: 2MB.';
                } elseif ($file && $file->getError() === UPLOAD_ERR_FORM_SIZE) {
                    $errorMessage .= 'File exceeds form upload limit. Maximum file size: 2MB.';
                } else {
                    $errorMessage .= 'Please check file size and try again. Maximum file size: 2MB.';
                }
                
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => ['excel_file' => [$errorMessage]]
                ], 422);
            }
            
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            
            // Check if the worksheet has any data
            if ($highestRow < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Excel file appears to be empty or has no data rows. Please ensure the file contains data starting from row 2.',
                    'errors' => ['excel_file' => ['Excel file is empty or has no data']]
                ], 422);
            }

            // Get telecallers based on assignment type
            if ($request->assign_to_all) {
                // When assigning to all, get telecallers from the selected team or all teams
                if ($request->team_id === 'all') {
                    $telecallers = User::where('role_id', 3)
                        ->where('is_active', true)
                        ->pluck('id')->toArray();
                } else {
                    $telecallers = User::where('team_id', $request->team_id)
                        ->where('role_id', 3)
                        ->where('is_active', true)
                        ->pluck('id')->toArray();
                }
                    
                // Check if team has telecallers
                if (empty($telecallers)) {
                    $message = $request->team_id === 'all' 
                        ? 'No telecallers found in any team. Please assign telecallers manually.'
                        : 'No telecallers found in the selected team. Please select a different team or assign telecallers manually.';
                    
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                        'errors' => ['team_id' => ['No telecallers available']]
                    ], 422);
                }
            } else {
                // When assigning manually, use selected telecallers
                $telecallers = $request->telecallers ?? [];
                
                // Check if telecallers are selected
                if (empty($telecallers)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please select at least one telecaller or choose "Assign to all telecallers in team".',
                        'errors' => ['telecallers' => ['Please select at least one telecaller']]
                    ], 422);
                }
            }

            $telecallerIndex = 0;
            $successCount = 0;
            $duplicateCount = 0;

            // Limit the number of rows to prevent timeout
            $maxRows = min($highestRow, config('timeout.bulk_upload.max_rows', 1000));
            
            for ($row = 2; $row <= $maxRows; $row++) {
                $name = $worksheet->getCell('A' . $row)->getValue();
                $phone = $worksheet->getCell('B' . $row)->getValue();
                $place = $worksheet->getCell('C' . $row)->getValue();
                $remarks = $worksheet->getCell('D' . $row)->getValue();

                if (empty($phone)) continue;

                // Parse phone number to extract country code and phone number using helper
                $phoneData = get_phone_code($phone);
                $code = $phoneData['code'];
                $phoneNumber = $phoneData['phone'];
                
                // If parsing failed, use default country code
                if (empty($code) || empty($phoneNumber)) {
                    $code = '91'; // Default to India
                    $phoneNumber = $phone;
                }

                // Check if lead already exists (check by both code and phone)
                $existingLead = Lead::where('phone', $phoneNumber)
                                  ->where('code', $code)
                                  ->first();
                if ($existingLead) {
                    $duplicateCount++;
                    continue;
                }

                // Ensure we have a valid telecaller index
                $telecallerId = $telecallers[$telecallerIndex] ?? $telecallers[0];
                
                $lead = Lead::create([
                    'title' => $name,
                    'phone' => $phoneNumber,
                    'code' => $code,
                    'place' => $place,
                    'remarks' => $remarks,
                    'lead_source_id' => $request->lead_source_id,
                    'lead_status_id' => $request->lead_status_id,
                    'course_id' => $request->course_id,
                    'telecaller_id' => $telecallerId,
                    'created_by' => AuthHelper::getCurrentUserId(),
                    'updated_by' => AuthHelper::getCurrentUserId(),
                    'is_converted' => false
                ]);

                if ($lead) {
                    $successCount++;
                    
                    // Log activity
                    LeadActivity::create([
                        'lead_id' => $lead->id,
                        'activity_type' => 'bulk_upload',
                        'description' => 'Lead created via bulk upload',
                        'created_by' => AuthHelper::getCurrentUserId(),
                        'created_at' => now()
                    ]);
                    
                    $telecallerIndex = ($telecallerIndex + 1) % count($telecallers);
                }
            }

            $message = "Successfully uploaded {$successCount} leads!";
            if ($duplicateCount > 0) {
                $message .= " {$duplicateCount} duplicates skipped.";
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing file: ' . $e->getMessage()
            ], 500);
        }
    }


    public function statusChange(Request $request, Lead $lead)
    {
        // Handle GET request - return the status change form
        if ($request->isMethod('get')) {
            $leadStatuses = LeadStatus::where('is_active', true)->get();
            return response()->json([
                'success' => true,
                'html' => view('admin.leads.status-change-modal', compact('lead', 'leadStatuses'))->render()
            ]);
        }

        // Handle POST request - process the status change
        $validator = Validator::make($request->all(), [
            'lead_status_id' => 'required|exists:lead_statuses,id',
            'remarks' => 'required|string|max:1000',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }

        try {
            // Update lead status
            $lead->update([
                'lead_status_id' => $request->lead_status_id,
                'remarks' => $request->remarks,
                'updated_by' => AuthHelper::getCurrentUserId()
            ]);

            // Log activity
            LeadActivity::create([
                'lead_id' => $lead->id,
                'activity_type' => 'status_change',
                'description' => 'Lead status changed to: ' . $lead->leadStatus->title,
                'remarks' => $request->remarks,
                'created_by' => AuthHelper::getCurrentUserId(),
                'created_at' => $request->date . ' ' . $request->time
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lead status updated successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating lead status: ' . $e->getMessage()
            ]);
        }
    }

    public function history(Lead $lead)
    {
        $activities = LeadActivity::where('lead_id', $lead->id)
            ->with(['createdBy:id,name'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'html' => view('admin.leads.history-modal', compact('lead', 'activities'))->render()
        ]);
    }

    public function getTelecallersByTeam(Request $request)
    {
        $teamId = $request->get('team_id');
        
        if (!$teamId) {
            return response()->json(['telecallers' => []]);
        }

        if ($teamId === 'all') {
            // Get all telecallers from all teams
            $telecallers = User::where('role_id', 3) // Telecaller role
                              ->where('is_active', true)
                              ->with('team:id,name')
                              ->select('id', 'name', 'email', 'team_id')
                              ->get()
                              ->map(function($user) {
                                  return [
                                      'id' => $user->id,
                                      'name' => $user->name,
                                      'email' => $user->email,
                                      'team_name' => $user->team ? $user->team->name : 'No Team'
                                  ];
                              });
        } else {
            // Get telecallers from specific team
            $telecallers = User::where('team_id', $teamId)
                              ->where('role_id', 3) // Telecaller role
                              ->where('is_active', true)
                              ->select('id', 'name', 'email')
                              ->get();
        }

        return response()->json(['telecallers' => $telecallers]);
    }

    /**
     * Show bulk reassign form
     */
    public function ajaxBulkReassign()
    {
        // Get telecallers based on role
        $telecallerWhere = [];
        if (RoleHelper::is_team_lead() && !RoleHelper::is_admin()) {
            $teamId = User::where('id', AuthHelper::getCurrentUserId())->value('team_id');
            $teamMemberIds = User::where('team_id', $teamId)->pluck('id')->toArray();
            $telecallerWhere['id'] = $teamMemberIds;
        }
        $telecallerWhere['role_id'] = 3; // Telecaller role
        
        $data = [
            'telecallers' => User::where($telecallerWhere)->get(),
            'leadStatuses' => LeadStatus::where('is_active', 1)->get(),
            'leadSources' => LeadSource::where('is_active', 1)->get(),
            'countries' => Country::where('is_active', 1)->get(),
            'courses' => Course::where('is_active', 1)->get(),
        ];

        return view('admin.leads.ajax-bulk-reassign', $data);
    }

    /**
     * Process bulk reassign
     */
    public function bulkReassign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'telecaller_id' => 'required|exists:users,id',
            'lead_source_id' => 'required|exists:lead_sources,id',
            'lead_status_id' => 'required|exists:lead_statuses,id',
            'from_telecaller_id' => 'required|exists:users,id',
            'lead_from_date' => 'required|date',
            'lead_to_date' => 'required|date',
            'lead_id' => 'required|array|min:1',
            'lead_id.*' => 'exists:leads,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $successCount = 0;
        foreach ($request->lead_id as $leadId) {
            $lead = Lead::find($leadId);
            if ($lead) {
                $lead->update([
                    'telecaller_id' => $request->telecaller_id,
                    'lead_source_id' => $request->lead_source_id,
                    'lead_status_id' => $request->lead_status_id,
                    'updated_by' => AuthHelper::getCurrentUserId(),
                ]);
                $successCount++;
            }
        }

        return redirect()->back()->with('message_success', "Successfully reassigned {$successCount} leads!");
    }

    /**
     * Show bulk delete form
     */
    public function ajaxBulkDelete()
    {
        $data = [
            'telecallers' => User::where('role_id', 3)->get(),
            'leadStatuses' => LeadStatus::where('is_active', 1)->get(),
            'leadSources' => LeadSource::where('is_active', 1)->get(),
            'countries' => Country::where('is_active', 1)->get(),
            'courses' => Course::where('is_active', 1)->get(),
        ];

        return view('admin.leads.ajax-bulk-delete', $data);
    }

    /**
     * Process bulk delete
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'telecaller_id' => 'required|exists:users,id',
            'lead_date' => 'required|date',
            'lead_source_id' => 'required|exists:lead_sources,id',
            'lead_id' => 'required|array|min:1',
            'lead_id.*' => 'exists:leads,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $successCount = 0;
        foreach ($request->lead_id as $leadId) {
            $lead = Lead::find($leadId);
            if ($lead) {
                $lead->deleted_by = AuthHelper::getCurrentUserId();
                $lead->save();
                $lead->delete();
                $successCount++;
            }
        }

        return redirect()->back()->with('message_success', "Successfully deleted {$successCount} leads!");
    }

    /**
     * Show bulk convert form
     */
    public function ajaxBulkConvert()
    {
        $data = [
            'telecallers' => User::where('role_id', 3)->get(),
            'leadStatuses' => LeadStatus::where('is_active', 1)->get(),
            'leadSources' => LeadSource::where('is_active', 1)->get(),
            'countries' => Country::where('is_active', 1)->get(),
            'courses' => Course::where('is_active', 1)->get(),
        ];

        return view('admin.leads.ajax-bulk-convert', $data);
    }

    /**
     * Process bulk convert
     */
    public function bulkConvert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'telecaller_id' => 'required|exists:users,id',
            'lead_date' => 'required|date',
            'lead_source_id' => 'required|exists:lead_sources,id',
            'lead_id' => 'required|array|min:1',
            'lead_id.*' => 'exists:leads,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $successCount = 0;
        foreach ($request->lead_id as $leadId) {
            $lead = Lead::find($leadId);
            if ($lead) {
                // Create converted lead record with basic info
                ConvertedLead::create([
                    'lead_id' => $leadId,
                    'name' => $lead->title,
                    'code' => $lead->code,
                    'phone' => $lead->phone,
                    'email' => $lead->email,
                    'remarks' => $request->remarks ?? 'Converted via bulk operation',
                    'created_by' => AuthHelper::getCurrentUserId(),
                ]);

                // Update lead as converted
                $lead->update([
                    'is_converted' => 1,
                    'updated_by' => AuthHelper::getCurrentUserId(),
                ]);
                
                $successCount++;
            }
        }

        return redirect()->back()->with('message_success', "Successfully converted {$successCount} leads!");
    }

    /**
     * Get leads by source for bulk operations
     */
    public function getLeadsBySource(Request $request)
    {
        $leads = Lead::where('lead_source_id', $request->lead_source_id)
                    ->where('telecaller_id', $request->tele_caller_id)
                    ->whereDate('created_at', $request->created_at)
                    ->with(['leadStatus', 'leadSource', 'telecaller'])
                    ->get();

        return view('admin.leads.partials.leads-table-rows', compact('leads'));
    }

    /**
     * Get leads by source for reassign operations
     */
    public function getLeadsBySourceReassign(Request $request)
    {
        $leads = Lead::where('lead_source_id', $request->lead_source_id)
                    ->where('telecaller_id', $request->tele_caller_id)
                    ->where('lead_status_id', $request->lead_status_id)
                    ->whereBetween('created_at', [$request->from_date, $request->to_date])
                    ->with(['leadStatus', 'leadSource', 'telecaller', 'course'])
                    ->get();

        return view('admin.leads.partials.leads-table-rows-reassign', compact('leads'));
    }

    /**
     * Show convert lead form
     */
    public function convert(Lead $lead)
    {
        $courses = Course::where('is_active', true)->get();
        $academic_assistants = User::where('role_id', 13)->where('is_active', true)->get();
        $country_codes = get_country_code();

        return view('admin.leads.convert-modal', compact(
            'lead', 'courses', 'academic_assistants', 'country_codes'
        ));
    }

    /**
     * Process lead conversion
     */
    public function convertSubmit(Request $request, Lead $lead)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'course_id' => 'required|exists:courses,id',
            'academic_assistant_id' => 'required|exists:users,id',
            'remarks' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please correct the errors below.',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Create converted lead record
            $convertedLead = ConvertedLead::create([
                'lead_id' => $lead->id,
                'name' => $request->name,
                'code' => $request->code,
                'phone' => $request->phone,
                'email' => $request->email,
                'course_id' => $request->course_id,
                'academic_assistant_id' => $request->academic_assistant_id,
                'candidate_status_id' => 1,
                'remarks' => $request->remarks,
                'created_by' => AuthHelper::getCurrentUserId(),
                'updated_by' => AuthHelper::getCurrentUserId(),
            ]);

            // Update lead as converted
            $lead->update([
                'is_converted' => true,
                'updated_by' => AuthHelper::getCurrentUserId(),
            ]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lead converted successfully!',
                    'data' => $convertedLead
                ]);
            }

            return redirect()->route('leads.index')
                ->with('message_success', 'Lead converted successfully!');

        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while converting the lead. Please try again.'
                ], 500);
            }

            return redirect()->back()
                ->with('message_danger', 'An error occurred while converting the lead. Please try again.')
                ->withInput();
        }
    }


}