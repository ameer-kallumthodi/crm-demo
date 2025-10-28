@extends('layouts.mantis')

@section('title', 'E-School Converted Leads')

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
                    <h5 class="m-b-10">E-School Converted Leads Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.converted-leads.index') }}">Converted Leads</a></li>
                    <li class="breadcrumb-item">E-School</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Course Filter Buttons ] start -->
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
                    <a href="{{ route('admin.e-school-converted-leads.index') }}" class="btn btn-primary active">
                        <i class="ti ti-device-laptop"></i> E-School Converted Leads
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
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
                <form id="filterForm" method="GET" action="{{ route('admin.e-school-converted-leads.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   placeholder="Name, Phone, Email, Register No" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-3">
                            <label for="admission_batch_id" class="form-label">Admission Batch</label>
                            <select class="form-select" id="admission_batch_id" name="admission_batch_id" data-selected="{{ request('admission_batch_id') }}">
                                <option value="">All Admission Batches</option>
                                @foreach($admission_batches as $admission_batch)
                                    <option value="{{ $admission_batch->id }}" {{ request('admission_batch_id') == $admission_batch->id ? 'selected' : '' }}>
                                        {{ $admission_batch->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="sub_course_id" class="form-label">Sub Course</label>
                            <select class="form-select" id="sub_course_id" name="sub_course_id">
                                <option value="">All Sub Courses</option>
                                @foreach($sub_courses as $sub_course)
                                    <option value="{{ $sub_course->id }}" {{ request('sub_course_id') == $sub_course->id ? 'selected' : '' }}>
                                        {{ $sub_course->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-3">
                            <label for="teacher_id" class="form-label">Teacher</label>
                            <select class="form-select" id="teacher_id" name="teacher_id">
                                <option value="">All Teachers</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="class_status" class="form-label">Class Status</label>
                            <select class="form-select" id="class_status" name="class_status">
                                <option value="">All</option>
                                <option value="ongoing" {{ request('class_status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                <option value="completed" {{ request('class_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="dropout" {{ request('class_status') == 'dropout' ? 'selected' : '' }}>Dropout</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-filter"></i> Filter
                            </button>
                            <a href="{{ route('admin.e-school-converted-leads.index') }}" class="btn btn-secondary">
                                <i class="ti ti-x"></i> Clear
                            </a>
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
                <h5>E-School Converted Leads</h5>
            </div>
            <div class="card-body">
                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover data_table_basic" id="eschoolTable">
                        <thead>
                            <tr>
                                <th>SL No</th>
                                <th>Converted Date</th>
                                <th>Registration Number</th>
                                <th>Phone</th>
                                <th>Teacher</th>
                                <th>Batch</th>
                                <th>Admission Batch</th>
                                <th>Sub Course</th>
                                <th>Subject</th>
                                <th>Screening</th>
                                <th>Class Time</th>
                                <th>Class Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($convertedLeads as $index => $convertedLead)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $convertedLead->created_at->format('d-m-Y') }}</td>
                                <td>
                                    <div class="inline-edit" data-field="register_number" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->register_number }}">
                                        <span class="display-value">{{ $convertedLead->register_number ?: '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="phone" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->phone }}">
                                        <span class="display-value">{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) ?: '-' }}</span>
                                        <span class="inline-code-value d-none" data-current="{{ $convertedLead->code }}"></span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="teacher_id" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->teacher_id }}">
                                        <span class="display-value">{{ $convertedLead->studentDetails?->teacher?->name ?: '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $convertedLead->batch?->title ?: '-' }}</td>
                                <td>
                                    <div class="inline-edit" data-field="admission_batch_id" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->admission_batch_id }}" data-batch-id="{{ $convertedLead->batch_id }}">
                                        <span class="display-value">{{ $convertedLead->admissionBatch?->title ?: '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="sub_course_id" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->sub_course_id }}">
                                        <span class="display-value">{{ $convertedLead->subCourse?->title ?: '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="subject_id" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->subject_id }}">
                                        <span class="display-value">{{ $convertedLead->subject?->title ?: '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-edit" data-field="screening" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->screening }}">
                                        <span class="display-value">{{ $convertedLead->studentDetails?->screening ? $convertedLead->studentDetails->screening->format('d-m-Y') : '-' }}</span>
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
                                        <span class="display-value">{{ $convertedLead->studentDetails?->class_status ?: '-' }}</span>
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                        <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.converted-leads.show', $convertedLead->id) }}" 
                                           class="btn btn-sm btn-info" title="View Details">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="13" class="text-center">No E-School converted leads found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="d-lg-none">
                    @forelse($convertedLeads as $index => $convertedLead)
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-start">
                                <div class="avtar avtar-s rounded-circle bg-light-primary me-3 d-flex align-items-center justify-content-center">
                                    <span class="f-16 fw-bold text-primary">{{ strtoupper(substr($convertedLead->name, 0, 1)) }}</span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $convertedLead->name }}</h6>
                                    <small class="text-muted">{{ $convertedLead->register_number ?: 'No Registration Number' }}</small>
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
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-6">
                                    <small class="text-muted d-block">Phone</small>
                                    <span class="fw-medium">{{ $convertedLead->phone ?: 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Teacher</small>
                                    <span class="fw-medium">{{ $convertedLead->studentDetails?->teacher?->name ?: 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Batch</small>
                                    <span class="fw-medium">{{ $convertedLead->batch?->title ?: 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Sub Course</small>
                                    <span class="fw-medium">{{ $convertedLead->subCourse?->title ?: 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Subject</small>
                                    <span class="fw-medium">{{ $convertedLead->subject?->title ?: 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Class Status</small>
                                    <span class="fw-medium">{{ $convertedLead->studentDetails?->class_status ?: 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Registration Number</small>
                                    <span class="fw-medium">{{ $convertedLead->register_number ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Converted Date</small>
                                    <span class="fw-medium">{{ $convertedLead->created_at->format('d-m-Y') }}</span>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('admin.converted-leads.show', $convertedLead->id) }}"
                                   class="btn btn-sm btn-info">
                                    <i class="ti ti-eye me-1"></i>View Details
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="ti ti-inbox f-48 text-muted"></i>
                        <p class="text-muted mt-2">No E-School converted leads found.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

<!-- Country Codes JSON for JavaScript -->
<script type="application/json" id="country-codes-json">
{!! json_encode($country_codes) !!}
</script>
@endsection

@push('styles')
<style>
.inline-edit {
    position: relative;
}

.inline-edit .edit-form {
    position: absolute;
    top: 0;
    left: -8px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 8px;
    z-index: 1000;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    min-width: 320px;
    max-width: 440px;
}

.inline-edit .edit-form input,
.inline-edit .edit-form select {
    width: 100%;
    margin-bottom: 4px;
}

.inline-edit .edit-form .btn-group {
    display: flex;
    gap: 4px;
}

.inline-edit .edit-form .btn {
    flex: 1;
    font-size: 11px;
}

#eschoolTable thead th,
#eschoolTable tbody td {
    white-space: nowrap;
}

#eschoolTable thead th {
    position: sticky;
    top: 0;
    z-index: 5;
    background: #fff;
    box-shadow: inset 0 -1px 0 #e9ecef;
}

#eschoolTable tbody tr:hover {
    background: #fafbff;
}

#eschoolTable td .display-value {
    display: inline-block;
    max-width: 220px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    vertical-align: middle;
}

#eschoolTable .btn-group .btn { margin-right: 4px; }
#eschoolTable .btn-group .btn:last-child { margin-right: 0; }
</style>
@endpush

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
            const id = container.data('id');
            const currentValue = container.data('current') !== undefined ? String(container.data('current')).trim() : container.find('.display-value').text().trim();
            
            if (container.hasClass('editing')) {
                return;
            }
            
            $('.inline-edit.editing').not(container).each(function() {
                $(this).removeClass('editing');
                $(this).find('.edit-form').remove();
            });

            let editForm = '';
            
            if (field === 'phone') {
                const currentCode = container.siblings('.inline-code-value').data('current') || '';
                editForm = createPhoneField(currentCode, currentValue);
            } else if (['class_status'].includes(field)) {
                editForm = createSelectField(field, currentValue);
            } else if (['screening'].includes(field)) {
                editForm = createDateField(field, currentValue);
            } else if (field === 'class_time') {
                editForm = createTimeField(field, currentValue);
            } else if (field === 'admission_batch_id') {
                const batchId = container.data('batch-id');
                editForm = createAdmissionBatchField(batchId, currentValue);
            } else if (['teacher_id', 'sub_course_id', 'subject_id'].includes(field)) {
                editForm = createSelectField(field, currentValue);
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
            
            // Load options for select fields
            if (['teacher_id', 'sub_course_id', 'subject_id', 'class_status'].includes(field)) {
                const $select = container.find('select');
                loadSelectOptions($select, field, currentValue);
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
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        } else if (xhr.responseJSON.errors) {
                            // Handle validation errors
                            const errors = xhr.responseJSON.errors;
                            const fieldErrors = Object.values(errors).flat();
                            errorMessage = fieldErrors.join(', ');
                        }
                    }
                    toast_error(errorMessage);
                },
                complete: function() {
                    btn.data('busy', false);
                    container.removeClass('editing');
                    container.find('.edit-form').remove();
                }
            });
        });
        
        // Cancel edit
        $(document).on('click', '.cancel-edit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const container = $(this).closest('.inline-edit');
            container.removeClass('editing');
            container.find('.edit-form').remove();
            container.find('.display-value').show();
            container.find('.edit-btn').show();
        });

        // Helper functions for creating form elements
        function createInputField(field, currentValue) {
            const displayValue = currentValue === '-' ? '' : currentValue;
            return `
                <div class="edit-form">
                    <input type="text" value="${displayValue}" class="form-control form-control-sm" autocomplete="off" autocapitalize="off" spellcheck="false">
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createDateField(field, currentValue) {
            const value = (currentValue && currentValue !== '-') ? currentValue : '';
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

        function createSelectField(field, currentValue) {
            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm" data-field="${field}">
                        <option value="">Select ${field.replace('_', ' ')}</option>
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
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

        function loadSelectOptions($select, field, currentValue) {
            let options = `<option value="">Select ${field.replace('_', ' ')}</option>`;
            
            if (field === 'teacher_id') {
                @foreach($teachers as $teacher)
                    const selected{{ $teacher->id }} = String(currentValue) === '{{ $teacher->id }}' ? 'selected' : '';
                    options += `<option value="{{ $teacher->id }}" ${selected{{ $teacher->id }}}>{{ $teacher->name }}</option>`;
                @endforeach
            } else if (field === 'sub_course_id') {
                @foreach($sub_courses as $sub_course)
                    const selected{{ $sub_course->id }} = String(currentValue) === '{{ $sub_course->id }}' ? 'selected' : '';
                    options += `<option value="{{ $sub_course->id }}" ${selected{{ $sub_course->id }}}>{{ $sub_course->title }}</option>`;
                @endforeach
            } else if (field === 'subject_id') {
                @foreach($subjects as $subject)
                    const selected{{ $subject->id }} = String(currentValue) === '{{ $subject->id }}' ? 'selected' : '';
                    options += `<option value="{{ $subject->id }}" ${selected{{ $subject->id }}}>{{ $subject->title }}</option>`;
                @endforeach
            } else if (field === 'class_status') {
                const statuses = ['ongoing', 'completed', 'dropout'];
                statuses.forEach(function(status) {
                    const selected = String(currentValue) === status ? 'selected' : '';
                    options += `<option value="${status}" ${selected}>${status.charAt(0).toUpperCase() + status.slice(1)}</option>`;
                });
            }
            
            $select.html(options);
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
                            <select name="code" class="form-select form-select-sm">${buildOptions(currentCode)}</select>
                        </div>
                        <div class="col-7">
                            <input type="text" value="${safePhone}" class="form-control form-control-sm" placeholder="Phone">
                        </div>
                    </div>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }
    });
</script>
@endpush
