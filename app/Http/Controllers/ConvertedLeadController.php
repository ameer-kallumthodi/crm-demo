<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConvertedLead;
use App\Models\Lead;
use App\Helpers\AuthHelper;
use App\Helpers\RoleHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Storage;
use App\Models\ConvertedLeadIdCard;
use Illuminate\Support\Facades\Mail;
use App\Mail\IdCardNotification;
use Carbon\Carbon;

class ConvertedLeadController extends Controller
{
    /**
     * Display a listing of converted leads
     */
    public function index(Request $request)
    {
        $query = ConvertedLead::with(['lead', 'course', 'academicAssistant', 'createdBy', 'subject', 'studentDetails']);

        // Apply role-based filtering
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            // General Manager: Can see ALL converted leads (no filter)
            if (RoleHelper::is_general_manager()) {
                // No filtering
            // Check team lead next
            } elseif (RoleHelper::is_team_lead()) {
                // Team Lead: Can see converted leads from their team
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    $query->whereHas('lead', function($q) use ($teamMemberIds) {
                        $q->whereIn('telecaller_id', $teamMemberIds);
                    });
                } else {
                    // If no team assigned, only show their own leads
                    $query->whereHas('lead', function($q) {
                        $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                    });
                }
            } elseif (RoleHelper::is_admission_counsellor()) {
                // Admission Counsellor: Can see ALL converted leads
                // No additional filtering needed - show all
            } elseif (RoleHelper::is_academic_assistant()) {
                // Academic Assistant: Can see ALL converted leads
                // No additional filtering needed - show all
            } elseif (RoleHelper::is_telecaller()) {
                // Telecaller: Can only see converted leads from leads assigned to them
                $query->whereHas('lead', function($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
            } elseif (RoleHelper::is_support_team()) {
                // Support Team: Only see academically verified leads
                $query->where('is_academic_verified', 1);
            } elseif (RoleHelper::is_mentor()) {
                // Mentor: Filter by admission_batch_id where mentor_id matches
                // Also filter by is_support_verified = 1 and course condition
                $mentorAdmissionBatchIds = \App\Models\AdmissionBatch::where('mentor_id', AuthHelper::getCurrentUserId())
                    ->pluck('id')
                    ->toArray();
                
                if (!empty($mentorAdmissionBatchIds)) {
                    $query->whereIn('admission_batch_id', $mentorAdmissionBatchIds)
                          ->where('is_support_verified', 1);
                } else {
                    // If mentor has no admission batches, return empty result
                    $query->whereRaw('1 = 0');
                }
            }
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('register_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        if ($request->filled('admission_batch_id')) {
            $query->where('admission_batch_id', $request->admission_batch_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('reg_fee')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('reg_fee', $request->reg_fee);
            });
        }

        if ($request->filled('exam_fee')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('exam_fee', $request->exam_fee);
            });
        }

        if ($request->filled('id_card')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('id_card', $request->id_card);
            });
        }

        if ($request->filled('tma')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('tma', $request->tma);
            });
        }

        // Apply default 7-day date filtering if no dates are provided
        $fromDate = $request->get('date_from', now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', now()->format('Y-m-d'));
        
        $query->whereDate('created_at', '>=', $fromDate);
        $query->whereDate('created_at', '<=', $toDate);

        $convertedLeads = $query->orderBy('created_at', 'desc')->get();

        // Get filter data
        $courses = \App\Models\Course::where('is_active', 1)->get();
        $batches = \App\Models\Batch::where('is_active', 1)->get();
        $admission_batches = \App\Models\AdmissionBatch::where('is_active', 1)->get();

        // Country codes for inline phone editor
        $country_codes = get_country_code();

        return view('admin.converted-leads.index', compact('convertedLeads', 'courses', 'batches', 'admission_batches', 'country_codes', 'fromDate', 'toDate'));
    }

    /**
     * Display NIOS converted leads (course_id = 1)
     */
    public function niosIndex(Request $request)
    {
        $query = ConvertedLead::with(['lead', 'course', 'academicAssistant', 'createdBy', 'batch', 'admissionBatch', 'subject', 'studentDetails'])
            ->where('course_id', 1);

        // Apply role-based filtering
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_general_manager()) {
                // No filtering
            } elseif (RoleHelper::is_team_lead()) {
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    $query->whereHas('lead', function($q) use ($teamMemberIds) {
                        $q->whereIn('telecaller_id', $teamMemberIds);
                    });
                } else {
                    $query->whereHas('lead', function($q) {
                        $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                    });
                }
            } elseif (RoleHelper::is_admission_counsellor()) {
                // Can see all
            } elseif (RoleHelper::is_academic_assistant()) {
                // Can see all
            } elseif (RoleHelper::is_telecaller()) {
                $query->whereHas('lead', function($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
            }
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('register_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        if ($request->filled('admission_batch_id')) {
            $query->where('admission_batch_id', $request->admission_batch_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('reg_fee')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('reg_fee', $request->reg_fee);
            });
        }

        if ($request->filled('exam_fee')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('exam_fee', $request->exam_fee);
            });
        }

        if ($request->filled('id_card')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('id_card', $request->id_card);
            });
        }

        if ($request->filled('tma')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('tma', $request->tma);
            });
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
        $batches = \App\Models\Batch::where('course_id', 1)->where('is_active', 1)->get();
        $admission_batches = \App\Models\AdmissionBatch::where('is_active', 1)->get();
        $country_codes = get_country_code();

        return view('admin.converted-leads.nios-index', compact('convertedLeads', 'courses', 'batches', 'admission_batches', 'country_codes'));
    }

    /**
     * Display BOSSE converted leads (course_id = 2)
     */
    public function bosseIndex(Request $request)
    {
        $query = ConvertedLead::with(['lead', 'course', 'academicAssistant', 'createdBy', 'batch', 'admissionBatch', 'subject', 'studentDetails'])
            ->where('course_id', 2);

        // Apply role-based filtering
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_general_manager()) {
                // No filtering
            } elseif (RoleHelper::is_team_lead()) {
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    $query->whereHas('lead', function($q) use ($teamMemberIds) {
                        $q->whereIn('telecaller_id', $teamMemberIds);
                    });
                } else {
                    $query->whereHas('lead', function($q) {
                        $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                    });
                }
            } elseif (RoleHelper::is_admission_counsellor()) {
                // Can see all
            } elseif (RoleHelper::is_academic_assistant()) {
                // Can see all
            } elseif (RoleHelper::is_telecaller()) {
                $query->whereHas('lead', function($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
            }
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('register_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        if ($request->filled('admission_batch_id')) {
            $query->where('admission_batch_id', $request->admission_batch_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('reg_fee')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('reg_fee', $request->reg_fee);
            });
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
        $batches = \App\Models\Batch::where('course_id', 2)->where('is_active', 1)->get();
        $admission_batches = \App\Models\AdmissionBatch::where('is_active', 1)->get();
        $country_codes = get_country_code();

        return view('admin.converted-leads.bosse-index', compact('convertedLeads', 'courses', 'batches', 'admission_batches', 'country_codes'));
    }

    /**
     * Display Hotel Management converted leads (course_id = 8)
     */
    public function hotelManagementIndex(Request $request)
    {
        $query = ConvertedLead::with(['lead', 'course', 'academicAssistant', 'createdBy', 'subject', 'studentDetails'])
            ->where('course_id', 8);

        // Apply role-based filtering
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_team_lead()) {
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    $query->whereHas('lead', function($q) use ($teamMemberIds) {
                        $q->whereIn('telecaller_id', $teamMemberIds);
                    });
                } else {
                    $query->whereHas('lead', function($q) {
                        $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                    });
                }
            } elseif (RoleHelper::is_admission_counsellor()) {
                // Can see all
            } elseif (RoleHelper::is_academic_assistant()) {
                // Can see all
            } elseif (RoleHelper::is_telecaller()) {
                $query->whereHas('lead', function($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
            }
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('register_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('app')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('app', $request->app);
            });
        }

        if ($request->filled('group')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('group', $request->group);
            });
        }

        if ($request->filled('interview')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('interview', $request->interview);
            });
        }

        if ($request->filled('howmany_interview')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('howmany_interview', $request->howmany_interview);
            });
        }

        // Get paginated results
        $convertedLeads = $query->orderBy('created_at', 'desc')->paginate(25);

        // Get filter data
        $courses = \App\Models\Course::where('is_active', 1)->get();
        $batches = \App\Models\Batch::where('course_id', 8)->where('is_active', 1)->get();
        $admission_batches = \App\Models\AdmissionBatch::where('is_active', 1)->get();
        $country_codes = get_country_code();

        return view('admin.converted-leads.hotel-management-index', compact('convertedLeads', 'courses', 'batches', 'admission_batches', 'country_codes'));
    }

    /**
     * Display GMVSS converted leads (course_id = 16)
     */
    public function gmvssIndex(Request $request)
    {
        $query = ConvertedLead::with(['lead.studentDetails', 'course', 'academicAssistant', 'createdBy', 'batch', 'admissionBatch', 'subject', 'studentDetails.registrationLink'])
            ->where('course_id', 16);

        // Apply role-based filtering
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_team_lead()) {
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    $query->whereHas('lead', function($q) use ($teamMemberIds) {
                        $q->whereIn('telecaller_id', $teamMemberIds);
                    });
                } else {
                    $query->whereHas('lead', function($q) {
                        $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                    });
                }
            } elseif (RoleHelper::is_admission_counsellor()) {
                // Can see all
            } elseif (RoleHelper::is_academic_assistant()) {
                // Can see all
            } elseif (RoleHelper::is_telecaller()) {
                $query->whereHas('lead', function($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
            }
        }
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('register_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        if ($request->filled('admission_batch_id')) {
            $query->where('admission_batch_id', $request->admission_batch_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('registration_link_id')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('registration_link_id', $request->registration_link_id);
            });
        }

        if ($request->filled('certificate_status')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('certificate_status', $request->certificate_status);
            });
        }

        $convertedLeads = $query->orderBy('created_at', 'desc')->get();
        
        $courses = \App\Models\Course::all();
        $batches = \App\Models\Batch::where('course_id', 16)->get();
        $admission_batches = \App\Models\AdmissionBatch::where('is_active', 1)->get();
        $country_codes = get_country_code();
        $registration_links = \App\Models\RegistrationLink::all();

        return view('admin.converted-leads.gmvss-index', compact('convertedLeads', 'courses', 'batches', 'admission_batches', 'country_codes', 'registration_links'));
    }

    /**
     * Display AI with Python converted leads (course_id = 10)
     */
    public function aiPythonIndex(Request $request)
    {
        $query = ConvertedLead::with(['lead', 'course', 'academicAssistant', 'createdBy', 'subject', 'studentDetails'])
            ->where('course_id', 10);

        // Apply role-based filtering
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_team_lead()) {
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    $query->whereHas('lead', function($q) use ($teamMemberIds) {
                        $q->whereIn('telecaller_id', $teamMemberIds);
                    });
                } else {
                    $query->whereHas('lead', function($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
                }
            } elseif (RoleHelper::is_admission_counsellor()) {
                // Can see all
            } elseif (RoleHelper::is_academic_assistant()) {
                // Can see all
            } elseif (RoleHelper::is_telecaller()) {
                $query->whereHas('lead', function($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
            }
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('register_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('call_status')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('call_status', $request->call_status);
            });
        }

        if ($request->filled('class_information')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('class_information', $request->class_information);
            });
        }

        if ($request->filled('orientation_class_status')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('orientation_class_status', $request->orientation_class_status);
            });
        }

        if ($request->filled('whatsapp_group_status')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('whatsapp_group_status', $request->whatsapp_group_status);
            });
        }

        if ($request->filled('class_status')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('class_status', $request->class_status);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        // Get all results for DataTable
        $convertedLeads = $query->orderBy('created_at', 'desc')->get();

        // Get filter data
        $courses = \App\Models\Course::where('is_active', 1)->get();
        $batches = \App\Models\Batch::where('course_id', 10)->where('is_active', 1)->get();
        $admission_batches = \App\Models\AdmissionBatch::where('is_active', 1)->get();
        $country_codes = get_country_code();

        return view('admin.converted-leads.ai-python-index', compact('convertedLeads', 'courses', 'batches', 'admission_batches', 'country_codes'));
    }

    /**
     * Display Digital Marketing converted leads (course_id = 11)
     */
    public function digitalMarketingIndex(Request $request)
    {
        $query = ConvertedLead::with(['lead', 'course', 'academicAssistant', 'createdBy', 'subject', 'studentDetails'])
            ->where('course_id', 11);

        // Apply role-based filtering
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_team_lead()) {
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    $query->whereHas('lead', function($q) use ($teamMemberIds) {
                        $q->whereIn('telecaller_id', $teamMemberIds);
                    });
                } else {
                    $query->whereHas('lead', function($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
                }
            } elseif (RoleHelper::is_admission_counsellor()) {
                // Can see all
            } elseif (RoleHelper::is_academic_assistant()) {
                // Can see all
            } elseif (RoleHelper::is_telecaller()) {
                $query->whereHas('lead', function($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
            }
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('register_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('call_status')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('call_status', $request->call_status);
            });
        }

        if ($request->filled('class_information')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('class_information', $request->class_information);
            });
        }

        if ($request->filled('orientation_class_status')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('orientation_class_status', $request->orientation_class_status);
            });
        }

        if ($request->filled('whatsapp_group_status')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('whatsapp_group_status', $request->whatsapp_group_status);
            });
        }

        if ($request->filled('class_status')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('class_status', $request->class_status);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        // Get all results for DataTable
        $convertedLeads = $query->orderBy('created_at', 'desc')->get();

        // Get filter data
        $courses = \App\Models\Course::where('is_active', 1)->get();
        $batches = \App\Models\Batch::where('course_id', 11)->where('is_active', 1)->get();
        $admission_batches = \App\Models\AdmissionBatch::where('is_active', 1)->get();
        $country_codes = get_country_code();

        return view('admin.converted-leads.digital-marketing-index', compact('convertedLeads', 'courses', 'batches', 'admission_batches', 'country_codes'));
    }

    /**
     * Display AI Automation converted leads (course_id = 12)
     */
    public function aiAutomationIndex(Request $request)
    {
        $query = ConvertedLead::with(['lead', 'course', 'academicAssistant', 'createdBy', 'subject', 'studentDetails'])
            ->where('course_id', 12);

        // Apply role-based filtering
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_team_lead()) {
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    $query->whereHas('lead', function($q) use ($teamMemberIds) {
                        $q->whereIn('telecaller_id', $teamMemberIds);
                    });
                } else {
                    $query->whereHas('lead', function($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
                }
            } elseif (RoleHelper::is_admission_counsellor()) {
                // Can see all
            } elseif (RoleHelper::is_academic_assistant()) {
                // Can see all
            } elseif (RoleHelper::is_telecaller()) {
                $query->whereHas('lead', function($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
            }
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('register_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('call_status')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('call_status', $request->call_status);
            });
        }

        if ($request->filled('class_information')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('class_information', $request->class_information);
            });
        }

        if ($request->filled('orientation_class_status')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('orientation_class_status', $request->orientation_class_status);
            });
        }

        if ($request->filled('whatsapp_group_status')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('whatsapp_group_status', $request->whatsapp_group_status);
            });
        }

        if ($request->filled('class_status')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('class_status', $request->class_status);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        // Get all results for DataTable
        $convertedLeads = $query->orderBy('created_at', 'desc')->get();

        // Get filter data
        $courses = \App\Models\Course::where('is_active', 1)->get();
        $batches = \App\Models\Batch::where('course_id', 12)->where('is_active', 1)->get();
        $admission_batches = \App\Models\AdmissionBatch::where('is_active', 1)->get();
        $country_codes = get_country_code();

        return view('admin.converted-leads.ai-automation-index', compact('convertedLeads', 'courses', 'batches', 'admission_batches', 'country_codes'));
    }

    /**
     * Display Web Development & Designing converted leads (course_id = 13)
     */
    public function webDevIndex(Request $request)
    {
        $query = ConvertedLead::with(['lead', 'course', 'academicAssistant', 'createdBy', 'subject', 'studentDetails'])
            ->where('course_id', 13);

        // Apply role-based filtering
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_team_lead()) {
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    $query->whereHas('lead', function($q) use ($teamMemberIds) {
                        $q->whereIn('telecaller_id', $teamMemberIds);
                    });
                } else {
                    $query->whereHas('lead', function($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
                }
            } elseif (RoleHelper::is_admission_counsellor()) {
                // Can see all
            } elseif (RoleHelper::is_academic_assistant()) {
                // Can see all
            } elseif (RoleHelper::is_telecaller()) {
                $query->whereHas('lead', function($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
            }
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('register_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('call_status')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('call_status', $request->call_status);
            });
        }

        if ($request->filled('class_information')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('class_information', $request->class_information);
            });
        }

        if ($request->filled('orientation_class_status')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('orientation_class_status', $request->orientation_class_status);
            });
        }

        if ($request->filled('whatsapp_group_status')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('whatsapp_group_status', $request->whatsapp_group_status);
            });
        }

        if ($request->filled('class_status')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('class_status', $request->class_status);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        if ($request->filled('admission_batch_id')) {
            $query->where('admission_batch_id', $request->admission_batch_id);
        }

        // Get all results for DataTable
        $convertedLeads = $query->orderBy('created_at', 'desc')->get();

        // Get filter data
        $courses = \App\Models\Course::where('is_active', 1)->get();
        $batches = \App\Models\Batch::where('course_id', 13)->where('is_active', 1)->get();
        $admission_batches = \App\Models\AdmissionBatch::where('is_active', 1)->get();
        $country_codes = get_country_code();

        return view('admin.converted-leads.web-development-index', compact('convertedLeads', 'courses', 'batches', 'admission_batches', 'country_codes'));
    }

    /**
     * Display Vibe Coding converted leads (course_id = 14)
     */
    public function vibeCodingIndex(Request $request)
    {
        $query = ConvertedLead::with(['lead', 'course', 'academicAssistant', 'createdBy', 'subject', 'studentDetails'])
            ->where('course_id', 14);

        // Apply role-based filtering
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_team_lead()) {
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    $query->whereHas('lead', function($q) use ($teamMemberIds) {
                        $q->whereIn('telecaller_id', $teamMemberIds);
                    });
                } else {
                    $query->whereHas('lead', function($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
                }
            } elseif (RoleHelper::is_admission_counsellor()) {
                // Can see all
            } elseif (RoleHelper::is_academic_assistant()) {
                // Can see all
            } elseif (RoleHelper::is_telecaller()) {
                $query->whereHas('lead', function($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
            }
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('register_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('call_status')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('call_status', $request->call_status);
            });
        }

        if ($request->filled('class_information')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('class_information', $request->class_information);
            });
        }

        if ($request->filled('orientation_class_status')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('orientation_class_status', $request->orientation_class_status);
            });
        }

        if ($request->filled('whatsapp_group_status')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('whatsapp_group_status', $request->whatsapp_group_status);
            });
        }

        if ($request->filled('class_status')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('class_status', $request->class_status);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        if ($request->filled('admission_batch_id')) {
            $query->where('admission_batch_id', $request->admission_batch_id);
        }

        // Get all results for DataTable
        $convertedLeads = $query->orderBy('created_at', 'desc')->get();

        // Get filter data
        $courses = \App\Models\Course::where('is_active', 1)->get();
        $batches = \App\Models\Batch::where('course_id', 14)->where('is_active', 1)->get();
        $admission_batches = \App\Models\AdmissionBatch::where('is_active', 1)->get();
        $country_codes = get_country_code();

        return view('admin.converted-leads.vibe-coding-index', compact('convertedLeads', 'courses', 'batches', 'admission_batches', 'country_codes'));
    }

    /**
     * Display Graphic Designing converted leads (course_id = 15)
     */
    public function graphicDesigningIndex(Request $request)
    {
        $query = ConvertedLead::with(['lead', 'course', 'academicAssistant', 'createdBy', 'subject', 'studentDetails'])
            ->where('course_id', 15);

        // Apply role-based filtering
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_team_lead()) {
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    $query->whereHas('lead', function($q) use ($teamMemberIds) {
                        $q->whereIn('telecaller_id', $teamMemberIds);
                    });
                } else {
                    $query->whereHas('lead', function($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
                }
            } elseif (RoleHelper::is_admission_counsellor()) {
                // Can see all
            } elseif (RoleHelper::is_academic_assistant()) {
                // Can see all
            } elseif (RoleHelper::is_telecaller()) {
                $query->whereHas('lead', function($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
            }
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('register_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('call_status')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('call_status', $request->call_status);
            });
        }

        if ($request->filled('class_information')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('class_information', $request->class_information);
            });
        }

        if ($request->filled('orientation_class_status')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('orientation_class_status', $request->orientation_class_status);
            });
        }

        if ($request->filled('whatsapp_group_status')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('whatsapp_group_status', $request->whatsapp_group_status);
            });
        }

        if ($request->filled('class_status')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('class_status', $request->class_status);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        if ($request->filled('admission_batch_id')) {
            $query->where('admission_batch_id', $request->admission_batch_id);
        }

        // Get all results for DataTable
        $convertedLeads = $query->orderBy('created_at', 'desc')->get();

        // Get filter data
        $courses = \App\Models\Course::where('is_active', 1)->get();
        $batches = \App\Models\Batch::where('course_id', 15)->where('is_active', 1)->get();
        $admission_batches = \App\Models\AdmissionBatch::where('is_active', 1)->get();
        $country_codes = get_country_code();

        return view('admin.converted-leads.graphic-designing-index', compact('convertedLeads', 'courses', 'batches', 'admission_batches', 'country_codes'));
    }

    public function eduthanzeelIndex(Request $request)
    {
        $query = ConvertedLead::with(['lead', 'course', 'subCourse', 'academicAssistant', 'createdBy', 'subject', 'studentDetails', 'teacher'])
            ->where('course_id', 6);

        // Apply role-based filtering
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_team_lead()) {
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    $query->whereHas('lead', function($q) use ($teamMemberIds) {
                        $q->whereIn('telecaller_id', $teamMemberIds);
                    });
                } else {
                    $query->whereHas('lead', function($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
                }
            } elseif (RoleHelper::is_admission_counsellor()) {
                // Can see all
            } elseif (RoleHelper::is_academic_assistant()) {
                // Can see all
            } elseif (RoleHelper::is_telecaller()) {
                $query->whereHas('lead', function($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
            }
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('register_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('class_status')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('class_status', $request->class_status);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        if ($request->filled('admission_batch_id')) {
            $query->where('admission_batch_id', $request->admission_batch_id);
        }

        // Get all results for DataTable
        $convertedLeads = $query->orderBy('created_at', 'desc')->get();

        // Get filter data
        $courses = \App\Models\Course::where('is_active', 1)->get();
        $batches = \App\Models\Batch::where('course_id', 6)->where('is_active', 1)->get();
        $admission_batches = \App\Models\AdmissionBatch::where('is_active', 1)->get();
        $teachers = \App\Models\User::where('role_id', 10)->where('is_active', 1)->get();
        $sub_courses = \App\Models\SubCourse::where('course_id', 6)->where('is_active', 1)->get();
        $country_codes = get_country_code();

        return view('admin.converted-leads.eduthanzeel-index', compact('convertedLeads', 'courses', 'batches', 'admission_batches', 'teachers', 'sub_courses', 'country_codes'));
    }

    /**
     * Display E-School converted leads (course_id = 5)
     */
    public function eschoolIndex(Request $request)
    {
        $query = ConvertedLead::with(['lead', 'course', 'subCourse', 'academicAssistant', 'createdBy', 'subject', 'studentDetails', 'teacher'])
            ->where('course_id', 5);

        // Apply role-based filtering
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_team_lead()) {
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    $query->whereHas('lead', function($q) use ($teamMemberIds) {
                        $q->whereIn('telecaller_id', $teamMemberIds);
                    });
                } else {
                    $query->whereHas('lead', function($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
                }
            } elseif (RoleHelper::is_admission_counsellor()) {
                // Can see all
            } elseif (RoleHelper::is_academic_assistant()) {
                // Can see all
            } elseif (RoleHelper::is_telecaller()) {
                $query->whereHas('lead', function($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
            }
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('register_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('class_status')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('class_status', $request->class_status);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        if ($request->filled('admission_batch_id')) {
            $query->where('admission_batch_id', $request->admission_batch_id);
        }

        if ($request->filled('sub_course_id')) {
            $query->where('sub_course_id', $request->sub_course_id);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('teacher_id')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('teacher_id', $request->teacher_id);
            });
        }

        // Get all results for DataTable
        $convertedLeads = $query->orderBy('created_at', 'desc')->get();

        // Get filter data
        $courses = \App\Models\Course::where('is_active', 1)->get();
        $batches = \App\Models\Batch::where('course_id', 5)->where('is_active', 1)->get();
        $admission_batches = \App\Models\AdmissionBatch::where('is_active', 1)->get();
        $sub_courses = \App\Models\SubCourse::where('course_id', 5)->where('is_active', 1)->get();
        $subjects = \App\Models\Subject::where('course_id', 5)->where('is_active', 1)->get();
        $teachers = \App\Models\User::where('role_id', 10)->where('is_active', 1)->get();
        $country_codes = get_country_code();

        return view('admin.converted-leads.eschool-index', compact('convertedLeads', 'courses', 'batches', 'admission_batches', 'sub_courses', 'subjects', 'teachers', 'country_codes'));
    }

    /**
     * Display the specified converted lead
     */
    public function show($id)
    {
        $convertedLead = ConvertedLead::with([
            'lead',
            'leadDetail',
            'course',
            'batch',
            'admissionBatch',
            'subject',
            'academicAssistant',
            'createdBy',
            'studentDetails.registrationLink'
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
                    if (!in_array($convertedLead->lead->telecaller_id, $teamMemberIds)) {
                        return redirect()->route('admin.converted-leads.index')
                            ->with('message_danger', 'Access denied. You can only view converted leads from your team.');
                    }
                } else {
                    // If no team assigned, only show their own leads
                    if ($convertedLead->lead->telecaller_id != AuthHelper::getCurrentUserId()) {
                        return redirect()->route('admin.converted-leads.index')
                            ->with('message_danger', 'Access denied. You can only view converted leads from your team.');
                    }
                }
            } elseif (RoleHelper::is_admission_counsellor()) {
                // Admission Counsellor: Can see ALL converted leads
                // No additional filtering needed
            } elseif (RoleHelper::is_academic_assistant()) {
                // Academic Assistant: Can see ALL converted leads
                // No additional filtering needed
            } elseif (RoleHelper::is_telecaller()) {
                // Telecaller: Can only see converted leads from leads assigned to them
                if ($convertedLead->lead->telecaller_id != AuthHelper::getCurrentUserId()) {
                    return redirect()->route('admin.converted-leads.index')
                        ->with('message_danger', 'Access denied. You can only view converted leads from leads assigned to you.');
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


    public function generateIdCardPdf($id)
    {
        $convertedLead = ConvertedLead::with([
            'lead',
            'leadDetail',
            'course',
            'academicAssistant',
            'createdBy',
            'studentDetails'
        ])->findOrFail($id);

        // Role-based access (same logic as you had)
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_team_lead()) {
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    if (!in_array($convertedLead->lead->telecaller_id, $teamMemberIds)) {
                        return redirect()->route('admin.converted-leads.index')
                            ->with('message_danger', 'Access denied. You can only view converted leads from your team.');
                    }
                } else {
                    if ($convertedLead->lead->telecaller_id != AuthHelper::getCurrentUserId()) {
                        return redirect()->route('admin.converted-leads.index')
                            ->with('message_danger', 'Access denied. You can only view converted leads from your team.');
                    }
                }
            } elseif (RoleHelper::is_academic_assistant()) {
                // Academic Assistant: Can see ALL converted leads
                // No additional filtering needed
            } elseif (RoleHelper::is_telecaller()) {
                if ($convertedLead->lead->telecaller_id != AuthHelper::getCurrentUserId()) {
                    return redirect()->route('admin.converted-leads.index')
                        ->with('message_danger', 'Access denied. You can only view converted leads from leads assigned to you.');
                }
            }
            // Admission counsellor = can see all
        }

        // Create circular image if passport photo exists
        $circularImagePath = null;
        if ($convertedLead->leadDetail && $convertedLead->leadDetail->passport_photo) {
            $circularImagePath = $this->createCircularImage($convertedLead->leadDetail->passport_photo);
        }

        // Load Blade view
        $html = view('admin.converted-leads.id-card-pdf', compact('convertedLead', 'circularImagePath'))->render();

        // Create mPDF instance
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_left' => 0,
            'margin_right' => 0,
        ]);

        // Write HTML
        $mpdf->WriteHTML($html);

        $filename = 'id_card_' . $convertedLead->name . '_' . $convertedLead->id . '.pdf';

        // Stream to browser
        return response($mpdf->Output($filename, 'I'))
            ->header('Content-Type', 'application/pdf');
    }

    public function generateAndStoreIdCard($id)
    {
        $convertedLead = ConvertedLead::with(['lead','leadDetail','course','academicAssistant','createdBy','studentDetails'])
            ->findOrFail($id);

        // Check if ID card was already generated recently (within last 30 seconds)
        $recentIdCard = ConvertedLeadIdCard::where('converted_lead_id', $id)
            ->where('generated_at', '>', now()->subSeconds(30))
            ->first();
            
        if ($recentIdCard) {
            return response()->json([
                'success' => false,
                'message' => 'ID card was already generated recently. Please wait a moment before generating again.',
            ], 429);
        }

        // Create circular image if passport photo exists
        $circularImagePath = null;
        if ($convertedLead->leadDetail && $convertedLead->leadDetail->passport_photo) {
            $circularImagePath = $this->createCircularImage($convertedLead->leadDetail->passport_photo);
        }

        $html = view('admin.converted-leads.id-card-pdf', compact('convertedLead', 'circularImagePath'))->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_left' => 0,
            'margin_right' => 0,
        ]);

        $mpdf->WriteHTML($html);

        $safeName = preg_replace('/[^A-Za-z0-9_-]+/', '_', $convertedLead->name);
        $fileName = 'id_card_' . $safeName . '_' . $convertedLead->id . '_' . time() . '.pdf';
        $relativePath = 'id_cards/' . $fileName;

        // Ensure directory exists
        if (!Storage::disk('public')->exists('id_cards')) {
            Storage::disk('public')->makeDirectory('id_cards');
        }

        // Save PDF to storage/app/public/id_cards
        $pdfContent = $mpdf->Output($fileName, 'S');
        Storage::disk('public')->put($relativePath, $pdfContent);

        // Store the relative path for database (this will be accessible via /storage/id_cards/filename.pdf)
        $dbPath = 'storage/' . $relativePath;

        // Create DB record (upsert latest per converted lead)
        $idCardRecord = ConvertedLeadIdCard::updateOrCreate(
            ['converted_lead_id' => $convertedLead->id],
            [
                'file_path' => $dbPath,
                'file_name' => $fileName,
                'generated_at' => now(),
                'generated_by' => AuthHelper::getCurrentUserId(),
            ]
        );

        // Send email to student with ID card attachment
        try {
            if ($convertedLead->email) {
                Mail::to($convertedLead->email)->send(new IdCardNotification(
                    $convertedLead->name,
                    $convertedLead->course ? $convertedLead->course->title : 'N/A',
                    $idCardRecord->file_path
                ));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send ID card email: ' . $e->getMessage());
            // Continue execution even if email fails
        }

        return response()->json([
            'success' => true,
            'message' => 'ID Card generated, stored, and sent to student email successfully.',
        ]);
    }

    /**
     * Generate a PDF of the converted lead details (without Uploaded Documents)
     */
    public function generateDetailsPdf($id)
    {
        $convertedLead = ConvertedLead::with([
            'lead',
            'leadDetail',
            'course',
            'batch',
            'admissionBatch',
            'subject',
            'academicAssistant',
            'createdBy',
            'studentDetails'
        ])->findOrFail($id);

        // Lead activities (same as show page)
        $leadActivities = \App\Models\LeadActivity::where('lead_id', $convertedLead->lead_id)
            ->select('id', 'lead_id', 'reason', 'created_at', 'activity_type', 'description', 'remarks', 'rating', 'followup_date', 'created_by', 'lead_status_id')
            ->with(['leadStatus:id,title', 'createdBy:id,name'])
            ->orderBy('created_at', 'desc')
            ->get();

        $html = view('admin.converted-leads.pdf', compact('convertedLead', 'leadActivities'))->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 12,
            'margin_bottom' => 12,
            'margin_left' => 12,
            'margin_right' => 12,
        ]);

        $mpdf->SetTitle('Converted Lead Details - #' . $convertedLead->id);
        $mpdf->WriteHTML($html);

        $filename = 'converted-lead-details-' . $convertedLead->id . '.pdf';
        return response($mpdf->Output($filename, 'I'))
            ->header('Content-Type', 'application/pdf');
    }

    public function viewStoredIdCard($id)
    {
        $convertedLead = ConvertedLead::findOrFail($id);
        $record = ConvertedLeadIdCard::where('converted_lead_id', $convertedLead->id)->first();
        if (!$record) {
            return redirect()->back()->with('message_danger', 'ID Card not generated yet.');
        }

        // The file path in database is 'storage/id_cards/filename.pdf'
        // This should be accessible via public/storage/id_cards/filename.pdf (thanks to storage link)
        $absolute = public_path($record->file_path);
        
        if (!file_exists($absolute)) {
            return redirect()->back()->with('message_danger', 'Stored ID Card file missing.');
        }

        return response()->file($absolute, [
            'Content-Type' => 'application/pdf'
        ]);
    }

    /**
     * Show modal for updating register number
     */
    public function showUpdateRegisterNumberModal($id)
    {
        $convertedLead = ConvertedLead::findOrFail($id);
        
        // Check if user has permission to update register numbers
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_academic_assistant() && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['error' => 'Access denied. Only admins, academic assistants, and admission counsellors can update register numbers.'], 403);
        }

        return view('admin.converted-leads.update-register-number-modal', compact('convertedLead'));
    }

    /**
     * Update register number
     */
    public function updateRegisterNumber(Request $request, $id)
    {
        // Check if user has permission to update register numbers
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_academic_assistant() && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['error' => 'Access denied. Only admins, academic assistants, and admission counsellors can update register numbers.'], 403);
        }

        $request->validate([
            'register_number' => 'required|string|max:50|unique:converted_leads,register_number,' . $id
        ]);

        $convertedLead = ConvertedLead::findOrFail($id);
        
        $convertedLead->update([
            'register_number' => $request->register_number,
            'reg_updated_at' => now(),
            'reg_updated_by' => AuthHelper::getCurrentUserId()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Register number updated successfully.',
            'register_number' => $convertedLead->register_number
        ]);
    }

    /**
     * Toggle academic verification status for a converted lead
     */
    public function toggleAcademicVerification(\Illuminate\Http\Request $request, $id)
    {
        if (!\App\Helpers\RoleHelper::is_admin_or_super_admin() && !\App\Helpers\RoleHelper::is_academic_assistant() && !\App\Helpers\RoleHelper::is_admission_counsellor()) {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }

        $convertedLead = \App\Models\ConvertedLead::findOrFail($id);

        $isCurrentlyVerified = (bool) $convertedLead->is_academic_verified;
        if ($isCurrentlyVerified) {
            $convertedLead->is_academic_verified = 0;
            $convertedLead->academic_verified_by = null;
            $convertedLead->academic_verified_at = null;
        } else {
            $convertedLead->is_academic_verified = 1;
            $convertedLead->academic_verified_by = \App\Helpers\AuthHelper::getCurrentUserId();
            $convertedLead->academic_verified_at = now();
        }
        $convertedLead->save();

        return response()->json([
            'success' => true,
            'message' => $isCurrentlyVerified ? 'Academic verification removed.' : 'Academic verification completed.',
            'is_academic_verified' => (bool) $convertedLead->is_academic_verified,
        ]);
    }

    /**
     * Inline update for converted lead fields
     */
    public function inlineUpdate(Request $request, $id)
    {
        // Check if user has permission to update
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_academic_assistant()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $convertedLead = ConvertedLead::findOrFail($id);
        
        // Additional role-based access control
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_academic_assistant()) {
                // Academic Assistant: Can update ALL converted leads
                // No additional filtering needed
            }
        }

        $field = $request->input('field');
        $value = $request->input('value');

        // Special case: if updating phone and code together, allow updating code via same request
        if ($field === 'phone' && $request->filled('code')) {
            $codeValue = $request->input('code');
            // Validate code quickly against allowed format (numeric or +prefix)
            $codeValidator = Validator::make(['code' => $codeValue], ['code' => 'nullable|string|max:5']);
            if ($codeValidator->fails()) {
                return response()->json([
                    'error' => 'Validation failed.',
                    'errors' => $codeValidator->errors()
                ], 422);
            }
            $convertedLead->code = $codeValue;
        }

        // Define allowed fields and their validation rules
        $allowedFields = [
            'register_number' => 'nullable|string|max:50',
            'sub_course_id' => 'nullable|exists:sub_courses,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'admission_batch_id' => 'nullable|exists:admission_batches,id',
            'academic_assistant_id' => 'nullable|exists:users,id',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'dob' => 'nullable|date|before_or_equal:today',
            'status' => 'nullable|string|in:Paid,Admission cancel,Active,Inactive',
            'reg_fee' => 'nullable|string|in:Handover -1,Handover - 2,Handover - 3,Handover - 4,Handover - 5,Paid,Admission cancel',
            'exam_fee' => 'nullable|string|in:Pending,Not Paid,Paid',
            'ref_no' => 'nullable|string|max:255',
            'enroll_no' => 'nullable|string|max:255',
            'id_card' => 'nullable|string|in:processing,download,not downloaded',
            'tma' => 'nullable|string|in:Uploaded,Not Upload',
            'registration_number' => 'nullable|string|max:255',
            'enrollment_number' => 'nullable|string|max:255',
            'registration_link_id' => 'nullable|exists:registration_links,id',
            'certificate_status' => 'nullable|string|in:In Progress,Online Result Not Arrived,One Result Arrived,Certificate Arrived,Not Received,No Admission',
            'certificate_received_date' => 'nullable|date',
            'certificate_issued_date' => 'nullable|date',
            'remarks' => 'nullable|string|max:1000',
            // E-School and Eduthanzeel specific fields
            'continuing_studies' => 'nullable|string|in:yes,no',
            'reason' => 'nullable|string|max:1000',
            // BOSSE specific fields
            'application_number' => 'nullable|string|max:255',
            'board_registration_number' => 'nullable|string|max:255',
            'st' => 'nullable|integer|min:0|max:20',
            'phy' => 'nullable|integer|min:0|max:20',
            'che' => 'nullable|integer|min:0|max:20',
            'bio' => 'nullable|integer|min:0|max:20',
            // Hotel Management specific fields
            'app' => 'nullable|string|in:Provided,Ad cancel',
            'group' => 'nullable|string|in:Assigned',
            'interview' => 'nullable|string|in:Failed,Passed,Ad cancel',
            'howmany_interview' => 'nullable|integer|min:0|max:10',
            // AI with Python specific fields
            'call_status' => 'nullable|string|in:Call Not Answered,Switched Off,Line Busy,Student Asks to Call Later,Lack of Interest in Conversation,Wrong Contact,Inconsistent Responses,Task Complete,Admission cancel',
            'class_information' => 'nullable|string|in:phone call,whatsapp',
            'orientation_class_status' => 'nullable|string|in:Participated,Did not participated',
            'class_starting_date' => 'nullable|date',
            'class_ending_date' => 'nullable|date',
            'whatsapp_group_status' => 'nullable|string|in:sent link,task complete',
            'class_time' => 'nullable|date_format:H:i',
            'class_status' => 'nullable|string|in:Running,Cancel,complete,completed,drop out,ongoing,dropout',
            'complete_cancel_date' => 'nullable|date',
            // Eduthanzeel specific fields
            'teacher_id' => 'nullable|exists:users,id',
            'screening' => 'nullable|date',
            // phone & code inline updates
            'phone' => 'nullable|string|max:20',
            'code' => 'nullable|string|max:5',
        ];

        if (!array_key_exists($field, $allowedFields)) {
            return response()->json(['error' => 'Invalid field.'], 400);
        }

        // Special handling for register_number validation
        if ($field === 'register_number') {
            // If the value is empty or null, allow it
            if (empty($value) || $value === '-' || $value === 'N/A') {
                $value = null;
            } else {
                // Check if the register number already exists for another record
                $existingRecord = ConvertedLead::where('register_number', $value)
                    ->where('id', '!=', $id)
                    ->first();
                
                if ($existingRecord) {
                    return response()->json([
                        'error' => 'Register number has already been taken.',
                        'errors' => ['register_number' => ['Register number has already been taken.']]
                    ], 422);
                }
            }
        }

        // Validate the field
        $validator = Validator::make([$field => $value], [$field => $allowedFields[$field]]);
        
        if ($validator->fails()) {
            // Create user-friendly error messages
            $errors = $validator->errors();
            $userFriendlyErrors = [];
            
            foreach ($errors->all() as $error) {
                // Make error messages more user-friendly
                $userFriendlyError = str_replace('The ', '', $error);
                $userFriendlyError = str_replace(' field ', ' ', $userFriendlyError);
                $userFriendlyError = str_replace(' must not be greater than 20.', ' cannot be more than 20.', $userFriendlyError);
                $userFriendlyError = str_replace(' must be at least 0.', ' cannot be less than 0.', $userFriendlyError);
                $userFriendlyError = ucfirst($userFriendlyError);
                $userFriendlyErrors[] = $userFriendlyError;
            }
            
            return response()->json([
                'error' => implode(', ', $userFriendlyErrors),
                'errors' => $validator->errors()
            ], 422);
        }

        // Special handling for specific fields
        if ($field === 'password' && $value) {
            // Password will be encrypted automatically by the model's setPasswordAttribute
        } elseif ($field === 'dob' && $value) {
            try {
                $value = Carbon::parse($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Invalid date format.'
                ], 422);
            }
        }

        // Handle fields that are now in ConvertedStudentDetail
        $studentDetailFields = ['reg_fee', 'exam_fee', 'enroll_no', 'id_card', 'tma', 'registration_number', 'enrollment_number', 'registration_link_id', 'certificate_status', 'certificate_received_date', 'certificate_issued_date', 'remarks', 'continuing_studies', 'reason', 'application_number', 'board_registration_number', 'st', 'phy', 'che', 'bio', 'app', 'group', 'interview', 'howmany_interview', 'call_status', 'class_information', 'orientation_class_status', 'class_starting_date', 'class_ending_date', 'whatsapp_group_status', 'class_time', 'class_status', 'complete_cancel_date', 'teacher_id', 'screening'];
        
        if (in_array($field, $studentDetailFields)) {
            // Update in ConvertedStudentDetail
            $studentDetail = $convertedLead->studentDetails;
            if (!$studentDetail) {
                // Create student detail if it doesn't exist
                $studentDetail = $convertedLead->studentDetails()->create([
                    'converted_lead_id' => $convertedLead->id,
                ]);
            }
            $studentDetail->{$field} = $value;
            $studentDetail->save();
        } else {
            // Update in ConvertedLead
            $convertedLead->{$field} = $value;
            $convertedLead->updated_by = AuthHelper::getCurrentUserId();
            $convertedLead->save();
        }

        // Get the updated value for response
        if (in_array($field, $studentDetailFields)) {
            // For student detail fields, get the value from the relationship
            $updatedValue = $convertedLead->studentDetails ? $convertedLead->studentDetails->$field : $value;
        } else {
            $updatedValue = $convertedLead->$field;
        }
        
        // Special handling for display values
        if ($field === 'sub_course_id' && $updatedValue) {
            $subCourse = \App\Models\SubCourse::find($updatedValue);
            $updatedValue = $subCourse ? $subCourse->title : $updatedValue;
        } elseif ($field === 'subject_id' && $updatedValue) {
            $subject = \App\Models\Subject::find($updatedValue);
            $updatedValue = $subject ? $subject->title : $updatedValue;
        } elseif ($field === 'admission_batch_id' && $updatedValue) {
            $admissionBatch = \App\Models\AdmissionBatch::find($updatedValue);
            $updatedValue = $admissionBatch ? $admissionBatch->title : $updatedValue;
        } elseif ($field === 'academic_assistant_id' && $updatedValue) {
            $user = \App\Models\User::find($updatedValue);
            $updatedValue = $user ? $user->name : $updatedValue;
        } elseif (in_array($field, ['phone', 'code'])) {
            // For phone/code updates, return formatted display
            $updatedValue = \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone);
        } elseif ($field === 'registration_link_id' && $updatedValue) {
            $registrationLink = \App\Models\RegistrationLink::find($updatedValue);
            $updatedValue = $registrationLink ? $registrationLink->title : $updatedValue;
        } elseif (in_array($field, ['certificate_received_date', 'certificate_issued_date', 'class_starting_date', 'class_ending_date', 'complete_cancel_date', 'screening']) && $updatedValue) {
            $updatedValue = \Carbon\Carbon::parse($updatedValue)->format('d-m-Y');
        } elseif ($field === 'class_time' && $updatedValue) {
            $updatedValue = \Carbon\Carbon::parse($updatedValue)->format('h:i A');
        } elseif ($field === 'teacher_id' && $updatedValue) {
            $teacher = \App\Models\User::find($updatedValue);
            $updatedValue = $teacher ? $teacher->name : $updatedValue;
        } elseif ($field === 'register_number') {
            // For register_number, return the value or '-' if empty
            $updatedValue = $updatedValue ?: '-';
        } elseif ($field === 'continuing_studies' && $updatedValue) {
            // Format continuing_studies with ucfirst
            $updatedValue = ucfirst($updatedValue);
        }

        return response()->json([
            'success' => true,
            'message' => ucfirst(str_replace('_', ' ', $field)) . ' updated successfully.',
            'value' => $updatedValue
        ]);
    }

    /**
     * Create a circular version of the passport photo
     */
    private function createCircularImage($imagePath)
    {
        try {
            $originalPath = public_path('storage/' . $imagePath);
            
            if (!file_exists($originalPath)) {
                return null;
            }

            // Get image info
            $imageInfo = getimagesize($originalPath);
            $mimeType = $imageInfo['mime'];
            
            // Create image resource based on type
            switch ($mimeType) {
                case 'image/jpeg':
                    $source = imagecreatefromjpeg($originalPath);
                    break;
                case 'image/png':
                    $source = imagecreatefrompng($originalPath);
                    break;
                case 'image/gif':
                    $source = imagecreatefromgif($originalPath);
                    break;
                default:
                    return null;
            }

            // Set dimensions
            $size = 200;
            $radius = $size / 2;

            // Create a new image with transparent background
            $circular = imagecreatetruecolor($size, $size);
            imagealphablending($circular, false);
            imagesavealpha($circular, true);
            $transparent = imagecolorallocatealpha($circular, 0, 0, 0, 127);
            imagefill($circular, 0, 0, $transparent);

            // Create circular mask
            $mask = imagecreatetruecolor($size, $size);
            imagealphablending($mask, false);
            imagesavealpha($mask, true);
            imagefill($mask, 0, 0, $transparent);

            // Draw white circle for mask
            $white = imagecolorallocate($mask, 255, 255, 255);
            imagefilledellipse($mask, $radius, $radius, $size, $size, $white);

            // Apply mask to source image
            imagealphablending($source, true);
            imagealphablending($circular, true);
            
            // Copy and resize source image
            imagecopyresampled($circular, $source, 0, 0, 0, 0, $size, $size, imagesx($source), imagesy($source));
            
            // Apply circular mask
            for ($x = 0; $x < $size; $x++) {
                for ($y = 0; $y < $size; $y++) {
                    $color = imagecolorat($mask, $x, $y);
                    if ($color == 0) { // Black pixels in mask
                        imagesetpixel($circular, $x, $y, $transparent);
                    }
                }
            }

            // Save circular image
            $circularPath = 'temp/circular_' . uniqid() . '.png';
            $fullCircularPath = public_path($circularPath);
            
            // Create temp directory if it doesn't exist
            if (!file_exists(public_path('temp'))) {
                mkdir(public_path('temp'), 0755, true);
            }
            
            imagepng($circular, $fullCircularPath);
            
            // Clean up
            imagedestroy($source);
            imagedestroy($circular);
            imagedestroy($mask);
            
            return $circularPath;
            
        } catch (\Exception $e) {
            Log::error('Error creating circular image: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Batch update converted leads
     */
    public function batchUpdate(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor() && !RoleHelper::is_academic_assistant()) {
            return response()->json([
                'success' => false,
                'error' => 'Access denied.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:converted_leads,id',
            'field' => 'required|string',
            'value' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first()
            ], 422);
        }

        $ids = $request->ids;
        $field = $request->field;
        $value = $request->value;

        // Validate field
        $allowedFields = ['batch_id', 'admission_batch_id', 'status', 'reg_fee', 'exam_fee', 'id_card', 'tma', 'academic_assistant_id'];
        if (!in_array($field, $allowedFields)) {
            return response()->json([
                'success' => false,
                'error' => 'Field not allowed for batch update.'
            ], 422);
        }

        try {
            $updatedCount = 0;
            $studentDetailFields = ['reg_fee', 'exam_fee', 'id_card', 'tma'];

            foreach ($ids as $id) {
                $convertedLead = ConvertedLead::find($id);
                if (!$convertedLead) {
                    continue;
                }

                if (in_array($field, $studentDetailFields)) {
                    $studentDetail = $convertedLead->studentDetails;
                    if (!$studentDetail) {
                        $studentDetail = $convertedLead->studentDetails()->create([
                            'converted_lead_id' => $convertedLead->id,
                        ]);
                    }
                    $studentDetail->{$field} = $value;
                    $studentDetail->save();
                } else {
                    $convertedLead->{$field} = $value;
                    $convertedLead->updated_by = AuthHelper::getCurrentUserId();
                    $convertedLead->save();
                }

                $updatedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$updatedCount} record(s).",
                'count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Batch update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to update records: ' . $e->getMessage()
            ], 500);
        }
    }
}
