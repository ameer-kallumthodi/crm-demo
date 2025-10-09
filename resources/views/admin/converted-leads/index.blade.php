@extends('layouts.mantis')

@section('title', 'Converted Leads')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Converted Leads Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Converted Leads</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Filter Section ] start -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.converted-leads.index') }}" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search"
                                value="{{ request('search') }}" placeholder="Name, Phone, Email">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="course_id" class="form-label">Course</label>
                            <select class="form-select" id="course_id" name="course_id">
                                <option value="">All Courses</option>
                                @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="batch_id" class="form-label">Batch</label>
                            <select class="form-select" id="batch_id" name="batch_id" data-selected="{{ request('batch_id') }}">
                                <option value="">All Batches</option>
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
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All</option>
                                <option value="Paid" {{ request('status')==='Paid' ? 'selected' : '' }}>Paid</option>
                                <option value="Admission cancel" {{ request('status')==='Admission cancel' ? 'selected' : '' }}>Admission cancel</option>
                                <option value="Active" {{ request('status')==='Active' ? 'selected' : '' }}>Active</option>
                                <option value="Inactive" {{ request('status')==='Inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="reg_fee" class="form-label">REG. FEE</label>
                            <select class="form-select" id="reg_fee" name="reg_fee">
                                <option value="">All</option>
                                <option value="Received" {{ request('reg_fee')==='Received' ? 'selected' : '' }}>Received</option>
                                <option value="Not Received" {{ request('reg_fee')==='Not Received' ? 'selected' : '' }}>Not Received</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="exam_fee" class="form-label">EXAM FEE</label>
                            <select class="form-select" id="exam_fee" name="exam_fee">
                                <option value="">All</option>
                                <option value="Pending" {{ request('exam_fee')==='Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Not Paid" {{ request('exam_fee')==='Not Paid' ? 'selected' : '' }}>Not Paid</option>
                                <option value="Paid" {{ request('exam_fee')==='Paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="id_card" class="form-label">ID CARD</label>
                            <select class="form-select" id="id_card" name="id_card">
                                <option value="">All</option>
                                <option value="processing" {{ request('id_card')==='processing' ? 'selected' : '' }}>processing</option>
                                <option value="download" {{ request('id_card')==='download' ? 'selected' : '' }}>download</option>
                                <option value="not downloaded" {{ request('id_card')==='not downloaded' ? 'selected' : '' }}>not downloaded</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="tma" class="form-label">TMA</label>
                            <select class="form-select" id="tma" name="tma">
                                <option value="">All</option>
                                <option value="Uploaded" {{ request('tma')==='Uploaded' ? 'selected' : '' }}>Uploaded</option>
                                <option value="Not Upload" {{ request('tma')==='Not Upload' ? 'selected' : '' }}>Not Upload</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-search"></i> <span class="d-none d-sm-inline">Filter</span>
                                </button>
                                <a href="{{ route('admin.converted-leads.index') }}" class="btn btn-secondary">
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
                <h5 class="mb-0">Converted Leads List</h5>
            </div>
            <div class="card-body">
                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover data_table_basic" id="convertedLeadsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Register Number</th>
                                    <th>Date</th>
                                    <th>DOB</th>
                                    <th>Name</th>
                                    <th>Subject</th>
                                    <th>Mobile</th>
                                    <th>Course</th>
                                    <th>Batch</th>
                                    <th>Admission Batch</th>
                                    <th>Registered Person</th>
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th>Status</th>
                                    <th>REG. FEE</th>
                                    <th>EXAM FEE</th>
                                    <th>Ref No</th>
                                    <th>Enroll No</th>
                                    <th>MAIL</th>
                                    <th>ID CARD</th>
                                    <th>TMA</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($convertedLeads as $index => $convertedLead)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="inline-edit" data-field="register_number" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->register_number }}">
                                            @if($convertedLead->register_number)
                                            <span class="badge bg-success"><span class="display-value">{{ $convertedLead->register_number }}</span></span>
                                            @else
                                            <span class="display-value text-muted">Not Set</span>
                                            @endif
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $convertedLead->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="inline-edit" data-field="dob" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->dob }}">
                                            @php
                                                $dobDisplay = $convertedLead->dob ? (strtotime($convertedLead->dob) ? date('d-m-Y', strtotime($convertedLead->dob)) : $convertedLead->dob) : 'N/A';
                                            @endphp
                                            <span class="display-value">{{ $dobDisplay }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avtar avtar-s rounded-circle bg-light-success me-2 d-flex align-items-center justify-content-center">
                                                <span class="f-16 fw-bold text-success">{{ strtoupper(substr($convertedLead->name, 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $convertedLead->name }}</h6>
                                                <small class="text-muted">ID: {{ $convertedLead->lead_id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="subject_id" data-id="{{ $convertedLead->id }}" data-course-id="{{ $convertedLead->course_id }}" data-current-id="{{ $convertedLead->subject_id }}">
                                            <span class="display-value">{{ $convertedLead->subject ? $convertedLead->subject->title : 'N/A' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
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
                                    <td>{{ $convertedLead->course ? $convertedLead->course->title : 'N/A' }}</td>
                                    <td>{{ $convertedLead->batch ? $convertedLead->batch->title : 'N/A' }}</td>
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
                                    <td>
                                        <div class="inline-edit" data-field="academic_assistant_id" data-id="{{ $convertedLead->id }}" data-current-id="{{ $convertedLead->academic_assistant_id }}">
                                            <span class="display-value">{{ $convertedLead->academicAssistant ? $convertedLead->academicAssistant->name : 'N/A' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="username" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->username }}">
                                            <span class="display-value">{{ $convertedLead->username ?? 'N/A' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="password" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->password }}">
                                            <span class="display-value">{{ $convertedLead->password ?? 'N/A' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="status" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->status }}">
                                            <span class="display-value">{{ $convertedLead->status ?? 'N/A' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="reg_fee" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->reg_fee }}">
                                            <span class="display-value">{{ $convertedLead->reg_fee ?? 'N/A' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="exam_fee" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->exam_fee }}">
                                            <span class="display-value">{{ $convertedLead->exam_fee ?? 'N/A' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="ref_no" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->ref_no }}">
                                            <span class="display-value">{{ $convertedLead->ref_no ?? 'N/A' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="enroll_no" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->enroll_no }}">
                                            <span class="display-value">{{ $convertedLead->enroll_no ?? 'N/A' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $convertedLead->email ?? 'N/A' }}</td>
                                    <td>
                                        <div class="inline-edit" data-field="id_card" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->id_card }}">
                                            <span class="display-value">{{ $convertedLead->id_card ?? 'N/A' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="tma" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->tma }}">
                                            <span class="display-value">{{ $convertedLead->tma ?? 'N/A' }}</span>
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
                                                    <a href="{{ route('admin.converted-leads.id-card-view', $convertedLead->id) }}" class="btn btn-sm btn-warning" title="View ID Card" target="_blank">
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
                                    <td colspan="22" class="text-center">No converted leads found</td>
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
                            <!-- Lead Header -->
                            <div class="d-flex align-items-center mb-3">
                                <div class="avtar avtar-s rounded-circle bg-light-success me-3 d-flex align-items-center justify-content-center">
                                    <span class="f-16 fw-bold text-success">{{ strtoupper(substr($convertedLead->name, 0, 1)) }}</span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">{{ $convertedLead->name }}</h6>
                                    <small class="text-muted">ID: {{ $convertedLead->lead_id }}</small>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.converted-leads.show', $convertedLead->id) }}">
                                                <i class="ti ti-eye me-2"></i>View Details
                                            </a>
                                        </li>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_finance() || \App\Helpers\RoleHelper::is_post_sales())
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.invoices.index', $convertedLead->id) }}">
                                                <i class="ti ti-receipt me-2"></i>View Invoice
                                            </a>
                                        </li>
                                        @endif
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                        <li>
                                            <a class="dropdown-item update-register-btn" href="#"
                                                data-url="{{ route('admin.converted-leads.update-register-number-modal', $convertedLead->id) }}"
                                                data-title="Update Register Number">
                                                <i class="ti ti-edit me-2"></i>Update Register Number
                                            </a>
                                        </li>
                                        @if($convertedLead->register_number)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.converted-leads.id-card-pdf', $convertedLead->id) }}" target="_blank">
                                                <i class="ti ti-id me-2"></i>Generate ID Card PDF
                                            </a>
                                        </li>
                                        @endif
                                        @endif
                                    </ul>
                                </div>
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
                                    <small class="text-muted d-block">Course</small>
                                    <span class="fw-medium">{{ $convertedLead->course ? $convertedLead->course->title : 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Academic Assistant</small>
                                    <span class="fw-medium">{{ $convertedLead->academicAssistant ? $convertedLead->academicAssistant->name : 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Register Number</small>
                                    @if($convertedLead->register_number)
                                    <span class="badge bg-success">{{ $convertedLead->register_number }}</span>
                                    @else
                                    <span class="text-muted">Not Set</span>
                                    @endif
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Converted Date</small>
                                    <span class="fw-medium">{{ $convertedLead->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('admin.converted-leads.show', $convertedLead->id) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="ti ti-eye me-1"></i>View Details
                                </a>
                                @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_finance() || \App\Helpers\RoleHelper::is_post_sales())
                                <a href="{{ route('admin.invoices.index', $convertedLead->id) }}"
                                    class="btn btn-sm btn-success">
                                    <i class="ti ti-receipt me-1"></i>View Invoice
                                </a>
                                @endif
                                @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                <button type="button" class="btn btn-sm btn-info update-register-btn"
                                    data-url="{{ route('admin.converted-leads.update-register-number-modal', $convertedLead->id) }}"
                                    data-title="Update Register Number">
                                    <i class="ti ti-edit me-1"></i>Update Register
                                </button>
                                @if($convertedLead->register_number)
                                <a href="{{ route('admin.converted-leads.id-card-pdf', $convertedLead->id) }}"
                                    class="btn btn-sm btn-warning" target="_blank">
                                    <i class="ti ti-id me-1"></i>ID Card PDF
                                </a>
                                @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <div class="text-muted">
                            <i class="ti ti-check-circle f-48 mb-3 d-block"></i>
                            <h5>No converted leads found</h5>
                            <p>Try adjusting your filters or check back later.</p>
                        </div>
                    </div>
                    @endforelse
                </div>

                <!-- Mobile Card View -->
                <div class="d-lg-none">
                    @forelse($convertedLeads as $index => $convertedLead)
                    <div class="card mb-3">
                        <div class="card-body">
                            <!-- Lead Header -->
                            <div class="d-flex align-items-center mb-3">
                                <div class="avtar avtar-s rounded-circle bg-light-success me-3 d-flex align-items-center justify-content-center">
                                    <span class="f-16 fw-bold text-success">{{ strtoupper(substr($convertedLead->name, 0, 1)) }}</span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">{{ $convertedLead->name }}</h6>
                                    <small class="text-muted">ID: {{ $convertedLead->lead_id }}</small>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.converted-leads.show', $convertedLead->id) }}">
                                                <i class="ti ti-eye me-2"></i>View Details
                                            </a>
                                        </li>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_finance() || \App\Helpers\RoleHelper::is_post_sales())
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.invoices.index', $convertedLead->id) }}">
                                                <i class="ti ti-receipt me-2"></i>View Invoice
                                            </a>
                                        </li>
                                        @endif
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                        <li>
                                            <a class="dropdown-item update-register-btn" href="#"
                                                data-url="{{ route('admin.converted-leads.update-register-number-modal', $convertedLead->id) }}"
                                                data-title="Update Register Number">
                                                <i class="ti ti-edit me-2"></i>Update Register Number
                                            </a>
                                        </li>
                                        @if($convertedLead->register_number)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.converted-leads.id-card-pdf', $convertedLead->id) }}" target="_blank">
                                                <i class="ti ti-id me-2"></i>Generate ID Card PDF
                                            </a>
                                        </li>
                                        @endif
                                        @endif
                                    </ul>
                                </div>
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
                                    <small class="text-muted d-block">Course</small>
                                    <span class="fw-medium">{{ $convertedLead->course ? $convertedLead->course->title : 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Academic Assistant</small>
                                    <span class="fw-medium">{{ $convertedLead->academicAssistant ? $convertedLead->academicAssistant->name : 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Register Number</small>
                                    @if($convertedLead->register_number)
                                    <span class="badge bg-success">{{ $convertedLead->register_number }}</span>
                                    @else
                                    <span class="text-muted">Not Set</span>
                                    @endif
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Converted Date</small>
                                    <span class="fw-medium">{{ $convertedLead->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('admin.converted-leads.show', $convertedLead->id) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="ti ti-eye me-1"></i>View Details
                                </a>
                                @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_finance() || \App\Helpers\RoleHelper::is_post_sales())
                                <a href="{{ route('admin.invoices.index', $convertedLead->id) }}"
                                    class="btn btn-sm btn-success">
                                    <i class="ti ti-receipt me-1"></i>View Invoice
                                </a>
                                @endif
                                @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                <button type="button" class="btn btn-sm btn-info update-register-btn"
                                    data-url="{{ route('admin.converted-leads.update-register-number-modal', $convertedLead->id) }}"
                                    data-title="Update Register Number">
                                    <i class="ti ti-edit me-1"></i>Update Register
                                </button>
                                @if($convertedLead->register_number)
                                <a href="{{ route('admin.converted-leads.id-card-pdf', $convertedLead->id) }}"
                                    class="btn btn-sm btn-warning" target="_blank">
                                    <i class="ti ti-id me-1"></i>ID Card PDF
                                </a>
                                @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <div class="text-muted">
                            <i class="ti ti-check-circle f-48 mb-3 d-block"></i>
                            <h5>No converted leads found</h5>
                            <p>Try adjusting your filters or check back later.</p>
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

<script id="country-codes-json" type="application/json">{!! json_encode($country_codes ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>

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
    left: -8px; /* allow a bit wider than the cell */
    z-index: 10;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
    min-width: 320px; /* wider edit area */
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
    padding: 4px 8px;
    border: 1px solid #ccc;
    border-radius: 3px;
    font-size: 12px;
}

.inline-edit .edit-form input:focus,
.inline-edit .edit-form select:focus {
    border-color: #7366ff;
    outline: none;
    box-shadow: 0 0 0 2px rgba(115,102,255,0.15);
}

.inline-edit .edit-form .btn-group {
    margin-top: 5px;
}

.inline-edit .edit-form .btn {
    padding: 2px 8px;
    font-size: 11px;
}

/* Increase table column widths for readability */
#convertedLeadsTable thead th,
#convertedLeadsTable tbody td {
    white-space: nowrap;
}

/* Sticky header */
#convertedLeadsTable thead th {
    position: sticky;
    top: 0;
    z-index: 5;
    background: #fff;
    box-shadow: inset 0 -1px 0 #e9ecef;
}

/* Hover state */
#convertedLeadsTable tbody tr:hover {
    background: #fafbff;
}

/* Truncate long text */
#convertedLeadsTable td .display-value {
    display: inline-block;
    max-width: 220px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    vertical-align: middle;
}

/* Action buttons spacing */
#convertedLeadsTable .btn-group .btn { margin-right: 4px; }
#convertedLeadsTable .btn-group .btn:last-child { margin-right: 0; }

/* Filter form separation */
.card .card-body #filterForm {
    border-bottom: 1px dashed #e9ecef;
    padding-bottom: 8px;
}

/* Column-specific min-widths by position */
#convertedLeadsTable thead th:nth-child(1),
#convertedLeadsTable tbody td:nth-child(1) { min-width: 60px; }
#convertedLeadsTable thead th:nth-child(2),
#convertedLeadsTable tbody td:nth-child(2) { min-width: 140px; }
#convertedLeadsTable thead th:nth-child(3),
#convertedLeadsTable tbody td:nth-child(3) { min-width: 120px; }
#convertedLeadsTable thead th:nth-child(4),
#convertedLeadsTable tbody td:nth-child(4) { min-width: 120px; }
#convertedLeadsTable thead th:nth-child(5),
#convertedLeadsTable tbody td:nth-child(5) { min-width: 220px; }
#convertedLeadsTable thead th:nth-child(6),
#convertedLeadsTable tbody td:nth-child(6) { min-width: 180px; }
#convertedLeadsTable thead th:nth-child(7),
#convertedLeadsTable tbody td:nth-child(7) { min-width: 140px; }
#convertedLeadsTable thead th:nth-child(8),
#convertedLeadsTable tbody td:nth-child(8) { min-width: 180px; }
#convertedLeadsTable thead th:nth-child(9),
#convertedLeadsTable tbody td:nth-child(9) { min-width: 180px; }
#convertedLeadsTable thead th:nth-child(10),
#convertedLeadsTable tbody td:nth-child(10) { min-width: 160px; }
#convertedLeadsTable thead th:nth-child(11),
#convertedLeadsTable tbody td:nth-child(11) { min-width: 140px; }
#convertedLeadsTable thead th:nth-child(12),
#convertedLeadsTable tbody td:nth-child(12) { min-width: 140px; }
#convertedLeadsTable thead th:nth-child(13),
#convertedLeadsTable tbody td:nth-child(13) { min-width: 140px; }
#convertedLeadsTable thead th:nth-child(14),
#convertedLeadsTable tbody td:nth-child(14) { min-width: 140px; }
#convertedLeadsTable thead th:nth-child(15),
#convertedLeadsTable tbody td:nth-child(15) { min-width: 140px; }
#convertedLeadsTable thead th:nth-child(16),
#convertedLeadsTable tbody td:nth-child(16) { min-width: 140px; }
#convertedLeadsTable thead th:nth-child(17),
#convertedLeadsTable tbody td:nth-child(17) { min-width: 200px; }
#convertedLeadsTable thead th:nth-child(18),
#convertedLeadsTable tbody td:nth-child(18) { min-width: 120px; }
#convertedLeadsTable thead th:nth-child(19),
#convertedLeadsTable tbody td:nth-child(19) { min-width: 120px; }
#convertedLeadsTable thead th:nth-child(20),
#convertedLeadsTable tbody td:nth-child(20) { min-width: 140px; }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Handle filter form submission
        $('#filterForm').on('submit', function(e) {
            e.preventDefault();

            // Get form data
            const formData = new FormData(this);
            const params = new URLSearchParams();

            // Add form data to params
            for (let [key, value] of formData.entries()) {
                if (value.trim() !== '') {
                    params.append(key, value);
                }
            }

            // Redirect with filter parameters
            const url = new URL(window.location.href);
            url.search = params.toString();
            window.location.href = url.toString();
        });

        // Handle clear button
        $('a[href="{{ route("admin.converted-leads.index") }}"]').on('click', function(e) {
            e.preventDefault();
            window.location.href = '{{ route("admin.converted-leads.index") }}';
        });

        // Dependent filters: load batches by course
        function loadBatchesByCourse(courseId, selectedId) {
            const $batch = $('#batch_id');
            $batch.html('<option value="">Loading...</option>');
            if (!courseId) {
                $batch.html('<option value="">All Batches</option>');
                return;
            }
            $.get(`/api/batches/by-course/${courseId}`).done(function(response) {
                let opts = '<option value="">All Batches</option>';
                if (response.success && response.batches) {
                    response.batches.forEach(function(b) {
                        const sel = String(selectedId) === String(b.id) ? 'selected' : '';
                        opts += `<option value="${b.id}" ${sel}>${b.title}</option>`;
                    });
                }
                $batch.html(opts);
            }).fail(function() {
                $batch.html('<option value="">All Batches</option>');
            });
        }

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
        const initialCourse = $('#course_id').val();
        loadBatchesByCourse(initialCourse, $('#batch_id').data('selected'));
        loadAdmissionBatchesByBatch($('#batch_id').data('selected'), $('#admission_batch_id').data('selected'));

        // On course change → reload batches, clear admission batch
        $('#course_id').on('change', function() {
            const cid = $(this).val();
            $('#admission_batch_id').html('<option value="">All Admission Batches</option>');
            loadBatchesByCourse(cid, '');
        });

        // On batch change → reload admission batches
        $('#batch_id').on('change', function() {
            const bid = $(this).val();
            loadAdmissionBatchesByBatch(bid, '');
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
            
            // Prevent multiple submissions
            if (button.prop('disabled')) {
                return false;
            }
            
            const originalText = button.html();
            const loadingText = button.data('loading-text');
            
            // Show loading state
            button.prop('disabled', true).html('<i class="ti ti-loader-2 spin"></i> ' + loadingText);
            
            // Submit form via AJAX
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
                        // Reload page to show updated button
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    }
                },
                error: function(xhr) {
                    console.error('Error generating ID card:', xhr);
                    toast_error('Error generating ID card. Please try again.');
                    // Reset button
                    button.prop('disabled', false).html(originalText);
                }
            });
            
            return false;
        });


        // Inline editing functionality
        $(document).on('click', '.edit-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const container = $(this).closest('.inline-edit');
            const field = container.data('field');
            const id = container.data('id');
            const currentValue = container.data('current') !== undefined ? String(container.data('current')).trim() : container.find('.display-value').text().trim();
            const currentId = container.data('current-id') !== undefined ? String(container.data('current-id')).trim() : '';
            
            // Don't show edit form if already editing
            if (container.hasClass('editing')) {
                return;
            }
            
            // Close any other open inline editors
            $('.inline-edit.editing').not(container).each(function() {
                $(this).removeClass('editing');
                $(this).find('.edit-form').remove();
            });

            // Create edit form based on field type
            let editForm = '';
            
            if (field === 'subject_id') {
                const courseId = container.data('course-id');
                editForm = createSubjectSelect(courseId, currentId);
            } else if (field === 'admission_batch_id') {
                const batchId = container.data('batch-id');
                editForm = createAdmissionBatchSelect(batchId, currentId);
            } else if (field === 'academic_assistant_id') {
                editForm = createAcademicAssistantSelect(currentId);
            } else if (['status', 'reg_fee', 'exam_fee', 'id_card', 'tma'].includes(field)) {
                editForm = createSelectField(field, currentValue);
            } else if (field === 'phone') {
                const currentCode = container.siblings('.inline-code-value').data('current') || '';
                editForm = createPhoneField(currentCode, currentValue);
            } else {
                editForm = createInputField(field, currentValue);
            }
            
            container.addClass('editing');
            container.append(editForm);
            
            // Focus on the input/select
            container.find('input, select').first().focus();
        });

        // Save inline edit (guard against double-clicks)
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
            // Special handling for phone: also capture code
            if (field === 'phone') {
                const codeVal = container.find('select[name="code"]').val();
                extra = { code: codeVal };
            }
            
            // Show loading state
            const btn = $(this);
            if (btn.data('busy')) return; // already in progress
            btn.data('busy', true);
            btn.prop('disabled', true).html('<i class="ti ti-loader-2 spin"></i>');
            
            // Make AJAX request
            $.ajax({
                url: `/admin/converted-leads/${id}/inline-update`,
                method: 'POST',
                data: $.extend({
                    field: field,
                    value: value,
                    _token: $('meta[name="csrf-token"]').attr('content')
                }, extra),
                success: function(response) {
                    if (response.success) {
                        container.find('.display-value').text(response.value || value);
                        if (field === 'phone') {
                            // Persist code next to row for future edits
                            const codeVal = extra.code || '';
                            container.siblings('.inline-code-value').data('current', codeVal);
                        }
                        toast_success(response.message);
                    } else {
                        toast_error(response.error || 'Update failed');
                    }
                },
                error: function(xhr) {
                    const error = xhr.responseJSON?.error || 'Update failed';
                    toast_error(error);
                },
                complete: function() {
                    btn.data('busy', false);
                    container.removeClass('editing');
                    container.find('.edit-form').remove();
                }
            });
        });

        // Cancel inline edit
        $(document).on('click', '.cancel-edit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const container = $(this).closest('.inline-edit');
            container.removeClass('editing');
            container.find('.edit-form').remove();
        });

        // Helper functions for creating form elements
        function createInputField(field, currentValue) {
            // For DOB use date input with max=today and existing value
            if (field === 'dob') {
                const today = new Date().toISOString().split('T')[0];
                const value = (currentValue && currentValue !== 'N/A') ? currentValue : '';
                return `
                    <div class="edit-form">
                        <input type="date" max="${today}" value="${value}" class="form-control form-control-sm">
                        <div class="btn-group mt-1">
                            <button class="btn btn-success btn-sm save-edit">Save</button>
                            <button class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                        </div>
                    </div>
                `;
            }

            // For password, use text (not masked)
            const inputType = 'text';
            const displayValue = currentValue === 'N/A' ? '' : currentValue;
            const commonAttrs = 'autocomplete="off" autocapitalize="off" spellcheck="false" name="inline-temp"';
            const valueAttr = `value="${displayValue}"`;
            return `
                <div class="edit-form">
                    <input type="${inputType}" ${valueAttr} ${commonAttrs} class="form-control form-control-sm">
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
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
            const selectedValue = currentValue === 'N/A' ? '' : currentValue;
            
            switch(field) {
                case 'status':
                    options = '<option value="">Select Status</option>';
                    options += `<option value="Paid" ${selectedValue === 'Paid' ? 'selected' : ''}>Paid</option>`;
                    options += `<option value="Admission cancel" ${selectedValue === 'Admission cancel' ? 'selected' : ''}>Admission cancel</option>`;
                    options += `<option value="Active" ${selectedValue === 'Active' ? 'selected' : ''}>Active</option>`;
                    options += `<option value="Inactive" ${selectedValue === 'Inactive' ? 'selected' : ''}>Inactive</option>`;
                    break;
                case 'reg_fee':
                    options = '<option value="">Select REG. FEE</option>';
                    options += `<option value="Received" ${selectedValue === 'Received' ? 'selected' : ''}>Received</option>`;
                    options += `<option value="Not Received" ${selectedValue === 'Not Received' ? 'selected' : ''}>Not Received</option>`;
                    break;
                case 'exam_fee':
                    options = '<option value="">Select EXAM FEE</option>';
                    options += `<option value="Pending" ${selectedValue === 'Pending' ? 'selected' : ''}>Pending</option>`;
                    options += `<option value="Not Paid" ${selectedValue === 'Not Paid' ? 'selected' : ''}>Not Paid</option>`;
                    options += `<option value="Paid" ${selectedValue === 'Paid' ? 'selected' : ''}>Paid</option>`;
                    break;
                case 'id_card':
                    options = '<option value="">Select ID CARD</option>';
                    options += `<option value="processing" ${selectedValue === 'processing' ? 'selected' : ''}>processing</option>`;
                    options += `<option value="download" ${selectedValue === 'download' ? 'selected' : ''}>download</option>`;
                    options += `<option value="not downloaded" ${selectedValue === 'not downloaded' ? 'selected' : ''}>not downloaded</option>`;
                    break;
                case 'tma':
                    options = '<option value="">Select TMA</option>';
                    options += `<option value="Uploaded" ${selectedValue === 'Uploaded' ? 'selected' : ''}>Uploaded</option>`;
                    options += `<option value="Not Upload" ${selectedValue === 'Not Upload' ? 'selected' : ''}>Not Upload</option>`;
                    break;
            }
            
            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm">
                        ${options}
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createSubjectSelect(courseId, currentId) {
            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm">
                        <option value="">Loading...</option>
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createAdmissionBatchSelect(batchId, currentId) {
            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm">
                        <option value="">Loading...</option>
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createAcademicAssistantSelect(currentId) {
            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm">
                        <option value="">Loading...</option>
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        // Load options for select fields
        $(document).on('click', '.edit-btn', function() {
            const container = $(this).closest('.inline-edit');
            const field = container.data('field');
            const select = container.find('select');
            const currentValue = container.data('current') !== undefined ? String(container.data('current')).trim() : container.find('.display-value').text().trim();
            const currentId = container.data('current-id') !== undefined ? String(container.data('current-id')).trim() : '';
            
            if (field === 'subject_id') {
                const courseId = container.data('course-id');
                loadSubjects(courseId, select, currentId);
            } else if (field === 'admission_batch_id') {
                const batchId = container.data('batch-id');
                loadAdmissionBatches(batchId, select, currentId);
            } else if (field === 'academic_assistant_id') {
                loadAcademicAssistants(select, currentId);
            }
        });

        function loadSubjects(courseId, select, currentId) {
            $.get(`/api/subjects/by-course/${courseId}`)
                .done(function(subjects) {
                    let options = '<option value="">Select Subject</option>';
                    subjects.forEach(function(subject) {
                        const isSelected = String(currentId) === String(subject.id) ? 'selected' : '';
                        options += `<option value="${subject.id}" ${isSelected}>${subject.title}</option>`;
                    });
                    select.html(options);
                })
                .fail(function() {
                    select.html('<option value="">Error loading subjects</option>');
                });
        }

        function loadAdmissionBatches(batchId, select, currentId) {
            $.get(`/api/admission-batches/by-batch/${batchId}`)
                .done(function(batches) {
                    let options = '<option value="">Select Admission Batch</option>';
                    batches.forEach(function(batch) {
                        const isSelected = String(currentId) === String(batch.id) ? 'selected' : '';
                        options += `<option value="${batch.id}" ${isSelected}>${batch.title}</option>`;
                    });
                    select.html(options);
                })
                .fail(function() {
                    select.html('<option value="">Error loading admission batches</option>');
                });
        }

        function loadAcademicAssistants(select, currentId) {
            $.get('/api/academic-assistants')
                .done(function(assistants) {
                    let options = '<option value="">Select Academic Assistant</option>';
                    assistants.forEach(function(assistant) {
                        const isSelected = String(currentId) === String(assistant.id) ? 'selected' : '';
                        options += `<option value="${assistant.id}" ${isSelected}>${assistant.name}</option>`;
                    });
                    select.html(options);
                })
                .fail(function() {
                    select.html('<option value="">Error loading academic assistants</option>');
                });
        }
    });
</script>
@endpush