<?php

namespace App\Http\Controllers;

use App\Helpers\RoleHelper;
use App\Models\ConvertedLead;
use App\Models\Course;
use App\Models\LeadActivity;
use App\Services\LeadCallLogService;
use Illuminate\Http\Request;

class PostSalesConvertedLeadController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureAccess();

        $query = ConvertedLead::with(['course', 'batch', 'admissionBatch', 'subject']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('register_number', 'like', "%{$search}%");
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

        $convertedLeads = $query->orderByDesc('created_at')->get();
        $courses = Course::where('is_active', 1)->orderBy('title')->get(['id', 'title']);

        return view('admin.post-sales.converted-leads.index', compact('convertedLeads', 'courses'));
    }

    public function show($id)
    {
        $this->ensureAccess();

        $convertedLead = ConvertedLead::with([
            'lead',
            'leadDetail.sslcCertificates.verifiedBy',
            'leadDetail.sslcVerifiedBy',
            'leadDetail.plustwoVerifiedBy',
            'leadDetail.ugVerifiedBy',
            'leadDetail.passportPhotoVerifiedBy',
            'leadDetail.adharFrontVerifiedBy',
            'leadDetail.adharBackVerifiedBy',
            'leadDetail.signatureVerifiedBy',
            'leadDetail.birthCertificateVerifiedBy',
            'leadDetail.otherDocumentVerifiedBy',
            'course',
            'batch',
            'admissionBatch',
            'subject',
            'academicAssistant',
            'createdBy',
            'studentDetails.registrationLink'
        ])->findOrFail($id);

        $leadActivities = LeadActivity::where('lead_id', $convertedLead->lead_id)
            ->select('id', 'lead_id', 'reason', 'created_at', 'activity_type', 'description', 'remarks', 'rating', 'followup_date', 'created_by', 'lead_status_id')
            ->with(['leadStatus:id,title', 'createdBy:id,name'])
            ->orderBy('created_at', 'desc')
            ->get();

        $callLogs = LeadCallLogService::forConvertedLead($convertedLead);
        $listRoute = route('admin.post-sales.converted-leads.index');

        return view('admin.converted-leads.show', compact('convertedLead', 'leadActivities', 'callLogs', 'listRoute'));
    }

    protected function ensureAccess(): void
    {
        if (
            RoleHelper::is_post_sales() ||
            RoleHelper::is_admin_or_super_admin() ||
            RoleHelper::is_general_manager()
        ) {
            return;
        }

        abort(403, 'Access denied.');
    }
}

