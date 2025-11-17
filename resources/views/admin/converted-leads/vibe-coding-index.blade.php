@extends('layouts.mantis')

@section('title', 'Vibe Coding Converted Leads')

@section('content')
<style>
    .table td {
        white-space: nowrap;
        vertical-align: middle;
    }
    .table td .btn-group {
        white-space: nowrap;
    }
    .table td .inline-edit {
        white-space: nowrap;
    }
    .table td .display-value {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
        display: inline-block;
    }
</style>
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Vibe Coding Converted Leads Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.converted-leads.index') }}">Converted Leads</a></li>
                    <li class="breadcrumb-item">Vibe Coding</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Course Filter Buttons ] start -->
@if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_finance())
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Filter by Course</h6>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-list"></i> All Converted Leads
                    </a>
                    <a href="{{ route('admin.nios-converted-leads.index') }}" class="btn btn-outline-success">
                        <i class="ti ti-school"></i> NIOS Converted Leads
                    </a>
                    <a href="{{ route('admin.bosse-converted-leads.index') }}" class="btn btn-outline-warning">
                        <i class="ti ti-school-2"></i> BOSSE Converted Leads
                    </a>
                    <a href="{{ route('admin.ugpg-converted-leads.index') }}" class="btn btn-outline-warning">
                        <i class="ti ti-graduation"></i> UG/PG Converted Leads
                    </a>
                    <a href="{{ route('admin.hotel-management-converted-leads.index') }}" class="btn btn-outline-info">
                        <i class="ti ti-building"></i> Hotel Management Converted Leads
                    </a>
                    <a href="{{ route('admin.gmvss-converted-leads.index') }}" class="btn btn-outline-info">
                        <i class="ti ti-certificate"></i> GMVSS Converted Leads
                    </a>
                    <a href="{{ route('admin.ai-python-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-code"></i> AI with Python Converted Leads
                    </a>
                    <a href="{{ route('admin.digital-marketing-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-marketing"></i> Digital Marketing Converted Leads
                    </a>
                    <a href="{{ route('admin.ai-automation-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-robot"></i> AI Automation Converted Leads
                    </a>
                    <a href="{{ route('admin.web-development-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-world"></i> Web Development & Designing Converted Leads
                    </a>
                    <a href="{{ route('admin.vibe-coding-converted-leads.index') }}" class="btn btn-primary active">
                        <i class="ti ti-device-desktop"></i> Vibe Coding Converted Leads
                    </a>
                    <a href="{{ route('admin.graphic-designing-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-palette"></i> Graphic Designing Converted Leads
                    </a>
                    <a href="{{ route('admin.eduthanzeel-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-school"></i> Eduthanzeel Converted Leads
                    </a>
                    <a href="{{ route('admin.e-school-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-device-laptop"></i> E-School Converted Leads
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<!-- [ Course Filter Buttons ] end -->

<!-- [ Mentor List ] start -->
@if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Mentor List</h6>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.mentor-bosse-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Bosse Converted Mentor List
                    </a>
                    <a href="{{ route('admin.mentor-nios-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> NIOS Converted Mentor List
                    </a>
                    <a href="{{ route('admin.mentor-eschool-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> E-School Converted Mentor List
                    </a>
                    <a href="{{ route('admin.mentor-eduthanzeel-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Eduthanzeel Converted Mentor List
                    </a>
                    <a href="{{ route('admin.gmvss-mentor-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> GMVSS Mentor List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<!-- [ Mentor List ] end -->

<!-- [ Support List ] start -->
@if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_support_team())
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Support List</h6>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.support-bosse-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Bosse Converted Support List
                    </a>
                    <a href="{{ route('admin.support-nios-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> NIOS Converted Support List
                    </a>
                    <a href="{{ route('admin.support-hotel-management-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Hotel Management Converted Support List
                    </a>
                    <a href="{{ route('admin.support-gmvss-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> GMVSS Converted Support List
                    </a>
                    <a href="{{ route('admin.support-ai-python-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> AI with Python Converted Support List
                    </a>
                    <a href="{{ route('admin.support-digital-marketing-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Digital Marketing Converted Support List
                    </a>
                    <a href="{{ route('admin.support-ai-automation-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> AI Automation Converted Support List
                    </a>
                    <a href="{{ route('admin.support-web-development-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Web Development & Designing Converted Support List
                    </a>
                    <a href="{{ route('admin.support-vibe-coding-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Vibe Coding Converted Support List
                    </a>
                    <a href="{{ route('admin.support-graphic-designing-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Graphic Designing Converted Support List
                    </a>
                    <a href="{{ route('admin.support-eduthanzeel-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> Eduthanzeel Converted Support List
                    </a>
                    <a href="{{ route('admin.support-e-school-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-headphones"></i> E-School Converted Support List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<!-- [ Support List ] end -->

