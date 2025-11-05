@extends('layouts.mantis')

@section('title', 'Leads')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Leads Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Leads</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

@if(request('search_key'))
<!-- [ Search Results Indicator ] start -->
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <div class="d-flex align-items-center">
        <i class="ti ti-search me-2"></i>
        <div class="flex-grow-1">
            <strong>Search Results:</strong> Showing leads matching "{{ request('search_key') }}"
        </div>
        <a href="{{ route('leads.index') }}" class="btn btn-sm btn-outline-info">
            <i class="ti ti-x"></i> Clear Search
        </a>
    </div>
</div>
<!-- [ Search Results Indicator ] end -->
@endif

<!-- [ Filter Section ] start -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('leads.index') }}" id="dateFilterForm">
                    <div class="row g-3 align-items-end">
                        <!-- From Date -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control form-control-sm" name="date_from" id="date_from"
                                value="{{ $fromDate }}">
                        </div>

                        <!-- To Date -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control form-control-sm" name="date_to" id="date_to"
                                value="{{ $toDate }}">
                        </div>

                        <!-- Status -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <label for="filter_lead_status_id" class="form-label">Status</label>
                            <select class="form-select form-select-sm" name="lead_status_id" id="filter_lead_status_id">
                                <option value="">All Statuses</option>
                                @foreach($leadStatuses as $status)
                                <option value="{{ $status->id }}" {{ request('lead_status_id') == $status->id ? 'selected' : '' }}>
                                    {{ $status->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Source -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <label for="filter_lead_source_id" class="form-label">Source</label>
                            <select class="form-select form-select-sm" name="lead_source_id" id="filter_lead_source_id">
                                <option value="">All Sources</option>
                                @foreach($leadSources as $source)
                                <option value="{{ $source->id }}" {{ request('lead_source_id') == $source->id ? 'selected' : '' }}>
                                    {{ $source->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Course -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <label for="course_id" class="form-label">Course</label>
                            <select class="form-select form-select-sm" name="course_id" id="course_id">
                                <option value="">All Courses</option>
                                @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Rating -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <label for="rating" class="form-label">Rating</label>
                            <select class="form-select form-select-sm" name="rating" id="rating">
                                <option value="">All Ratings</option>
                                @for($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                                    {{ $i }}/10
                                    </option>
                                    @endfor
                            </select>
                        </div>

                        <!-- Telecaller (conditional) -->
                        @if(!$isTelecaller || $isTeamLead)
                        <div class="col-6 col-md-4 col-lg-2">
                            <label for="telecaller_id" class="form-label">Telecaller</label>
                            <select class="form-select form-select-sm" name="telecaller_id" id="telecaller_id_filter">
                                <option value="">All Telecallers</option>
                                @foreach($telecallers as $telecaller)
                                <option value="{{ $telecaller->id }}" {{ request('telecaller_id') == $telecaller->id ? 'selected' : '' }}>
                                    {{ $telecaller->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="col-12 col-lg-2">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary btn-sm flex-fill flex-lg-grow-0">
                                    <i class="ti ti-filter me-1"></i> Filter
                                </button>
                                <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary btn-sm flex-fill flex-lg-grow-0">
                                    <i class="ti ti-x me-1"></i> Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- [ Filter Section ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <!-- Desktop Header -->
                <div class="d-none d-md-flex justify-content-between align-items-center">
                    <h5 class="mb-0">All Leads</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('leads.export', request()->query()) }}" class="btn btn-outline-info btn-sm px-3"
                            title="Export to Excel">
                            <i class="ti ti-download"></i> Export Excel
                        </a>
                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_team_lead() || \App\Helpers\RoleHelper::is_general_manager())
                        <a href="javascript:void(0);" class="btn btn-primary btn-sm px-3"
                            onclick="show_ajax_modal('{{ route('leads.add') }}', 'Add New Lead')">
                            <i class="ti ti-plus"></i> Add Lead
                        </a>
                        <a href="javascript:void(0);" class="btn btn-outline-primary btn-sm px-3"
                            onclick="show_ajax_modal('{{ route('leads.bulk-upload.test') }}', 'Bulk Upload Leads')">
                            <i class="ti ti-upload"></i> Bulk Upload
                        </a>
                        <a href="javascript:void(0);" class="btn btn-outline-success btn-sm px-3"
                            onclick="show_large_modal('{{ route('admin.leads.bulk-reassign') }}', 'Bulk Reassign Leads')">
                            <i class="ti ti-users"></i> Bulk Reassign
                        </a>
                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_general_manager())
                        <a href="javascript:void(0);" class="btn btn-outline-danger btn-sm px-3"
                            onclick="show_ajax_modal('{{ route('admin.leads.bulk-delete') }}', 'Bulk Delete Leads')">
                            <i class="ti ti-trash"></i> Bulk Delete
                        </a>
                        @endif
                        @endif
                    </div>
                </div>

                <!-- Mobile Header -->
                <div class="d-md-none">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">All Leads</h5>
                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_team_lead() || \App\Helpers\RoleHelper::is_general_manager())
                        <a href="javascript:void(0);" class="btn btn-primary btn-sm"
                            onclick="show_ajax_modal('{{ route('leads.add') }}', 'Add New Lead')">
                            <i class="ti ti-plus"></i> Add
                        </a>
                        @endif
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-12">
                            <a href="{{ route('leads.export', request()->query()) }}" class="btn btn-outline-info btn-sm w-100"
                                title="Export to Excel">
                                <i class="ti ti-download me-1"></i> Export Excel
                            </a>
                        </div>
                    </div>

                    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_team_lead() || \App\Helpers\RoleHelper::is_general_manager())
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="javascript:void(0);" class="btn btn-outline-primary btn-sm w-100"
                                onclick="show_ajax_modal('{{ route('leads.bulk-upload.test') }}', 'Bulk Upload Leads')">
                                <i class="ti ti-upload me-1"></i> Upload
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="javascript:void(0);" class="btn btn-outline-success btn-sm w-100"
                                onclick="show_large_modal('{{ route('admin.leads.bulk-reassign') }}', 'Bulk Reassign Leads')">
                                <i class="ti ti-users me-1"></i> Reassign
                            </a>
                        </div>
                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_general_manager())
                        <div class="col-6">
                            <a href="javascript:void(0);" class="btn btn-outline-danger btn-sm w-100"
                                onclick="show_ajax_modal('{{ route('admin.leads.bulk-delete') }}', 'Bulk Delete Leads')">
                                <i class="ti ti-trash me-1"></i> Delete
                            </a>
                        </div>
                        @endif
                        <div class="col-6">
                            <!-- Empty space for better layout -->
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="card-body">

                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive" style="overflow-x: auto;">
                        <table class="table table-hover" id="leadsTable" style="min-width: 1700px;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Actions</th>
                                    @if($isAdminOrSuperAdmin || $isTelecallerRole || $isAcademicAssistant || $isAdmissionCounsellor)
                                    <th>Registration Details</th>
                                    @endif
                                    <th>Created At</th>
                                    <th>Name</th>
                                    <th>Profile</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Interest</th>
                                    <th>Rating</th>
                                    <th>Source</th>
                                    <th>Course</th>
                                    <th>Telecaller</th>
                                    <th>Place</th>
                                    <th>Followup Date</th>
                                    <th>Last Reason</th>
                                    <th>Remarks</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leads as $index => $lead)
                                @php
                                    // Cache expensive method calls to avoid repeated calculations
                                    $missingFields = $lead->getMissingFields();
                                    $missingFieldsCount = count($missingFields);
                                    $missingFieldsDisplay = implode(', ', array_slice($missingFields, 0, 5));
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary"
                                                onclick="show_large_modal('{{ route('leads.ajax-show', $lead->id) }}', 'View Lead')"
                                                title="View Lead">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            @if($isAdminOrSuperAdmin || $isTeamLeadRole || $isGeneralManager)
                                            <a href="javascript:void(0);" class="btn btn-sm btn-outline-secondary"
                                                onclick="show_ajax_modal('{{ route('leads.ajax-edit', $lead->id) }}', 'Edit Lead')"
                                                title="Edit Lead">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            @endif
                                            @if($hasLeadActionPermission)
                                            <a href="javascript:void(0);" class="btn btn-sm btn-outline-success"
                                                onclick="show_ajax_modal('{{ route('leads.status-update', $lead->id) }}', 'Update Status')"
                                                title="Update Status">
                                                <i class="ti ti-arrow-up"></i>
                                            </a>
                                            @if(!$lead->is_converted && $lead->studentDetails && (strtolower($lead->studentDetails->status ?? '') === 'approved'))
                                            <a href="javascript:void(0);" class="btn btn-sm btn-outline-warning"
                                                onclick="show_ajax_modal('{{ route('leads.convert', $lead->id) }}', 'Convert Lead')"
                                                title="Convert Lead">
                                                <i class="ti ti-refresh"></i>
                                            </a>
                                            @endif
                                        </div>
                                        <br>
                                        <hr><br>
                                        <div class="btn-group" role="group">
                                            @if($lead->lead_status_id == 6)
                                            <a href="https://docs.google.com/forms/d/e/1FAIpQLSchtc8xlKUJehZNmzoKTkRvwLwk4-SGjzKSHM2UFToAhgdTlQ/viewform?usp=sf_link"
                                                target="_blank"
                                                class="btn btn-sm btn-outline-info"
                                                title="Demo Conduction Form">
                                                <i class="ti ti-file-text"></i>
                                            </a>
                                            @endif
                                            @if($lead->phone && is_telecaller())
                                            @php
                                            $currentUserId = session('user_id') ?? (\App\Helpers\AuthHelper::getCurrentUserId() ?? 0);
                                            @endphp
                                            @if($currentUserId > 0)
                                            <button class="btn btn-sm btn-outline-success voxbay-call-btn"
                                                data-lead-id="{{ $lead->id }}"
                                                data-telecaller-id="{{ $currentUserId }}"
                                                title="Call Lead">
                                                <i class="ti ti-phone"></i>
                                            </button>
                                            @endif
                                            @endif
                                            <a href="{{ route('leads.call-logs', $lead) }}"
                                                class="btn btn-sm btn-outline-info"
                                                title="View Call Logs">
                                                <i class="ti ti-phone-call"></i>
                                            </a>
                                            @if($isAdminOrSuperAdmin || $isGeneralManager)
                                            <a href="javascript:void(0);" class="btn btn-sm btn-outline-danger"
                                                onclick="delete_modal('{{ route('leads.destroy', $lead->id) }}')"
                                                title="Delete Lead">
                                                <i class="ti ti-trash"></i>
                                            </a>
                                            @endif
                                        </div>

                                        @endif
                                    </td>
                                    @if($isAdminOrSuperAdmin || $isTelecallerRole || $isAcademicAssistant || $isAdmissionCounsellor)
                                    <td class="text-center">
                                        @if($lead->studentDetails)
                                        <div class="d-flex flex-column gap-1">
                                            <span class="badge bg-success">Form Submitted</span>
                                            <small class="text-muted">{{ $lead->studentDetails->_course_title ?? ($courseName[$lead->studentDetails->course_id] ?? 'Unknown Course') }}</small>
                                            @if($lead->studentDetails->status)
                                            <span class="badge 
                                                        @if($lead->studentDetails->status == 'approved') bg-success
                                                        @elseif($lead->studentDetails->status == 'rejected') bg-danger
                                                        @else bg-warning
                                                        @endif">
                                                {{ ucfirst($lead->studentDetails->status) }}
                                            </span>
                                            @endif
                                            <a href="{{ route('leads.registration-details', $lead->id) }}"
                                                class="btn btn-sm btn-outline-primary mt-1"
                                                title="View Registration Details">
                                                <i class="ti ti-eye me-1"></i>View Details
                                            </a>
                                        </div>
                                        @else
                                        @if($lead->course_id == 1)
                                        <div class="d-flex gap-1">
                                        <a href="{{ route('public.lead.nios.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-warning" title="Open NIOS Registration Form">
                                            <i class="ti ti-external-link"></i>
                                        </a>
                                            <button type="button" class="btn btn-sm btn-outline-info copy-link-btn" 
                                                    data-url="{{ route('public.lead.nios.register', $lead->id) }}" 
                                                    title="Copy NIOS Registration Link">
                                                <i class="ti ti-copy"></i>
                                            </button>
                                        </div>
                                        @elseif($lead->course_id == 2)
                                        <div class="d-flex gap-1">
                                        <a href="{{ route('public.lead.bosse.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-warning" title="Open BOSSE Registration Form">
                                            <i class="ti ti-external-link"></i>
                                        </a>
                                            <button type="button" class="btn btn-sm btn-outline-info copy-link-btn" 
                                                    data-url="{{ route('public.lead.bosse.register', $lead->id) }}" 
                                                    title="Copy BOSSE Registration Link">
                                                <i class="ti ti-copy"></i>
                                            </button>
                                        </div>
                                        @elseif($lead->course_id == 3)
                                        <div class="d-flex gap-1">
                                        <a href="{{ route('public.lead.medical-coding.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-warning" title="Open Medical Coding Registration Form">
                                            <i class="ti ti-external-link"></i>
                                        </a>
                                            <button type="button" class="btn btn-sm btn-outline-info copy-link-btn" 
                                                    data-url="{{ route('public.lead.medical-coding.register', $lead->id) }}" 
                                                    title="Copy Medical Coding Registration Link">
                                                <i class="ti ti-copy"></i>
                                            </button>
                                        </div>
                                        @elseif($lead->course_id == 4)
                                        <div class="d-flex gap-1">
                                        <a href="{{ route('public.lead.hospital-admin.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-warning" title="Open Hospital Administration Registration Form">
                                            <i class="ti ti-external-link"></i>
                                        </a>
                                            <button type="button" class="btn btn-sm btn-outline-info copy-link-btn" 
                                                    data-url="{{ route('public.lead.hospital-admin.register', $lead->id) }}" 
                                                    title="Copy Hospital Administration Registration Link">
                                                <i class="ti ti-copy"></i>
                                            </button>
                                        </div>
                                        @elseif($lead->course_id == 5)
                                        <div class="d-flex gap-1">
                                        <a href="{{ route('public.lead.eschool.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-warning" title="Open E-School Registration Form">
                                            <i class="ti ti-external-link"></i>
                                        </a>
                                            <button type="button" class="btn btn-sm btn-outline-info copy-link-btn" 
                                                    data-url="{{ route('public.lead.eschool.register', $lead->id) }}" 
                                                    title="Copy E-School Registration Link">
                                                <i class="ti ti-copy"></i>
                                            </button>
                                        </div>
                                        @elseif($lead->course_id == 6)
                                        <div class="d-flex gap-1">
                                        <a href="{{ route('public.lead.eduthanzeel.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-warning" title="Open Eduthanzeel Registration Form">
                                            <i class="ti ti-external-link"></i>
                                        </a>
                                            <button type="button" class="btn btn-sm btn-outline-info copy-link-btn" 
                                                    data-url="{{ route('public.lead.eduthanzeel.register', $lead->id) }}" 
                                                    title="Copy Eduthanzeel Registration Link">
                                                <i class="ti ti-copy"></i>
                                            </button>
                                        </div>
                                        @elseif($lead->course_id == 7)
                                        <div class="d-flex gap-1">
                                        <a href="{{ route('public.lead.ttc.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-warning" title="Open TTC Registration Form">
                                            <i class="ti ti-external-link"></i>
                                        </a>
                                            <button type="button" class="btn btn-sm btn-outline-info copy-link-btn" 
                                                    data-url="{{ route('public.lead.ttc.register', $lead->id) }}" 
                                                    title="Copy TTC Registration Link">
                                                <i class="ti ti-copy"></i>
                                            </button>
                                        </div>
                                        @elseif($lead->course_id == 8)
                                        <div class="d-flex gap-1">
                                        <a href="{{ route('public.lead.hotel-mgmt.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-warning" title="Open Hotel Management Registration Form">
                                            <i class="ti ti-external-link"></i>
                                        </a>
                                            <button type="button" class="btn btn-sm btn-outline-info copy-link-btn" 
                                                    data-url="{{ route('public.lead.hotel-mgmt.register', $lead->id) }}" 
                                                    title="Copy Hotel Management Registration Link">
                                                <i class="ti ti-copy"></i>
                                            </button>
                                        </div>
                                        @elseif($lead->course_id == 9)
                                        <div class="d-flex gap-1">
                                        <a href="{{ route('public.lead.ugpg.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-warning" title="Open UG/PG Registration Form">
                                            <i class="ti ti-external-link"></i>
                                        </a>
                                            <button type="button" class="btn btn-sm btn-outline-info copy-link-btn" 
                                                    data-url="{{ route('public.lead.ugpg.register', $lead->id) }}" 
                                                    title="Copy UG/PG Registration Link">
                                                <i class="ti ti-copy"></i>
                                            </button>
                                        </div>
                                        @elseif($lead->course_id == 10)
                                        <div class="d-flex gap-1">
                                        <a href="{{ route('public.lead.python.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-warning" title="Open Python Registration Form">
                                            <i class="ti ti-external-link"></i>
                                        </a>
                                            <button type="button" class="btn btn-sm btn-outline-info copy-link-btn" 
                                                    data-url="{{ route('public.lead.python.register', $lead->id) }}" 
                                                    title="Copy Python Registration Link">
                                                <i class="ti ti-copy"></i>
                                            </button>
                                        </div>
                                        @elseif($lead->course_id == 11)
                                        <div class="d-flex gap-1">
                                        <a href="{{ route('public.lead.digital-marketing.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-warning" title="Open Digital Marketing Registration Form">
                                            <i class="ti ti-external-link"></i>
                                        </a>
                                            <button type="button" class="btn btn-sm btn-outline-info copy-link-btn" 
                                                    data-url="{{ route('public.lead.digital-marketing.register', $lead->id) }}" 
                                                    title="Copy Digital Marketing Registration Link">
                                                <i class="ti ti-copy"></i>
                                            </button>
                                        </div>
                                        @elseif($lead->course_id == 12)
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('public.lead.ai-automation.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-warning" title="Open AI Automation Registration Form">
                                                <i class="ti ti-external-link"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-info copy-link-btn" 
                                                    data-url="{{ route('public.lead.ai-automation.register', $lead->id) }}" 
                                                    title="Copy AI Automation Registration Link">
                                                <i class="ti ti-copy"></i>
                                            </button>
                                        </div>
                                        @elseif($lead->course_id == 13)
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('public.lead.web-dev.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-warning" title="Open Web Development & Designing Registration Form">
                                                <i class="ti ti-external-link"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-info copy-link-btn" 
                                                    data-url="{{ route('public.lead.web-dev.register', $lead->id) }}" 
                                                    title="Copy Web Development & Designing Registration Link">
                                                <i class="ti ti-copy"></i>
                                            </button>
                                        </div>
                                        @elseif($lead->course_id == 14)
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('public.lead.vibe-coding.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-warning" title="Open Vibe Coding Registration Form">
                                                <i class="ti ti-external-link"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-info copy-link-btn" 
                                                    data-url="{{ route('public.lead.vibe-coding.register', $lead->id) }}" 
                                                    title="Copy Vibe Coding Registration Link">
                                                <i class="ti ti-copy"></i>
                                            </button>
                                        </div>
                                        @elseif($lead->course_id == 15)
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('public.lead.graphic-designing.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-warning" title="Open Graphic Designing Registration Form">
                                                <i class="ti ti-external-link"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-info copy-link-btn" 
                                                    data-url="{{ route('public.lead.graphic-designing.register', $lead->id) }}" 
                                                    title="Copy Graphic Designing Registration Link">
                                                <i class="ti ti-copy"></i>
                                            </button>
                                        </div>
                                        @elseif($lead->course_id == 16)
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('public.lead.gmvss.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-outline-warning" title="Open GMVSS Registration Form">
                                                <i class="ti ti-external-link"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-info copy-link-btn" 
                                                    data-url="{{ route('public.lead.gmvss.register', $lead->id) }}" 
                                                    title="Copy GMVSS Registration Link">
                                                <i class="ti ti-copy"></i>
                                            </button>
                                        </div>
                                        @endif
                                        @endif
                                    </td>
                                    @endif
                                    <td>
                                        <small class="text-muted">{{ $lead->created_at->format('d-m-Y h:i A') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avtar avtar-s rounded-circle bg-light-primary me-2 d-flex align-items-center justify-content-center">
                                                <span class="f-16 fw-bold text-primary">{{ strtoupper(substr($lead->title, 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $lead->title }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($lead->isProfileIncomplete())
                                            <div class="me-2">
                                                <div class="progress" style="width: 60px; height: 8px;">
                                                    <div class="progress-bar 
                                                            @if($lead->profile_status == 'incomplete') bg-danger
                                                            @elseif($lead->profile_status == 'partial') bg-warning
                                                            @elseif($lead->profile_status == 'almost_complete') bg-info
                                                            @else bg-success
                                                            @endif"
                                                        role="progressbar"
                                                        style="width: {{ $lead->profile_completeness }}%"
                                                        aria-valuenow="{{ $lead->profile_completeness }}"
                                                        aria-valuemin="0"
                                                        aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="badge 
                                                    @if($lead->profile_status == 'incomplete') bg-danger
                                                    @elseif($lead->profile_status == 'partial') bg-warning
                                                    @elseif($lead->profile_status == 'almost_complete') bg-info
                                                    @else bg-success
                                                    @endif"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                title="Missing: {{ $missingFieldsDisplay }}{{ $missingFieldsCount > 5 ? '...' : '' }}">
                                                {{ $lead->profile_completeness }}%
                                            </span>
                                            @else
                                            <span class="badge bg-success">
                                                <i class="ti ti-check"></i> Complete
                                            </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone) }}</td>
                                    <td>{{ $lead->email ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ \App\Helpers\StatusHelper::getLeadStatusColorClass($lead->leadStatus->id) }}">
                                            {{ $lead->leadStatus->title }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($lead->interest_status)
                                        <span class="badge bg-{{ $lead->interest_status_color }}">
                                            {{ $lead->interest_status_label }}
                                        </span>
                                        @else
                                        <span class="badge bg-secondary">Not Set</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($lead->rating)
                                        <span class="badge bg-primary">{{ $lead->rating }}/10</span>
                                        @else
                                        <span class="badge bg-secondary">Not Rated</span>
                                        @endif
                                    </td>
                                    <td>{{ $lead->leadSource->title ?? '-' }}</td>
                                    <td>{{ $lead->course->title ?? '-' }}</td>
                                    <td>{{ $lead->telecaller->name ?? 'Unassigned' }}</td>
                                    <td>{{ $lead->place ?? '-' }}</td>
                                    <td>
                                        @if($lead->followup_date)
                                        <span class="badge bg-warning">{{ $lead->followup_date->format('M d, Y') }}</span>
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>
                                        {{-- Removed leadActivities query for performance - will load on-demand if needed --}}
                                        -
                                    </td>
                                    <td>{{ $lead->remarks ? $lead->remarks : '-' }}</td>
                                    <td>{{ $lead->created_at->format('M d, Y') }}</td>
                                    <td>{{ $lead->created_at->format('h:i A') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="19" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="ti ti-inbox f-48 mb-3 d-block"></i>
                                            No leads found
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <br>
                <hr>
                <br>

                <!-- Mobile Card View -->
                <div class="d-lg-none">
                    @forelse($leads as $index => $lead)
                    @php
                        // Cache expensive method calls to avoid repeated calculations
                        $missingFieldsMobile = $lead->getMissingFields();
                        $missingFieldsCountMobile = count($missingFieldsMobile);
                        $missingFieldsDisplayMobile = implode(', ', array_slice($missingFieldsMobile, 0, 5));
                    @endphp
                    <div class="card mb-2">
                        <div class="card-body p-3">
                            <!-- Lead Header -->
                            <div class="d-flex align-items-start justify-content-between mb-2">
                                <div class="d-flex align-items-center flex-grow-1">
                                    <div class="avtar avtar-s rounded-circle bg-light-primary me-2 d-flex align-items-center justify-content-center">
                                        <span class="f-14 fw-bold text-primary">{{ strtoupper(substr($lead->title, 0, 1)) }}</span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <small class="text-muted d-block f-10 mb-1">{{ $lead->created_at->format('d-m-Y h:i A') }}</small>
                                        <h6 class="mb-0 fw-bold f-14">{{ $lead->title }}</h6>
                                        <small class="text-muted f-11">#{{ $index + 1 }}</small>
                                    </div>
                                </div>
                                <!-- Action buttons in header -->
                                <div class="d-flex gap-1">
                                    <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary"
                                        onclick="show_large_modal('{{ route('leads.ajax-show', $lead->id) }}', 'View Lead')"
                                        title="View Lead">
                                        <i class="ti ti-eye f-12"></i>
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-sm btn-outline-secondary"
                                        onclick="show_ajax_modal('{{ route('leads.ajax-edit', $lead->id) }}', 'Edit Lead')"
                                        title="Edit Lead">
                                        <i class="ti ti-edit f-12"></i>
                                    </a>
                                    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin())
                                    <a href="javascript:void(0);" class="btn btn-sm btn-outline-danger"
                                        onclick="delete_modal('{{ route('leads.destroy', $lead->id) }}')"
                                        title="Delete Lead">
                                        <i class="ti ti-trash f-12"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>

                            <!-- Profile Completeness Indicator -->
                            <div class="mb-2">
                                @if($lead->isProfileIncomplete())
                                <div class="d-flex align-items-center">
                                    <small class="text-muted me-2 f-11">Profile:</small>
                                    <div class="progress me-2" style="width: 80px; height: 6px;">
                                        <div class="progress-bar 
                                                @if($lead->profile_status == 'incomplete') bg-danger
                                                @elseif($lead->profile_status == 'partial') bg-warning
                                                @elseif($lead->profile_status == 'almost_complete') bg-info
                                                @else bg-success
                                                @endif"
                                            role="progressbar"
                                            style="width: {{ $lead->profile_completeness }}%"
                                            aria-valuenow="{{ $lead->profile_completeness }}"
                                            aria-valuemin="0"
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                    <span class="badge 
                                            @if($lead->profile_status == 'incomplete') bg-danger
                                            @elseif($lead->profile_status == 'partial') bg-warning
                                            @elseif($lead->profile_status == 'almost_complete') bg-info
                                            @else bg-success
                                            @endif f-10"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="Missing: {{ $missingFieldsDisplayMobile }}{{ $missingFieldsCountMobile > 5 ? '...' : '' }}">
                                        {{ $lead->profile_completeness }}%
                                    </span>
                                </div>
                                @if($missingFieldsCountMobile > 0)
                                <div class="mt-1">
                                    <small class="text-muted f-10">
                                        Missing: {{ $missingFieldsDisplayMobile }}{{ $missingFieldsCountMobile > 5 ? '...' : '' }}
                                    </small>
                                </div>
                                @endif
                                @else
                                <div class="d-flex align-items-center">
                                    <small class="text-muted me-2 f-11">Profile:</small>
                                    <span class="badge bg-success f-10">
                                        <i class="ti ti-check me-1"></i> Complete
                                    </span>
                                </div>
                                @endif
                            </div>

                            <!-- Lead Details - Compact Layout -->
                            <div class="row g-1 mb-2">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-phone f-12 text-muted me-1"></i>
                                        <small class="text-muted f-11">{{ \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone) }}</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-mail f-12 text-muted me-1"></i>
                                        <small class="text-muted f-11">{{ $lead->email ?? '-' }}</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-circle f-12 text-muted me-1"></i>
                                        <span class="badge {{ \App\Helpers\StatusHelper::getLeadStatusColorClass($lead->leadStatus->id) }} f-11">
                                            {{ $lead->leadStatus->title }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-flame f-12 text-muted me-1"></i>
                                        @if($lead->interest_status)
                                        <span class="badge bg-{{ $lead->interest_status_color }} f-10">
                                            {{ $lead->interest_status_label }}
                                        </span>
                                        @else
                                        <span class="badge bg-secondary f-10">Not Set</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-star f-12 text-muted me-1"></i>
                                        @if($lead->rating)
                                        <span class="badge bg-primary f-10">{{ $lead->rating }}/10</span>
                                        @else
                                        <span class="badge bg-secondary f-10">Not Rated</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-user f-12 text-muted me-1"></i>
                                        <small class="text-muted f-11">{{ $lead->telecaller->name ?? 'Unassigned' }}</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-book f-12 text-muted me-1"></i>
                                        <small class="text-muted f-11">{{ $lead->course->title ?? '-' }}</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-calendar f-12 text-muted me-1"></i>
                                        <small class="text-muted f-11">{{ $lead->created_at->format('M d') }}</small>
                                    </div>
                                </div>
                                @if($lead->followup_date)
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-clock f-12 text-muted me-1"></i>
                                        <span class="badge bg-warning f-10">{{ $lead->followup_date->format('M d') }}</span>
                                    </div>
                                </div>
                                @endif
                                {{-- Removed leadActivities query for performance --}}
                                @if($lead->remarks)
                                <div class="col-12">
                                    <div class="d-flex align-items-start">
                                        <i class="ti ti-note f-12 text-muted me-1 mt-1"></i>
                                        <small class="text-muted f-11" title="{{ $lead->remarks }}">{{ Str::limit($lead->remarks, 50) }}</small>
                                    </div>
                                </div>
                                @endif

                                <!-- Registration Details Section -->
                                @if($lead->studentDetails)
                                <div class="col-12 mt-2">
                                    <div class="border-top pt-2">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <small class="text-muted f-11 fw-bold">Registration Details:</small>
                                            <span class="badge bg-success f-10">Form Submitted</span>
                                        </div>
                                        <div class="row g-1">
                                            <div class="col-6">
                                                <small class="text-muted f-10">Course:</small>
                                                <div class="fw-medium f-11">{{ $lead->studentDetails->_course_title ?? ($courseName[$lead->studentDetails->course_id] ?? 'Unknown') }}</div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted f-10">Status:</small>
                                                <div>
                                                    <span class="badge 
                                                        @if($lead->studentDetails->status == 'approved') bg-success
                                                        @elseif($lead->studentDetails->status == 'rejected') bg-danger
                                                        @else bg-warning
                                                        @endif f-10">
                                                        {{ ucfirst($lead->studentDetails->status ?? 'Pending') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            @if($isAdminOrSuperAdmin || $isTelecallerRole || $isAcademicAssistant || $isAdmissionCounsellor)
                                            <a href="{{ route('leads.registration-details', $lead->id) }}"
                                                class="btn btn-sm btn-outline-primary"
                                                title="View Registration Details">
                                                <i class="ti ti-eye me-1"></i>View Details
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <!-- Action Buttons - Enhanced -->
                            <div class="d-flex gap-1 flex-wrap justify-content-between">
                                <!-- Left side - Status and Convert buttons -->
                                <div class="d-flex gap-1">
                                    <a href="javascript:void(0);" class="btn btn-sm btn-outline-warning"
                                        onclick="show_ajax_modal('{{ route('leads.status-update', $lead->id) }}', 'Update Status')"
                                        title="Update Status">
                                        <i class="ti ti-arrow-up f-12"></i>
                                    </a>
                                    @if(!$lead->is_converted && $lead->studentDetails && (strtolower($lead->studentDetails->status ?? '') === 'approved'))
                                    <a href="javascript:void(0);" class="btn btn-sm btn-outline-success"
                                        onclick="show_ajax_modal('{{ route('leads.convert', $lead->id) }}', 'Convert Lead')"
                                        title="Convert Lead">
                                        <i class="ti ti-refresh f-12"></i>
                                    </a>
                                    @endif
                                    @if($lead->lead_status_id == 6)
                                    <a href="https://docs.google.com/forms/d/e/1FAIpQLSchtc8xlKUJehZNmzoKTkRvwLwk4-SGjzKSHM2UFToAhgdTlQ/viewform?usp=sf_link"
                                        target="_blank"
                                        class="btn btn-sm btn-outline-info"
                                        title="Demo Conduction Form">
                                        <i class="ti ti-file-text f-12"></i>
                                    </a>
                                    @endif
                                </div>

                                <!-- Right side - Call and Logs buttons -->
                                <div class="d-flex gap-1">
                                    @if($lead->phone && is_telecaller())
                                    @php
                                    $currentUserId = session('user_id') ?? (\App\Helpers\AuthHelper::getCurrentUserId() ?? 0);
                                    @endphp
                                    @if($currentUserId > 0)
                                    <button class="btn btn-sm btn-success voxbay-call-btn"
                                        data-lead-id="{{ $lead->id }}"
                                        data-telecaller-id="{{ $currentUserId }}"
                                        title="Call Lead">
                                        <i class="ti ti-phone f-12"></i>
                                    </button>
                                    @endif
                                    @endif
                                    <a href="{{ route('leads.call-logs', $lead) }}"
                                        class="btn btn-sm btn-info"
                                        title="View Call Logs">
                                        <i class="ti ti-phone-call f-12"></i>
                                    </a>
                                    <br>
                                    <hr><br>
                                    @if($isAdminOrSuperAdmin || $isTelecallerRole || $isAcademicAssistant || $isAdmissionCounsellor)
                                    @if($lead->course_id == 1)
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('public.lead.nios.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-warning" title="Open NIOS Registration Form">
                                            <i class="ti ti-external-link f-12"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-info copy-link-btn" 
                                                data-url="{{ route('public.lead.nios.register', $lead->id) }}" 
                                                title="Copy NIOS Registration Link">
                                            <i class="ti ti-copy f-12"></i>
                                        </button>
                                    </div>
                                    @elseif($lead->course_id == 2)
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('public.lead.bosse.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-warning" title="Open BOSSE Registration Form">
                                            <i class="ti ti-external-link f-12"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-info copy-link-btn" 
                                                data-url="{{ route('public.lead.bosse.register', $lead->id) }}" 
                                                title="Copy BOSSE Registration Link">
                                            <i class="ti ti-copy f-12"></i>
                                        </button>
                                    </div>
                                    @elseif($lead->course_id == 3)
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('public.lead.medical-coding.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-warning" title="Open Medical Coding Registration Form">
                                            <i class="ti ti-external-link f-12"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-info copy-link-btn" 
                                                data-url="{{ route('public.lead.medical-coding.register', $lead->id) }}" 
                                                title="Copy Medical Coding Registration Link">
                                            <i class="ti ti-copy f-12"></i>
                                        </button>
                                    </div>
                                    @elseif($lead->course_id == 4)
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('public.lead.hospital-admin.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-warning" title="Open Hospital Administration Registration Form">
                                            <i class="ti ti-external-link f-12"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-info copy-link-btn" 
                                                data-url="{{ route('public.lead.hospital-admin.register', $lead->id) }}" 
                                                title="Copy Hospital Administration Registration Link">
                                            <i class="ti ti-copy f-12"></i>
                                        </button>
                                    </div>
                                    @elseif($lead->course_id == 5)
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('public.lead.eschool.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-warning" title="Open E-School Registration Form">
                                            <i class="ti ti-external-link f-12"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-info copy-link-btn" 
                                                data-url="{{ route('public.lead.eschool.register', $lead->id) }}" 
                                                title="Copy E-School Registration Link">
                                            <i class="ti ti-copy f-12"></i>
                                        </button>
                                    </div>
                                    @elseif($lead->course_id == 6)
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('public.lead.eduthanzeel.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-warning" title="Open Eduthanzeel Registration Form">
                                            <i class="ti ti-external-link f-12"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-info copy-link-btn" 
                                                data-url="{{ route('public.lead.eduthanzeel.register', $lead->id) }}" 
                                                title="Copy Eduthanzeel Registration Link">
                                            <i class="ti ti-copy f-12"></i>
                                        </button>
                                    </div>
                                    @elseif($lead->course_id == 7)
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('public.lead.ttc.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-warning" title="Open TTC Registration Form">
                                            <i class="ti ti-external-link f-12"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-info copy-link-btn" 
                                                data-url="{{ route('public.lead.ttc.register', $lead->id) }}" 
                                                title="Copy TTC Registration Link">
                                            <i class="ti ti-copy f-12"></i>
                                        </button>
                                    </div>
                                    @elseif($lead->course_id == 8)
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('public.lead.hotel-mgmt.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-warning" title="Open Hotel Management Registration Form">
                                            <i class="ti ti-external-link f-12"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-info copy-link-btn" 
                                                data-url="{{ route('public.lead.hotel-mgmt.register', $lead->id) }}" 
                                                title="Copy Hotel Management Registration Link">
                                            <i class="ti ti-copy f-12"></i>
                                        </button>
                                    </div>
                                    @elseif($lead->course_id == 9)
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('public.lead.ugpg.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-warning" title="Open UG/PG Registration Form">
                                            <i class="ti ti-external-link f-12"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-info copy-link-btn" 
                                                data-url="{{ route('public.lead.ugpg.register', $lead->id) }}" 
                                                title="Copy UG/PG Registration Link">
                                            <i class="ti ti-copy f-12"></i>
                                        </button>
                                    </div>
                                    @elseif($lead->course_id == 10)
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('public.lead.python.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-warning" title="Open Python Registration Form">
                                            <i class="ti ti-external-link f-12"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-info copy-link-btn" 
                                                data-url="{{ route('public.lead.python.register', $lead->id) }}" 
                                                title="Copy Python Registration Link">
                                            <i class="ti ti-copy f-12"></i>
                                        </button>
                                    </div>
                                    @elseif($lead->course_id == 11)
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('public.lead.digital-marketing.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-warning" title="Open Digital Marketing Registration Form">
                                            <i class="ti ti-external-link f-12"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-info copy-link-btn" 
                                                data-url="{{ route('public.lead.digital-marketing.register', $lead->id) }}" 
                                                title="Copy Digital Marketing Registration Link">
                                            <i class="ti ti-copy f-12"></i>
                                        </button>
                                    </div>
                                    @elseif($lead->course_id == 12)
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('public.lead.ai-automation.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-warning" title="Open AI Automation Registration Form">
                                            <i class="ti ti-external-link f-12"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-info copy-link-btn" 
                                                data-url="{{ route('public.lead.ai-automation.register', $lead->id) }}" 
                                                title="Copy AI Automation Registration Link">
                                            <i class="ti ti-copy f-12"></i>
                                        </button>
                                    </div>
                                    @elseif($lead->course_id == 13)
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('public.lead.web-dev.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-warning" title="Open Web Development & Designing Registration Form">
                                            <i class="ti ti-external-link f-12"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-info copy-link-btn" 
                                                data-url="{{ route('public.lead.web-dev.register', $lead->id) }}" 
                                                title="Copy Web Development & Designing Registration Link">
                                            <i class="ti ti-copy f-12"></i>
                                        </button>
                                    </div>
                                    @elseif($lead->course_id == 14)
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('public.lead.vibe-coding.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-warning" title="Open Vibe Coding Registration Form">
                                            <i class="ti ti-external-link f-12"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-info copy-link-btn" 
                                                data-url="{{ route('public.lead.vibe-coding.register', $lead->id) }}" 
                                                title="Copy Vibe Coding Registration Link">
                                            <i class="ti ti-copy f-12"></i>
                                        </button>
                                    </div>
                                    @elseif($lead->course_id == 15)
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('public.lead.graphic-designing.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-warning" title="Open Graphic Designing Registration Form">
                                            <i class="ti ti-external-link f-12"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-info copy-link-btn" 
                                                data-url="{{ route('public.lead.graphic-designing.register', $lead->id) }}" 
                                                title="Copy Graphic Designing Registration Link">
                                            <i class="ti ti-copy f-12"></i>
                                        </button>
                                    </div>
                                    @elseif($lead->course_id == 16)
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('public.lead.gmvss.register', $lead->id) }}" target="_blank" class="btn btn-sm btn-warning" title="Open GMVSS Registration Form">
                                            <i class="ti ti-external-link f-12"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-info copy-link-btn" 
                                                data-url="{{ route('public.lead.gmvss.register', $lead->id) }}" 
                                                title="Copy GMVSS Registration Link">
                                            <i class="ti ti-copy f-12"></i>
                                        </button>
                                    </div>
                                    @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <div class="text-muted">
                            <i class="ti ti-inbox f-48 mb-3 d-block"></i>
                            <h5>No leads found</h5>
                            <p>Try adjusting your filters or add a new lead.</p>
                        </div>
                    </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->


@endsection

@push('scripts')
<style>
    /* Fix DataTables responsive dropdown icon issue */
    .dtr-control {
        position: relative;
        cursor: pointer;
    }

    .dtr-control:before {
        content: '+';
        display: inline-block;
        width: 20px;
        height: 20px;
        line-height: 18px;
        text-align: center;
        border: 1px solid #ddd;
        border-radius: 3px;
        background-color: #f8f9fa;
        color: #666;
        font-weight: bold;
        margin-right: 8px;
    }

    .dtr-control.dtr-expanded:before {
        content: '-';
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    /* Remove the problematic sorting_1 class styling */
    .dtr-control.sorting_1:before {
        content: '+';
    }

    /* Improve table responsiveness */
    .table-responsive {
        border: none;
    }

    #leadsTable {
        margin-bottom: 0;
    }

    #leadsTable thead th {
        border-top: none;
        font-weight: 600;
        background-color: #f8f9fa;
        white-space: nowrap;
    }

    #leadsTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
    }

    /* Fix action buttons in responsive mode */
    .dtr-details {
        background-color: #f8f9fa;
        padding: 10px;
        border-left: 3px solid #007bff;
    }

    .dtr-details li {
        margin-bottom: 5px;
    }

    /* Improve mobile card layout */
    @media (max-width: 991.98px) {
        .card-body {
            padding: 0.75rem;
        }

        .mobile-card {
            margin-bottom: 0.5rem;
        }
    }

    /* Additional responsive improvements */
    @media (max-width: 1200px) {
        .table-responsive {
            font-size: 0.875rem;
        }

        #leadsTable th,
        #leadsTable td {
            padding: 0.5rem 0.25rem;
        }
    }

    /* Enhanced action button styling */
    .btn-sm {
        min-width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-sm i {
        font-size: 12px;
    }

    /* Mobile action buttons layout */
    @media (max-width: 991.98px) {
        .d-flex.gap-1 {
            gap: 0.25rem !important;
        }

        .btn-sm {
            min-width: 28px;
            height: 28px;
            padding: 0.25rem;
        }
    }

    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.8rem;
        }

        #leadsTable th,
        #leadsTable td {
            padding: 0.375rem 0.125rem;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    }

    /* Fix DataTables info and pagination on mobile */
    .dataTables_info,
    .dataTables_paginate {
        font-size: 0.875rem;
    }

    @media (max-width: 576px) {

        .dataTables_info,
        .dataTables_paginate {
            font-size: 0.75rem;
        }

        .dataTables_length,
        .dataTables_filter {
            margin-bottom: 0.5rem;
        }
    }
    
    /* Copy link button styling */
    .copy-link-btn {
        transition: all 0.3s ease;
    }
    
    .copy-link-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    
    .copy-link-btn.btn-success {
        animation: pulse 0.6s ease-in-out;
    }
    
    .copy-link-btn.processing {
        pointer-events: none;
        opacity: 0.7;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
</style>
<script>
    // Initialize DataTables asynchronously to prevent blocking
    $(document).ready(function() {
        // ULTRA-OPTIMIZED DataTables for 410+ leads - Performance Critical
        // Prevent global initialization for this table
        $('#leadsTable').removeClass('data_table_basic');
        
        // Use setTimeout to defer initialization and allow page to render first
        setTimeout(function() {
            // Destroy existing instance if any
            if ($.fn.DataTable.isDataTable('#leadsTable')) {
                $('#leadsTable').DataTable().destroy();
            }
            
            // Initialize with maximum performance optimizations
            // deferRender is CRITICAL - it only processes visible rows initially
            var leadsTable = $('#leadsTable').DataTable({
                deferRender: true, // CRITICAL: Only process visible rows (25 instead of 410)
                processing: true,
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                order: [[3, 'desc']], // Sort by created_at (column 3)
                dom: "Bfrtip",
                buttons: ["csv", "excel", "print", "pdf"],
                stateSave: true,
                scrollCollapse: true,
                // Performance optimizations
                autoWidth: false, // Disable expensive auto-width calculation
                scrollX: true, // Enable horizontal scrolling
                searchHighlight: false, // Disable expensive search highlighting
                // Optimize rendering - only process visible rows
                drawCallback: function(settings) {
                    // Lazy load tooltips only for visible rows
                    var api = this.api();
                    $(api.rows({page: 'current'}).nodes()).find('[data-bs-toggle="tooltip"]').tooltip();
                },
                language: {
                    processing: "Loading...",
                    emptyTable: "No data available",
                    zeroRecords: "No matching records found",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    search: "Search:",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                }
            });
        }, 50); // Small delay to allow page to render first

        // Handle global search form submission
        $('.header-search form, .drp-search form').on('submit', function(e) {
            e.preventDefault();
            const searchValue = $(this).find('input[name="search_key"]').val().trim();
            if (searchValue) {
                window.location.href = '{{ route("leads.index") }}?search_key=' + encodeURIComponent(searchValue);
            } else {
                window.location.href = '{{ route("leads.index") }}';
            }
        });

        // Handle search input enter key
        $('.header-search input, .drp-search input').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                $(this).closest('form').submit();
            }
        });

        // Action buttons are now directly accessible without dropdown
        // All functionality is handled by onclick attributes on the buttons
        
        // Copy link functionality for all registration forms
        // Remove any existing event listeners first to prevent double execution
        $('.copy-link-btn').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Prevent double execution by checking if already processing
            if ($(this).hasClass('processing')) {
                return false;
            }
            
            // Mark as processing
            $(this).addClass('processing');
            
            const url = $(this).data('url');
            // Check if URL already contains protocol (http/https)
            const fullUrl = url.startsWith('http') ? url : window.location.origin + url;
            
            // Create a temporary input element
            const tempInput = document.createElement('input');
            tempInput.value = fullUrl;
            document.body.appendChild(tempInput);
            
            // Select and copy the text
            tempInput.select();
            tempInput.setSelectionRange(0, 99999); // For mobile devices
            
            try {
                document.execCommand('copy');
                
                // Show success feedback
                const originalIcon = $(this).find('i').attr('class');
                $(this).find('i').removeClass().addClass('ti ti-check');
                $(this).removeClass('btn-outline-info btn-info').addClass('btn-success');
                
                // Show toast notification
                showToast('Registration link copied to clipboard!', 'success');
                
                // Reset button after 2 seconds
                setTimeout(() => {
                    $(this).find('i').removeClass().addClass(originalIcon);
                    $(this).removeClass('btn-success').addClass('btn-outline-info');
                    $(this).removeClass('processing'); // Remove processing flag
                }, 2000);
                
            } catch (err) {
                console.error('Failed to copy: ', err);
                showToast('Failed to copy link. Please try again.', 'error');
                $(this).removeClass('processing'); // Remove processing flag on error
            }
            
            // Remove the temporary input
            document.body.removeChild(tempInput);
        });
    });

    // Function to show toast notifications
    function showToast(message, type = 'info') {
        // Create toast element
        const toast = $(`
            <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="ti ti-${type === 'success' ? 'check' : 'alert-circle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `);
        
        // Add to toast container or create one
        let toastContainer = $('.toast-container');
        if (toastContainer.length === 0) {
            toastContainer = $('<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>');
            $('body').append(toastContainer);
        }
        
        toastContainer.append(toast);
        
        // Initialize and show toast
        const bsToast = new bootstrap.Toast(toast[0]);
        bsToast.show();
        
        // Remove toast element after it's hidden
        toast.on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }

    // Function to show registration details in a modal
</script>
@endpush