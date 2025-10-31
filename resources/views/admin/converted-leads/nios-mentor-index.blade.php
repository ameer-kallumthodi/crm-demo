@extends('layouts.mantis')

@section('title', 'NIOS Converted Mentor List')

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
                    <h5 class="m-b-10">NIOS Converted Mentor List</h5>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end align-items-center gap-3">
                    <ul class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.converted-leads.index') }}">Converted Leads</a></li>
                        <li class="breadcrumb-item">NIOS Converted Mentor List</li>
                    </ul>
                    <a href="{{ route('admin.converted-leads.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left"></i> Back to Converted Leads
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Course Filter Buttons ] start -->
@if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
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
                    <a href="{{ route('admin.vibe-coding-converted-leads.index') }}" class="btn btn-outline-primary">
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
                    @if(\App\Helpers\RoleHelper::is_mentor())
                    <a href="{{ route('admin.converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-list"></i> All Converted Leads
                    </a>
                    @endif
                    <a href="{{ route('admin.mentor-bosse-converted-leads.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-user-star"></i> Bosse Converted Mentor List
                    </a>
                    <a href="{{ route('admin.mentor-nios-converted-leads.index') }}" class="btn btn-outline-primary active">
                        <i class="ti ti-user-star"></i> NIOS Converted Mentor List
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
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.mentor-nios-converted-leads.index') }}" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search"
                                value="{{ request('search') }}" placeholder="Name, Phone, Email, Enroll Number">
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
                            <select class="form-select" id="admission_batch_id" name="admission_batch_id">
                                <option value="">All Admission Batches</option>
                                @if(request('batch_id'))
                                    @php
                                        $admissionBatches = \App\Models\AdmissionBatch::where('batch_id', request('batch_id'))->get();
                                    @endphp
                                    @foreach($admissionBatches as $admissionBatch)
                                    <option value="{{ $admissionBatch->id }}" {{ request('admission_batch_id') == $admissionBatch->id ? 'selected' : '' }}>
                                        {{ $admissionBatch->title }}
                                    </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="subject_id" class="form-label">Subject</label>
                            <select class="form-select" id="subject_id" name="subject_id">
                                <option value="">All Subjects</option>
                                @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="registration_status" class="form-label">Registration Status</label>
                            <select class="form-select" id="registration_status" name="registration_status">
                                <option value="">All</option>
                                <option value="Paid" {{ request('registration_status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                                <option value="Not Paid" {{ request('registration_status') == 'Not Paid' ? 'selected' : '' }}>Not Paid</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="student_status" class="form-label">Student Status</label>
                            <select class="form-select" id="student_status" name="student_status">
                                <option value="">All</option>
                                <option value="Low Level" {{ request('student_status') == 'Low Level' ? 'selected' : '' }}>Low Level</option>
                                <option value="Below Medium" {{ request('student_status') == 'Below Medium' ? 'selected' : '' }}>Below Medium</option>
                                <option value="Medium Level" {{ request('student_status') == 'Medium Level' ? 'selected' : '' }}>Medium Level</option>
                                <option value="Advanced Level" {{ request('student_status') == 'Advanced Level' ? 'selected' : '' }}>Advanced Level</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="date_from" class="form-label">Date From</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="date_to" class="form-label">Date To</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-search"></i> <span class="d-none d-sm-inline">Filter</span>
                                </button>
                                <a href="{{ route('admin.mentor-nios-converted-leads.index') }}" class="btn btn-secondary">
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
                <h5 class="mb-0">NIOS Converted Mentor List</h5>
            </div>
            <div class="card-body">
                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover data_table_basic" id="niosMentorTable">
                            <thead>
                                <tr>
                                    <th>SL No</th>
                                    <th>Converted Date</th>
                                    <th>Registration Number</th>
                                    <th>Name</th>
                                    <th>DOB</th>
                                    <th>Enrolment Number</th>
                                    <th>Phone</th>
                                    <th>Subject</th>
                                    <th>Batch</th>
                                    <th>Admission Batch</th>
                                    <th>Technology Side</th>
                                    <th>Student Status</th>
                                    <th>CALL - 1</th>
                                    <th>APP</th>
                                    <th>WhatsApp Group</th>
                                    <th>Telegram Group</th>
                                    <th>Problems</th>
                                    <th>Call - 2</th>
                                    <th>Mentor Live 1</th>
                                    <th>FIRST LIVE</th>
                                    <th>FIRST EXAM</th>
                                    <th>CALL - 3</th>
                                    <th>Mentor Live 2</th>
                                    <th>CALL - 4</th>
                                    <th>SECOND LIVE</th>
                                    <th>Second Exam</th>
                                    <th>Call - 5</th>
                                    <th>Mentor Live 3</th>
                                    <th>Assignment</th>
                                    <th>Status</th>
                                    <th>EXAM FEES</th>
                                    <th>CALL - 6</th>
                                    <th>PCP CLASS</th>
                                    <th>CALL - 7</th>
                                    <th>Practical Record</th>
                                    <th>CALL - 8</th>
                                    <th>Mentor Live 4</th>
                                    <th>Model Exam Live</th>
                                    <th>Model Exam</th>
                                    <th>I D CARD</th>
                                    <th>Practical Hall Ticket</th>
                                    <th>CALL - 9</th>
                                    <th>Particle Exam</th>
                                    <th>Theory Hall Ticket</th>
                                    <th>Call - 10</th>
                                    <th>Subject -1</th>
                                    <th>Subject -2</th>
                                    <th>Subject -3</th>
                                    <th>Subject -4</th>
                                    <th>Subject -5</th>
                                    <th>Subject -6</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($convertedLeads as $index => $convertedLead)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $convertedLead->created_at->format('d-m-Y') }}</td>
                                    <td>{{ $convertedLead->register_number ?? '-' }}</td>
                                    <td>{{ $convertedLead->name }}</td>
                                    <td>{{ $convertedLead->dob ? \Carbon\Carbon::parse($convertedLead->dob)->format('d-m-Y') : '-' }}</td>
                                    <td>{{ $convertedLead->studentDetails?->enroll_no ?? '-' }}</td>
                                    <td>{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</td>
                                    <td>
                                        <div class="inline-edit" data-field="subject_id" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->subject_id }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->subject?->title ?? $convertedLead->subject?->title ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $convertedLead->batch ? $convertedLead->batch->title : 'N/A' }}</td>
                                    <td>{{ $convertedLead->admissionBatch ? $convertedLead->admissionBatch->title : 'N/A' }}</td>
                                    <td>
                                        <div class="inline-edit" data-field="technology_side" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->technology_side }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->technology_side ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="student_status" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->student_status }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->student_status ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <!-- Continue with all the other fields... -->
                                    <td>
                                        <div class="inline-edit" data-field="call_1" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->call_1 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->call_1 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="app" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->app }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->app ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="whatsapp_group" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->whatsapp_group }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->whatsapp_group ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="telegram_group" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->telegram_group }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->telegram_group ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="problems" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->problems }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->problems ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <!-- Continue with remaining fields... -->
                                    <td>
                                        <div class="inline-edit" data-field="call_2" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->call_2 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->call_2 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="mentor_live_1" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->mentor_live_1 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->mentor_live_1 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="first_live" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->first_live }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->first_live ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="first_exam" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->first_exam }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->first_exam ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="call_3" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->call_3 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->call_3 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="mentor_live_2" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->mentor_live_2 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->mentor_live_2 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="call_4" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->call_4 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->call_4 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="second_live" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->second_live }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->second_live ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="second_exam" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->second_exam }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->second_exam ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="call_5" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->call_5 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->call_5 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="mentor_live_3" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->mentor_live_3 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->mentor_live_3 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="assignment" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->assignment }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->assignment ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="status" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->status }}">
                                            <span class="display-value">{{ $convertedLead->status ?? 'N/A' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="exam_fees" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->exam_fees }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->exam_fees ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="call_6" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->call_6 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->call_6 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="pcp_class" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->pcp_class }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->pcp_class ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="call_7" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->call_7 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->call_7 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="practical_record" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->practical_record }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->practical_record ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="call_8" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->call_8 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->call_8 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="mentor_live_4" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->mentor_live_4 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->mentor_live_4 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="model_exam_live" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->model_exam_live }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->model_exam_live ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="model_exam" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->model_exam }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->model_exam ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="id_card" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->id_card }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->id_card ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="practical_hall_ticket" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->practical_hall_ticket }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->practical_hall_ticket ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="call_9" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->call_9 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->call_9 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="particle_exam" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->particle_exam }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->particle_exam ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="theory_hall_ticket" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->theory_hall_ticket }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->theory_hall_ticket ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="call_10" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->call_10 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->call_10 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="exam_subject_1" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->exam_subject_1 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->exam_subject_1 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="exam_subject_2" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->exam_subject_2 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->exam_subject_2 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="exam_subject_3" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->exam_subject_3 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->exam_subject_3 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="exam_subject_4" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->exam_subject_4 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->exam_subject_4 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="exam_subject_5" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->exam_subject_5 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->exam_subject_5 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="exam_subject_6" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->exam_subject_6 }}">
                                            <span class="display-value">{{ $convertedLead->mentorDetails?->exam_subject_6 ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_mentor())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="50" class="text-center">No converted leads found for NIOS mentoring</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="d-lg-none">
                    <div class="row" id="mobileCards">
                        @forelse($convertedLeads as $index => $convertedLead)
                        <div class="col-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">{{ $convertedLead->name }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <small class="text-muted d-block">Phone</small>
                                            <span class="fw-medium">{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Enrolment Number</small>
                                            <span class="fw-medium">{{ $convertedLead->studentDetails?->enroll_no ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Technology Side</small>
                                            <span class="fw-medium">{{ $convertedLead->mentorDetails?->technology_side ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Student Status</small>
                                            <span class="fw-medium">{{ $convertedLead->mentorDetails?->student_status ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Subject</small>
                                            <span class="fw-medium">{{ $convertedLead->mentorDetails?->subject?->title ?? $convertedLead->subject?->title ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Batch</small>
                                            <span class="fw-medium">{{ $convertedLead->batch ? $convertedLead->batch->title : 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="text-center py-4">
                                <p class="text-muted">No converted leads found for NIOS mentoring</p>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

@endsection

@push('styles')
<style>
.spin {
    animation: spin 1s linear infinite;
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.inline-edit {
    position: relative;
    overflow: visible;
}

.inline-edit .edit-form {
    display: none;
    position: absolute;
    top: 0;
    left: -8px;
    z-index: 10;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
    min-width: 320px;
    max-width: 440px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
}

.inline-edit.editing .edit-form {
    display: block;
}

.inline-edit.editing .display-value {
    display: none;
}

.inline-edit .edit-form input,
.inline-edit .edit-form select {
    width: 100%;
    margin-bottom: 8px;
}

.inline-edit .edit-form .btn-group {
    width: 100%;
}

.inline-edit .edit-form .btn {
    flex: 1;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Handle batch change to load admission batches
    $('#batch_id').change(function() {
        const batchId = $(this).val();
        const $admissionBatchSelect = $('#admission_batch_id');
        
        if (batchId) {
            $.get(`/api/admission-batches/by-batch/${batchId}`, function(data) {
                $admissionBatchSelect.html('<option value="">All Admission Batches</option>');
                data.forEach(function(batch) {
                    $admissionBatchSelect.append(`<option value="${batch.id}">${batch.title}</option>`);
                });
            }).fail(function() {
                $admissionBatchSelect.html('<option value="">Error loading admission batches</option>');
            });
        } else {
            $admissionBatchSelect.html('<option value="">All Admission Batches</option>');
        }
    });

    // Inline editing functionality
    $('.edit-btn').click(function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const $inlineEdit = $(this).closest('.inline-edit');
        const field = $inlineEdit.data('field');
        const id = $inlineEdit.data('id');
        const currentValue = $inlineEdit.data('current') || '';
        
        if ($inlineEdit.hasClass('editing')) {
            return;
        }
        
        $inlineEdit.addClass('editing');
        
        let inputHtml = '';
        
        if (field === 'subject_id') {
            inputHtml = createSubjectField(field, currentValue, id);
        } else if (field === 'problems') {
            inputHtml = createTextareaField(field, currentValue);
        } else if (['call_1', 'call_2', 'call_3', 'call_4', 'call_5', 'call_6', 'call_7', 'call_8', 'call_9', 'call_10'].includes(field)) {
            inputHtml = createSelectField(field, currentValue, [
                'Call Not Answered', 'Switched Off', 'Line Busy', 'Student Asks to Call Later', 
                'Lack of Interest in Conversation', 'Wrong Contact', 'Inconsistent Responses', 'Task Complete'
            ]);
        } else if (['mentor_live_1', 'mentor_live_2', 'mentor_live_3', 'mentor_live_4', 'mentor_live_5'].includes(field)) {
            inputHtml = createSelectField(field, currentValue, ['Not Respond', 'Task Complete']);
        } else if (['app', 'whatsapp_group', 'telegram_group', 'first_live', 'second_live', 'model_exam_live', 'assignment', 'exam_fees', 'pcp_class'].includes(field)) {
            inputHtml = createSelectField(field, currentValue, ['Not Respond', 'Task Complete']);
        } else if (['first_exam', 'second_exam', 'model_exam', 'particle_exam'].includes(field)) {
            inputHtml = createSelectField(field, currentValue, ['Did not log in on time', 'missed the exam', 'technical issue', 'task complete']);
        } else if (['practical_record'].includes(field)) {
            inputHtml = createSelectField(field, currentValue, ['Not Respond', '1 Subject Attend', '2 Subject Attend', '3 Subject Attend', '4 Subject Attend', '5 Subject Attend', '6 Subject Attend', 'Task Complete']);
        } else if (['id_card', 'practical_hall_ticket', 'theory_hall_ticket'].includes(field)) {
            inputHtml = createSelectField(field, currentValue, ['Did Not', 'Task Complete']);
        } else if (['exam_subject_1', 'exam_subject_2', 'exam_subject_3', 'exam_subject_4', 'exam_subject_5', 'exam_subject_6'].includes(field)) {
            inputHtml = createSelectField(field, currentValue, ['Did not log in on time', 'missed the exam', 'technical issue', 'task complete']);
        } else if (['technology_side'].includes(field)) {
            inputHtml = createSelectField(field, currentValue, ['No Knowledge', 'Limited Knowledge', 'Moderate Knowledge', 'High Knowledge']);
        } else if (['student_status'].includes(field)) {
            inputHtml = createSelectField(field, currentValue, ['Low Level', 'Below Medium', 'Medium Level', 'Advanced Level']);
        } else if (['status'].includes(field)) {
            inputHtml = createSelectField(field, currentValue, ['Paid', 'Admission cancel', 'Active', 'Inactive']);
        } else {
            inputHtml = createInputField(field, currentValue);
        }
        
        $inlineEdit.append(inputHtml);
    });
    
    function createInputField(field, value) {
        return `
            <div class="edit-form">
                <input type="text" class="form-control" value="${value}" data-field="${field}">
                <div class="btn-group w-100">
                    <button type="button" class="btn btn-success btn-sm save-btn">
                        <i class="ti ti-check"></i> Save
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm cancel-btn">
                        <i class="ti ti-x"></i> Cancel
                    </button>
                </div>
            </div>
        `;
    }
    
    function createTextareaField(field, value) {
        return `
            <div class="edit-form">
                <textarea class="form-control" rows="3" data-field="${field}">${value}</textarea>
                <div class="btn-group w-100">
                    <button type="button" class="btn btn-success btn-sm save-btn">
                        <i class="ti ti-check"></i> Save
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm cancel-btn">
                        <i class="ti ti-x"></i> Cancel
                    </button>
                </div>
            </div>
        `;
    }
    
    function createSelectField(field, value, options) {
        let optionsHtml = '<option value="">Select ' + field.replace('_', ' ') + '</option>';
        options.forEach(option => {
            const selected = option === value ? 'selected' : '';
            optionsHtml += `<option value="${option}" ${selected}>${option}</option>`;
        });
        
        return `
            <div class="edit-form">
                <select class="form-select" data-field="${field}">
                    ${optionsHtml}
                </select>
                <div class="btn-group w-100">
                    <button type="button" class="btn btn-success btn-sm save-btn">
                        <i class="ti ti-check"></i> Save
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm cancel-btn">
                        <i class="ti ti-x"></i> Cancel
                    </button>
                </div>
            </div>
        `;
    }
    
    function createSubjectField(field, value, id) {
        return `
            <div class="edit-form">
                <select class="form-select" data-field="${field}">
                    <option value="">Select Subject</option>
                </select>
                <div class="btn-group w-100">
                    <button type="button" class="btn btn-success btn-sm save-btn">
                        <i class="ti ti-check"></i> Save
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm cancel-btn">
                        <i class="ti ti-x"></i> Cancel
                    </button>
                </div>
            </div>
        `;
    }
    
    function loadSubjectsForEdit($select, currentValue) {
        $.get('/api/subjects/by-course/1', function(data) {
            $select.html('<option value="">Select Subject</option>');
            data.forEach(function(item) {
                const selected = item.id == currentValue ? 'selected' : '';
                $select.append(`<option value="${item.id}" ${selected}>${item.title}</option>`);
            });
        }).fail(function() {
            $select.html('<option value="">Error loading subjects</option>');
        });
    }
    
    // Handle save
    $(document).on('click', '.save-btn', function() {
        const $inlineEdit = $(this).closest('.inline-edit');
        const field = $inlineEdit.data('field');
        const id = $inlineEdit.data('id');
        const $input = $inlineEdit.find('[data-field]');
        const value = $input.val();
        
        // Show loading
        $(this).html('<i class="ti ti-loader spin"></i> Saving...');
        $(this).prop('disabled', true);
        
        $.ajax({
            url: `/mentor-nios-converted-leads/${id}/update-mentor-details`,
            method: 'POST',
            data: {
                field: field,
                value: value,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $inlineEdit.find('.display-value').text(response.value || value || '-');
                    $inlineEdit.data('current', value);
                    $inlineEdit.removeClass('editing');
                    $inlineEdit.find('.edit-form').remove();
                } else {
                    alert('Error: ' + response.error);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                alert('Error: ' + (response?.error || 'Update failed'));
            },
            complete: function() {
                // Reset button
                $inlineEdit.find('.save-btn').html('<i class="ti ti-check"></i> Save');
                $inlineEdit.find('.save-btn').prop('disabled', false);
            }
        });
    });
    
    // Handle cancel
    $(document).on('click', '.cancel-btn', function() {
        const $inlineEdit = $(this).closest('.inline-edit');
        $inlineEdit.removeClass('editing');
        $inlineEdit.find('.edit-form').remove();
    });
    
    // Load subjects when editing subject field
    $(document).on('change', '.edit-form select[data-field="subject_id"]', function() {
        // This is handled by the createSubjectField function
    });
    
    // Initialize subject dropdown when editing
    $(document).on('click', '.edit-btn', function() {
        const $inlineEdit = $(this).closest('.inline-edit');
        const field = $inlineEdit.data('field');
        
        if (field === 'subject_id') {
            setTimeout(() => {
                const $select = $inlineEdit.find('select[data-field="subject_id"]');
                const currentValue = $inlineEdit.data('current');
                loadSubjectsForEdit($select, currentValue);
            }, 100);
        }
    });
});
</script>
@endpush