<!-- [ Filter Section ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Filter Vibe Coding Converted Leads</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.vibe-coding-converted-leads.index') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search"
                                value="{{ request('search') }}" placeholder="Name, Phone, Email, Register Number">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="batch_id" class="form-label">Batch</label>
                            <select class="form-select" id="batch_id" name="batch_id">
                                <option value="">All Batches</option>
                                @foreach($batches as $batch)
                                <option value="{{ $batch->id }}" {{ request('batch_id') == $batch->id ? 'selected' : '' }}>
                                    {{ $batch->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="admission_batch_id" class="form-label">Admission Batch</label>
                            <select class="form-select" id="admission_batch_id" name="admission_batch_id" data-selected="{{ request('admission_batch_id') }}">
                                <option value="">All Admission Batches</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from"
                                value="{{ request('date_from') }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to"
                                value="{{ request('date_to') }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="call_status" class="form-label">Call Status</label>
                            <select class="form-select" id="call_status" name="call_status">
                                <option value="">All</option>
                                <option value="Call Not Answered" {{ request('call_status')==='Call Not Answered' ? 'selected' : '' }}>Call Not Answered</option>
                                <option value="Switched Off" {{ request('call_status')==='Switched Off' ? 'selected' : '' }}>Switched Off</option>
                                <option value="Line Busy" {{ request('call_status')==='Line Busy' ? 'selected' : '' }}>Line Busy</option>
                                <option value="Student Asks to Call Later" {{ request('call_status')==='Student Asks to Call Later' ? 'selected' : '' }}>Student Asks to Call Later</option>
                                <option value="Lack of Interest in Conversation" {{ request('call_status')==='Lack of Interest in Conversation' ? 'selected' : '' }}>Lack of Interest in Conversation</option>
                                <option value="Wrong Contact" {{ request('call_status')==='Wrong Contact' ? 'selected' : '' }}>Wrong Contact</option>
                                <option value="Inconsistent Responses" {{ request('call_status')==='Inconsistent Responses' ? 'selected' : '' }}>Inconsistent Responses</option>
                                <option value="Task Complete" {{ request('call_status')==='Task Complete' ? 'selected' : '' }}>Task Complete</option>
                                <option value="Admission cancel" {{ request('call_status')==='Admission cancel' ? 'selected' : '' }}>Admission cancel</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="class_information" class="form-label">Class Information</label>
                            <select class="form-select" id="class_information" name="class_information">
                                <option value="">All</option>
                                <option value="phone call" {{ request('class_information')==='phone call' ? 'selected' : '' }}>Phone Call</option>
                                <option value="whatsapp" {{ request('class_information')==='whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="orientation_class_status" class="form-label">Orientation Class Status</label>
                            <select class="form-select" id="orientation_class_status" name="orientation_class_status">
                                <option value="">All</option>
                                <option value="Participated" {{ request('orientation_class_status')==='Participated' ? 'selected' : '' }}>Participated</option>
                                <option value="Did not participated" {{ request('orientation_class_status')==='Did not participated' ? 'selected' : '' }}>Did not participated</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="whatsapp_group_status" class="form-label">WhatsApp Group Status</label>
                            <select class="form-select" id="whatsapp_group_status" name="whatsapp_group_status">
                                <option value="">All</option>
                                <option value="sent link" {{ request('whatsapp_group_status')==='sent link' ? 'selected' : '' }}>Sent Link</option>
                                <option value="task complete" {{ request('whatsapp_group_status')==='task complete' ? 'selected' : '' }}>Task Complete</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="class_status" class="form-label">Class Status</label>
                            <select class="form-select" id="class_status" name="class_status">
                                <option value="">All</option>
                                <option value="Running" {{ request('class_status')==='Running' ? 'selected' : '' }}>Running</option>
                                <option value="Cancel" {{ request('class_status')==='Cancel' ? 'selected' : '' }}>Cancel</option>
                                <option value="complete" {{ request('class_status')==='complete' ? 'selected' : '' }}>Complete</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-search"></i> <span class="d-none d-sm-inline">Filter</span>
                                </button>
                                <a href="{{ route('admin.vibe-coding-converted-leads.index') }}" class="btn btn-secondary">
                                    <i class="ti ti-x"></i> <span class="d-none d-sm-inline">Clear</span>
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
                <h5 class="mb-0">Vibe Coding Converted Leads List</h5>
            </div>
            <div class="card-body">
                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover data_table_basic" id="vibeCodingTable">
                        <thead>
                            <tr>
                                <th>SL No</th>
                                <th>Academic</th>
                                <th>Support</th>
                                <th>Converted Date</th>
                                <th>Registration Number</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Batch</th>
                                <th>Admission Batch</th>
                                <th>Email</th>
                                <th>Call Status</th>
                                <th>Class Information</th>
                                <th>Orientation Class Status</th>
                                <th>Class Starting Date</th>
                                <th>Class Ending Date</th>
                                <th>WhatsApp Group Status</th>
                                <th>Class Time</th>
                                <th>Class Status</th>
                                <th>Complete/Cancel Date</th>
                                <th>Remarks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($convertedLeads as $index => $convertedLead)
                            <tr>
                                @php
                                    $canToggleAcademic = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor();
                                    $canToggleSupport = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_support_team();
                                @endphp
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @include('admin.converted-leads.partials.status-badge', [
                                        'convertedLead' => $convertedLead,
                                        'type' => 'academic',
                                        'showToggle' => $canToggleAcademic,
                                        'toggleUrl' => $canToggleAcademic ? route('admin.converted-leads.toggle-academic-verify', $convertedLead->id) : null,
                                        'title' => 'academic',
                                        'useModal' => true
                                    ])
                                </td>
                                <td>
                                    @include('admin.converted-leads.partials.status-badge', [
                                        'convertedLead' => $convertedLead,
                                        'type' => 'support',
                                        'showToggle' => $canToggleSupport,
                                        'toggleUrl' => $canToggleSupport ? route('admin.support-converted-leads.toggle-support-verify', $convertedLead->id) : null,
                                        'title' => 'support',
                                        'useModal' => true
                                    ])
                                </td>
                                <td>{{ $convertedLead->created_at->format('d-m-Y') }}</td>
                                <td>
                                    <div class="inline-edit" data-field="registration_number" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->registration_number }}">
                                        <span class="display-value">{{ $convertedLead->studentDetails?->registration_number ?? '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $convertedLead->name }}</td>
                                <td>
                                    <div class="inline-edit" data-field="phone" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->phone }}">
                                        <span class="display-value">{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                    <div class="d-none inline-code-value" data-field="code" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->code }}"></div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="batch_id" data-id="{{ $convertedLead->id }}" data-course-id="{{ $convertedLead->course_id }}" data-current-id="{{ $convertedLead->batch_id }}">
                                        <span class="display-value">{{ $convertedLead->batch ? $convertedLead->batch->title : 'N/A' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="admission_batch_id" data-id="{{ $convertedLead->id }}" data-batch-id="{{ $convertedLead->batch_id }}" data-current-id="{{ $convertedLead->admission_batch_id }}">
                                        <span class="display-value">{{ $convertedLead->admissionBatch ? $convertedLead->admissionBatch->title : 'N/A' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $convertedLead->email ?? '-' }}</td>
                                <td>
                                    <div class="inline-edit" data-field="call_status" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->call_status }}">
                                        <span class="display-value">{{ $convertedLead->studentDetails?->call_status ?? '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="class_information" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->class_information }}">
                                        <span class="display-value">{{ $convertedLead->studentDetails?->class_information ?? '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="orientation_class_status" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->orientation_class_status }}">
                                        <span class="display-value">{{ $convertedLead->studentDetails?->orientation_class_status ?? '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="class_starting_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->class_starting_date ? \Carbon\Carbon::parse($convertedLead->studentDetails->class_starting_date)->format('d-m-Y') : '' }}">
                                        <span class="display-value">{{ $convertedLead->studentDetails?->class_starting_date ? \Carbon\Carbon::parse($convertedLead->studentDetails->class_starting_date)->format('d-m-Y') : '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="class_ending_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->class_ending_date ? \Carbon\Carbon::parse($convertedLead->studentDetails->class_ending_date)->format('d-m-Y') : '' }}">
                                        <span class="display-value">{{ $convertedLead->studentDetails?->class_ending_date ? \Carbon\Carbon::parse($convertedLead->studentDetails->class_ending_date)->format('d-m-Y') : '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="whatsapp_group_status" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->whatsapp_group_status }}">
                                        <span class="display-value">{{ $convertedLead->studentDetails?->whatsapp_group_status ?? '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="class_time" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->class_time }}">
                                        <span class="display-value">{{ $convertedLead->studentDetails?->class_time ? \Carbon\Carbon::parse($convertedLead->studentDetails->class_time)->format('h:i A') : '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="class_status" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->class_status }}">
                                        <span class="display-value">{{ $convertedLead->studentDetails?->class_status ?? '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="complete_cancel_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->complete_cancel_date ? \Carbon\Carbon::parse($convertedLead->studentDetails->complete_cancel_date)->format('d-m-Y') : '' }}">
                                        <span class="display-value">{{ $convertedLead->studentDetails?->complete_cancel_date ? \Carbon\Carbon::parse($convertedLead->studentDetails->complete_cancel_date)->format('d-m-Y') : '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="remarks" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->remarks }}">
                                        <span class="display-value">{{ $convertedLead->studentDetails?->remarks ?? '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.converted-leads.show', $convertedLead->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.invoices.index', $convertedLead->id) }}" class="btn btn-sm btn-success" title="View Invoice">
                                            <i class="ti ti-receipt"></i>
                                        </a>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                        <button type="button" class="btn btn-sm btn-info update-register-btn" title="Update Register Number"
                                            data-url="{{ route('admin.converted-leads.update-register-number-modal', $convertedLead->id) }}"
                                            data-title="Update Register Number">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @if($convertedLead->register_number)
                                            @php
                                                $idCard = \App\Models\ConvertedLeadIdCard::where('converted_lead_id', $convertedLead->id)->first();
                                            @endphp
                                            @if($idCard)
                                                <a href="{{ route('admin.converted-leads.id-card-view', $convertedLead->id) }}" class="btn btn-sm btn-success" title="View ID Card" target="_blank">
                                                    <i class="ti ti-id"></i>
                                                </a>
                                            @else
                                                <form action="{{ route('admin.converted-leads.id-card-generate', $convertedLead->id) }}" method="post" style="display:inline-block" class="id-card-generate-form">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-warning" title="Generate ID Card" data-loading-text="Generating...">
                                                        <i class="ti ti-id"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="21" class="text-center">No Vibe Coding converted leads found</td>
                            </tr>
                            @endforelse
                        </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="d-lg-none">
                    @forelse($convertedLeads as $index => $convertedLead)
                    <div class="card mb-3">
                        <div class="card-body">
                            @php
                                $canToggleAcademic = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor();
                                $canToggleSupport = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_support_team();
                            @endphp
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-0">{{ $convertedLead->name }}</h6>
                                <span class="badge bg-primary">#{{ $index + 1 }}</span>
                            </div>
                            
                            <!-- Lead Details -->
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <small class="text-muted d-block">Phone</small>
                                    <span class="fw-medium">{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Email</small>
                                    <span class="fw-medium">{{ $convertedLead->email ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Batch</small>
                                    <span class="fw-medium">{{ $convertedLead->batch ? $convertedLead->batch->title : 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Admission Batch</small>
                                    <span class="fw-medium">{{ $convertedLead->admissionBatch ? $convertedLead->admissionBatch->title : 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Call Status</small>
                                    <span class="fw-medium">{{ $convertedLead->studentDetails?->call_status ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Class Status</small>
                                    <span class="fw-medium">{{ $convertedLead->studentDetails?->class_status ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Academic</small>
                                    @include('admin.converted-leads.partials.status-badge', [
                                        'convertedLead' => $convertedLead,
                                        'type' => 'academic',
                                        'showToggle' => $canToggleAcademic,
                                        'toggleUrl' => $canToggleAcademic ? route('admin.converted-leads.toggle-academic-verify', $convertedLead->id) : null
                                    ])
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Support</small>
                                    @include('admin.converted-leads.partials.status-badge', [
                                        'convertedLead' => $convertedLead,
                                        'type' => 'support',
                                        'showToggle' => $canToggleSupport,
                                        'toggleUrl' => $canToggleSupport ? route('admin.support-converted-leads.toggle-support-verify', $convertedLead->id) : null
                                    ])
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.converted-leads.show', $convertedLead->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="ti ti-eye"></i> View
                                </a>
                                <a href="{{ route('admin.invoices.index', $convertedLead->id) }}" class="btn btn-sm btn-success">
                                    <i class="ti ti-receipt"></i> Invoice
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="ti ti-inbox fs-1 text-muted"></i>
                        <p class="text-muted mt-2">No Vibe Coding converted leads found</p>
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
<script>
    $(document).ready(function() {
        // DataTable is automatically initialized by layout for tables with 'data_table_basic' class

        // Batch filter enhancement
        $('#batch_id').on('change', function() {
            // Auto-submit form when batch is changed for better UX
            $('#filterForm').submit();
        });

        // Dependent filters: load admission batches by batch
        function loadAdmissionBatchesByBatch(batchId, selectedId) {
            const $admission = $('#admission_batch_id');
            $admission.html('<option value="">Loading...</option>');
            if (!batchId) {
                $admission.html('<option value="">All Admission Batches</option>');
                return;
            }
            $.get(`/api/admission-batches/by-batch/${batchId}`).done(function(list) {
                let opts = '<option value="">All Admission Batches</option>';
                list.forEach(function(i) {
                    const sel = String(selectedId) === String(i.id) ? 'selected' : '';
                    opts += `<option value="${i.id}" ${sel}>${i.title}</option>`;
                });
                $admission.html(opts);
            }).fail(function() {
                $admission.html('<option value="">All Admission Batches</option>');
            });
        }

        // Initialize dependent dropdowns on load
        loadAdmissionBatchesByBatch($('#batch_id').val(), $('#admission_batch_id').data('selected'));

        // On batch change → reload admission batches
        $('#batch_id').on('change', function() {
            const bid = $(this).val();
            loadAdmissionBatchesByBatch(bid, '');
        });

        // Inline editing functionality
        $(document).on('click', '.edit-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const container = $(this).closest('.inline-edit');
            const field = container.data('field');
            const currentValue = container.data('current');
            
            // Remove any existing edit forms
            container.find('.edit-form').remove();
            
            // Remove editing class from other containers
            $('.inline-edit').removeClass('editing');
            $('.edit-form').remove();

            let editForm = '';
            
            if (field === 'phone') {
                const currentCode = container.siblings('.inline-code-value').data('current') || '';
                editForm = createPhoneField(currentCode, currentValue);
            } else if (['call_status', 'class_information', 'orientation_class_status', 'whatsapp_group_status', 'class_status'].includes(field)) {
                editForm = createSelectField(field, currentValue);
            } else if (['class_starting_date', 'class_ending_date', 'complete_cancel_date'].includes(field)) {
                editForm = createDateField(field, currentValue);
            } else if (field === 'class_time') {
                editForm = createTimeField(field, currentValue);
            } else if (field === 'admission_batch_id') {
                const batchId = container.data('batch-id');
                editForm = createAdmissionBatchField(batchId, currentValue);
            } else {
                editForm = createInputField(field, currentValue);
            }
            
            container.addClass('editing');
            container.append(editForm);
            
            // Load admission batches if it's an admission batch field
            if (field === 'admission_batch_id') {
                const batchId = container.data('batch-id');
                const $select = container.find('select');
                loadAdmissionBatchesForEdit($select, batchId, currentValue);
            }
            
            container.find('input, select').first().focus();
        });
        
        // Save inline edit
        $(document).off('click.saveInline').on('click.saveInline', '.save-edit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const container = $(this).closest('.inline-edit');
            const field = container.data('field');
            const id = container.data('id');
            let value;
            if (field === 'phone') {
                value = container.find('input[type="text"]').val();
            } else {
                value = container.find('input, select').val();
            }
            let extra = {};
            if (field === 'phone') {
                const codeVal = container.find('select[name="code"]').val();
                extra = { code: codeVal };
            }
            
            const btn = $(this);
            if (btn.data('busy')) return;
            btn.data('busy', true);
            btn.prop('disabled', true).html('<i class="ti ti-loader-2 spin"></i>');
            
            $.ajax({
                url: '{{ route("admin.converted-leads.inline-update", ":id") }}'.replace(':id', id),
                method: 'POST',
                data: {
                    field: field,
                    value: value,
                    _token: '{{ csrf_token() }}',
                    ...extra
                },
                success: function(response) {
                    if (response.success) {
                        container.find('.display-value').text(response.value || value);
                        // Update the data-current attribute with the new value
                        container.data('current', response.value || value);
                        if (field === 'phone') {
                            const codeVal = extra.code || '';
                            container.siblings('.inline-code-value').data('current', codeVal);
                        }
                        toast_success(response.message);
                    } else {
                        toast_error(response.error || 'Update failed');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Update failed';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    toast_error(errorMessage);
                },
                complete: function() {
                    btn.data('busy', false);
                    btn.prop('disabled', false).html('Save');
                    container.removeClass('editing');
                    container.find('.edit-form').remove();
                }
            });
        });
        
        // Cancel inline edit
        $(document).off('click.cancelInline').on('click.cancelInline', '.cancel-edit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const container = $(this).closest('.inline-edit');
            container.removeClass('editing');
            container.find('.edit-form').remove();
        });
        
        // Click outside to cancel edit
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.inline-edit').length) {
                $('.inline-edit').removeClass('editing');
                $('.edit-form').remove();
            }
        });

        // Handle update register number button clicks
        $('.update-register-btn').on('click', function(e) {
            e.preventDefault();
            const url = $(this).data('url');
            const title = $(this).data('title');
            show_small_modal(url, title);
        });

        // Handle ID card generation form submission
        $(document).off('submit', '.id-card-generate-form').on('submit', '.id-card-generate-form', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            
            const form = $(this);
            const button = form.find('button[type="submit"]');
            
            if (button.prop('disabled')) {
                return false;
            }
            
            const originalText = button.html();
            const loadingText = button.data('loading-text');
            
            button.prop('disabled', true).html('<i class="ti ti-loader-2 spin"></i> ' + loadingText);
            
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        toast_success(response.message);
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    }
                },
                error: function(xhr) {
                    console.error('Error generating ID card:', xhr);
                    toast_error('Error generating ID card. Please try again.');
                    button.prop('disabled', false).html(originalText);
                }
            });
            
            return false;
        });
    });

    function createInputField(field, currentValue) {
        const value = (currentValue && currentValue !== '-') ? currentValue : '';
        return `
            <div class="edit-form">
                <input type="text" value="${value}" class="form-control form-control-sm">
                <div class="btn-group mt-1">
                    <button class="btn btn-success btn-sm save-edit">Save</button>
                    <button class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                </div>
            </div>
        `;
    }

    function createDateField(field, currentValue) {
        let value = '';
        if (currentValue && currentValue !== '-') {
            // Convert d-m-Y to Y-m-d for input
            const parts = currentValue.split('-');
            if (parts.length === 3) {
                value = `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
            }
        }
        return `
            <div class="edit-form">
                <input type="date" value="${value}" class="form-control form-control-sm">
                <div class="btn-group mt-1">
                    <button class="btn btn-success btn-sm save-edit">Save</button>
                    <button class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                </div>
            </div>
        `;
    }

    function createTimeField(field, currentValue) {
        const value = (currentValue && currentValue !== '-') ? currentValue : '';
        return `
            <div class="edit-form">
                <input type="time" value="${value}" class="form-control form-control-sm">
                <div class="btn-group mt-1">
                    <button class="btn btn-success btn-sm save-edit">Save</button>
                    <button class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                </div>
            </div>
        `;
    }

    function createAdmissionBatchField(batchId, currentValue) {
        return `
            <div class="edit-form">
                <select class="form-select form-select-sm" data-batch-id="${batchId}">
                    <option value="">Select Admission Batch</option>
                </select>
                <div class="btn-group mt-1">
                    <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                    <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                </div>
            </div>
        `;
    }

    function loadAdmissionBatchesForEdit($select, batchId, currentValue) {
        if (!batchId) {
            $select.html('<option value="">No batch selected</option>');
            return;
        }
        
        $.get(`/api/admission-batches/by-batch/${batchId}`).done(function(list) {
            let options = '<option value="">Select Admission Batch</option>';
            list.forEach(function(item) {
                const selected = String(currentValue) === String(item.id) ? 'selected' : '';
                options += `<option value="${item.id}" ${selected}>${item.title}</option>`;
            });
            $select.html(options);
        }).fail(function() {
            $select.html('<option value="">Error loading admission batches</option>');
        });
    }

    function createPhoneField(currentCode, currentPhone) {
        const codeOptionsEl = document.getElementById('country-codes-json');
        let codeOptions = {};
        try {
            codeOptions = codeOptionsEl ? JSON.parse(codeOptionsEl.textContent || '{}') : {};
        } catch (e) {
            codeOptions = {};
        }
        const buildOptions = (selected) => {
            let opts = '<option value="">Select Country</option>';
            for (const c in codeOptions) {
                const isSel = String(selected) === String(c) ? 'selected' : '';
                opts += `<option value="${c}" ${isSel}>${c} - ${codeOptions[c]}</option>`;
            }
            return opts;
        };
        const safePhone = (currentPhone && currentPhone !== 'N/A') ? currentPhone : '';
        return `
            <div class="edit-form">
                <div class="row g-1">
                    <div class="col-5">
                        <select class="form-select form-select-sm" name="code">
                            ${buildOptions(currentCode)}
                        </select>
                    </div>
                    <div class="col-7">
                        <input type="text" value="${safePhone}" class="form-control form-control-sm" placeholder="Phone number">
                    </div>
                </div>
                <div class="btn-group mt-1">
                    <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                    <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                </div>
            </div>
        `;
    }

    function createSelectField(field, currentValue) {
        let options = '';
        
        if (field === 'call_status') {
            options = `
                <option value="">Select Call Status</option>
                <option value="Call Not Answered" ${currentValue === 'Call Not Answered' ? 'selected' : ''}>Call Not Answered</option>
                <option value="Switched Off" ${currentValue === 'Switched Off' ? 'selected' : ''}>Switched Off</option>
                <option value="Line Busy" ${currentValue === 'Line Busy' ? 'selected' : ''}>Line Busy</option>
                <option value="Student Asks to Call Later" ${currentValue === 'Student Asks to Call Later' ? 'selected' : ''}>Student Asks to Call Later</option>
                <option value="Lack of Interest in Conversation" ${currentValue === 'Lack of Interest in Conversation' ? 'selected' : ''}>Lack of Interest in Conversation</option>
                <option value="Wrong Contact" ${currentValue === 'Wrong Contact' ? 'selected' : ''}>Wrong Contact</option>
                <option value="Inconsistent Responses" ${currentValue === 'Inconsistent Responses' ? 'selected' : ''}>Inconsistent Responses</option>
                <option value="Task Complete" ${currentValue === 'Task Complete' ? 'selected' : ''}>Task Complete</option>
                <option value="Admission cancel" ${currentValue === 'Admission cancel' ? 'selected' : ''}>Admission cancel</option>
            `;
        } else if (field === 'class_information') {
            options = `
                <option value="">Select Class Information</option>
                <option value="phone call" ${currentValue === 'phone call' ? 'selected' : ''}>Phone Call</option>
                <option value="whatsapp" ${currentValue === 'whatsapp' ? 'selected' : ''}>WhatsApp</option>
            `;
        } else if (field === 'orientation_class_status') {
            options = `
                <option value="">Select Orientation Class Status</option>
                <option value="Participated" ${currentValue === 'Participated' ? 'selected' : ''}>Participated</option>
                <option value="Did not participated" ${currentValue === 'Did not participated' ? 'selected' : ''}>Did not participated</option>
            `;
        } else if (field === 'whatsapp_group_status') {
            options = `
                <option value="">Select WhatsApp Group Status</option>
                <option value="sent link" ${currentValue === 'sent link' ? 'selected' : ''}>Sent Link</option>
                <option value="task complete" ${currentValue === 'task complete' ? 'selected' : ''}>Task Complete</option>
            `;
        } else if (field === 'class_status') {
            options = `
                <option value="">Select Class Status</option>
                <option value="Running" ${currentValue === 'Running' ? 'selected' : ''}>Running</option>
                <option value="Cancel" ${currentValue === 'Cancel' ? 'selected' : ''}>Cancel</option>
                <option value="complete" ${currentValue === 'complete' ? 'selected' : ''}>Complete</option>
            `;
        }
        
        return `
            <div class="edit-form">
                <select class="form-select form-select-sm">
                    ${options}
                </select>
                <div class="btn-group mt-1">
                    <button class="btn btn-success btn-sm save-edit">Save</button>
                    <button class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                </div>
            </div>
        `;
    }
</script>
@endpush

<!-- Support Verify Modal -->
<div class="modal fade" id="supportVerifyModal" tabindex="-1" aria-labelledby="supportVerifyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supportVerifyModalLabel">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="supportVerifyModalText" class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmSupportVerifyBtn">
                    <span class="confirm-text">Confirm</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Academic Verify Modal -->
<div class="modal fade" id="academicVerifyModal" tabindex="-1" aria-labelledby="academicVerifyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="academicVerifyModalLabel">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="academicVerifyModalText" class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmAcademicVerifyBtn">
                    <span class="confirm-text">Confirm</span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Toggle Academic Verification with confirmation modal
        let academicVerifyUrl = null;
        $(document).off('click', '.toggle-academic-verify-btn').on('click', '.toggle-academic-verify-btn', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const url = $btn.data('url');
            const name = $btn.data('name') || 'this student';
            const isVerified = String($btn.data('verified')) === '1';

            academicVerifyUrl = url;

            const actionText = isVerified ? 'unverify' : 'verify';
            const modalText = `Are you sure you want to ${actionText} academic status for <strong>${name}</strong>?`;
            $('#academicVerifyModalText').html(modalText);
            const $confirmBtn = $('#confirmAcademicVerifyBtn');
            $confirmBtn.removeClass('btn-danger btn-success').addClass(isVerified ? 'btn-danger' : 'btn-success');
            $('#academicVerifyModal').modal('show');
        });

        $('#confirmAcademicVerifyBtn').on('click', function() {
            if (!academicVerifyUrl) return;
            const $confirmBtn = $(this);
            const originalHtml = $confirmBtn.html();
            $confirmBtn.prop('disabled', true).addClass('disabled');
            $.post(academicVerifyUrl, {_token: '{{ csrf_token() }}'})
                .done(function(res) {
                    if (res && res.success) {
                        show_alert('success', res.message || 'Updated');
                        $('#academicVerifyModal').modal('hide');
                        setTimeout(() => { location.reload(); }, 600);
                    } else {
                        show_alert('error', (res && res.message) ? res.message : 'Failed to update');
                    }
                })
                .fail(function(xhr){
                    let msg = 'Failed to update';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    show_alert('error', msg);
                })
                .always(function(){
                    $confirmBtn.prop('disabled', false).removeClass('disabled').html(originalHtml);
                    academicVerifyUrl = null;
                });
        });

        // Toggle Support Verification with confirmation modal
        let supportVerifyUrl = null;
        $(document).off('click', '.toggle-support-verify-btn').on('click', '.toggle-support-verify-btn', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const url = $btn.data('url');
            const name = $btn.data('name') || 'this student';
            const isVerified = String($btn.data('verified')) === '1';

            supportVerifyUrl = url;

            const actionText = isVerified ? 'unverify' : 'verify';
            const modalText = `Are you sure you want to ${actionText} support status for <strong>${name}</strong>?`;
            $('#supportVerifyModalText').html(modalText);
            const $confirmBtn = $('#confirmSupportVerifyBtn');
            $confirmBtn.removeClass('btn-danger btn-success').addClass(isVerified ? 'btn-danger' : 'btn-success');
            $('#supportVerifyModal').modal('show');
        });

        $('#confirmSupportVerifyBtn').on('click', function() {
            if (!supportVerifyUrl) return;
            const $confirmBtn = $(this);
            const originalHtml = $confirmBtn.html();
            $confirmBtn.prop('disabled', true).addClass('disabled');
            $.post(supportVerifyUrl, {_token: '{{ csrf_token() }}'})
                .done(function(res) {
                    if (res && res.success) {
                        show_alert('success', res.message || 'Updated');
                        $('#supportVerifyModal').modal('hide');
                        setTimeout(() => { location.reload(); }, 600);
                    } else {
                        show_alert('error', (res && res.message) ? res.message : 'Failed to update');
                    }
                })
                .fail(function(xhr){
                    let msg = 'Failed to update';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    show_alert('error', msg);
                })
                .always(function(){
                    $confirmBtn.prop('disabled', false).removeClass('disabled').html(originalHtml);
                    supportVerifyUrl = null;
                });
        });
    });
</script>
@endpush
