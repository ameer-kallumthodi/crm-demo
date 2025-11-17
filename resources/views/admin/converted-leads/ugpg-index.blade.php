@extends('layouts.mantis')

@section('title', 'UG/PG Converted Leads')

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
                    <h5 class="m-b-10">UG/PG Converted Leads Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.converted-leads.index') }}">Converted Leads</a></li>
                    <li class="breadcrumb-item">UG/PG</li>
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
                    <a href="{{ route('admin.ugpg-converted-leads.index') }}" class="btn btn-warning active">
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
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.ugpg-converted-leads.index') }}" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search"
                                value="{{ request('search') }}" placeholder="Name, Phone, Email, Register Number">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="university_id" class="form-label">University</label>
                            <select class="form-select" id="university_id" name="university_id">
                                <option value="">All Universities</option>
                                @foreach($universities as $university)
                                <option value="{{ $university->id }}" {{ request('university_id') == $university->id ? 'selected' : '' }}>
                                    {{ $university->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="course_type" class="form-label">Course Type</label>
                            <select class="form-select" id="course_type" name="course_type">
                                <option value="">All</option>
                                <option value="UG" {{ request('course_type') === 'UG' ? 'selected' : '' }}>UG</option>
                                <option value="PG" {{ request('course_type') === 'PG' ? 'selected' : '' }}>PG</option>
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
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-search"></i> Filter
                                </button>
                                <a href="{{ route('admin.ugpg-converted-leads.index') }}" class="btn btn-secondary">
                                    <i class="ti ti-x"></i> Clear
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
                <h5 class="mb-0">UG/PG Converted Leads List</h5>
            </div>
            <div class="card-body">
                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover data_table_basic" id="ugpgTable">
                            <thead>
                                <tr>
                                    <th>SL No</th>
                                <th>Academic</th>
                                <th>Support</th>
                                    <th>Converted Date</th>
                                    <th>Register Number</th>
                                    <th>Name</th>
                                    <th>DOB</th>
                                    <th>Phone</th>
                                <th>WhatsApp Number</th>
                                <th>Email</th>
                                    <th>Board/University</th>
                                    <th>Course Type</th>
                                    <th>Course Name</th>
                                    <th>Back Year</th>
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
                                        <div class="inline-edit" data-field="dob" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->leadDetail?->date_of_birth ? $convertedLead->leadDetail->date_of_birth->format('Y-m-d') : ($convertedLead->dob ?: '') }}">
                                            @php
                                                $dobDisplay = '-';
                                                if ($convertedLead->leadDetail && $convertedLead->leadDetail->date_of_birth) {
                                                    $dobDisplay = $convertedLead->leadDetail->date_of_birth->format('d-m-Y');
                                                } elseif ($convertedLead->dob) {
                                                    $dobDisplay = strtotime($convertedLead->dob) ? date('d-m-Y', strtotime($convertedLead->dob)) : $convertedLead->dob;
                                                }
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
                                        <div class="inline-edit" data-field="whatsapp_number" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->leadDetail?->whatsapp_number }}">
                                            <span class="display-value">{{ $convertedLead->leadDetail ? \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->whatsapp_code, $convertedLead->leadDetail->whatsapp_number) : '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                        <div class="d-none inline-code-value" data-field="whatsapp_code" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->leadDetail?->whatsapp_code }}"></div>
                                    </td>
                                    <td>{{ $convertedLead->email ?? '-' }}</td>
                                    <td>
                                        <div class="inline-edit" data-field="university_id" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->leadDetail?->university_id }}">
                                            <span class="display-value">{{ $convertedLead->leadDetail?->university?->title ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="course_type" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->leadDetail?->course_type }}">
                                            <span class="display-value">{{ $convertedLead->leadDetail?->course_type ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="university_course_id" data-id="{{ $convertedLead->id }}" data-university-id="{{ $convertedLead->leadDetail?->university_id }}" data-current="{{ $convertedLead->leadDetail?->university_course_id }}">
                                            <span class="display-value">{{ $convertedLead->leadDetail?->universityCourse?->title ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="passed_year" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->leadDetail?->passed_year }}">
                                            <span class="display-value">{{ $convertedLead->leadDetail?->passed_year ?? '-' }}</span>
                                            @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant())
                                            <button class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="" role="group">
                                            <a href="{{ route('admin.converted-leads.show', $convertedLead->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="15" class="text-center">No UG/PG converted leads found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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

#ugpgTable thead th,
#ugpgTable tbody td {
    white-space: nowrap;
}

#ugpgTable thead th {
    position: sticky;
    top: 0;
    z-index: 5;
    background: #fff;
    box-shadow: inset 0 -1px 0 #e9ecef;
}

#ugpgTable tbody tr:hover {
    background: #fafbff;
}

#ugpgTable td .display-value {
    display: inline-block;
    max-width: 220px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    vertical-align: middle;
}
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Inline editing functionality
        $(document).on('click', '.edit-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const container = $(this).closest('.inline-edit');
            const field = container.data('field');
            const id = container.data('id');
            const currentValue = container.data('current') !== undefined ? String(container.data('current')).trim() : container.find('.display-value').text().trim();
            const currentId = container.data('current-id') !== undefined ? String(container.data('current-id')).trim() : '';
            const universityId = container.data('university-id');
            
            if (container.hasClass('editing')) {
                return;
            }
            
            $('.inline-edit.editing').not(container).each(function() {
                $(this).removeClass('editing');
                $(this).find('.edit-form').remove();
            });

            let editForm = '';
            
            if (field === 'phone' || field === 'whatsapp_number') {
                const currentCode = container.siblings('.inline-code-value').data('current') || '';
                const codeField = field === 'phone' ? 'code' : 'whatsapp_code';
                editForm = createPhoneField(currentCode, currentValue, codeField);
            } else if (field === 'course_type') {
                editForm = createCourseTypeSelect(currentValue);
            } else if (field === 'university_id') {
                editForm = createUniversitySelect(currentValue);
            } else if (field === 'university_course_id') {
                editForm = createUniversityCourseSelect(universityId, currentValue);
            } else if (field === 'dob') {
                editForm = createDateField(field, currentValue);
            } else if (field === 'passed_year') {
                editForm = createYearField(field, currentValue);
            } else {
                editForm = createInputField(field, currentValue);
            }
            
            container.addClass('editing');
            container.append(editForm);
            
            // Load university courses if needed
            if (field === 'university_course_id') {
                const $select = container.find('select');
                if (universityId) {
                    loadUniversityCourses(universityId, $select, currentValue);
                }
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
            let extra = {};
            
            if (field === 'phone' || field === 'whatsapp_number') {
                value = container.find('input[type="text"]').val();
                const codeField = field === 'phone' ? 'code' : 'whatsapp_code';
                const codeVal = container.find('select[name="' + codeField + '"]').val();
                extra[codeField] = codeVal;
            } else {
                value = container.find('input, select').val();
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
                        let displayValue = response.value || value;
                        
                        // Special handling for DOB field
                        if (field === 'dob' && displayValue) {
                            try {
                                const date = new Date(displayValue);
                                if (!isNaN(date.getTime())) {
                                    displayValue = date.toLocaleDateString('en-GB'); // d/m/Y format
                                }
                            } catch (e) {
                                // Keep original value if conversion fails
                            }
                        }
                        
                        container.find('.display-value').text(displayValue);
                        container.data('current', response.value || value);
                        
                        if (field === 'phone') {
                            const codeVal = extra.code || '';
                            container.siblings('.inline-code-value').data('current', codeVal);
                        } else if (field === 'whatsapp_number') {
                            const codeVal = extra.whatsapp_code || '';
                            container.siblings('.inline-code-value[data-field="whatsapp_code"]').data('current', codeVal);
                        } else if (field === 'university_id') {
                            // Reload university course options when university changes
                            const $universityCourseContainer = container.closest('tr').find('[data-field="university_course_id"]');
                            if ($universityCourseContainer.length) {
                                $universityCourseContainer.data('university-id', value);
                                $universityCourseContainer.data('current', ''); // Reset course selection
                                $universityCourseContainer.find('.display-value').text('-');
                            }
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
        });

        // Helper functions for creating form elements
        function createInputField(field, currentValue) {
            const displayValue = currentValue === '-' ? '' : currentValue;
            return `
                <div class="edit-form">
                    <input type="text" value="${displayValue}" class="form-control form-control-sm" autocomplete="off">
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createDateField(field, currentValue) {
            let value = '';
            if (currentValue && currentValue !== '-') {
                if (currentValue.match(/^\d{4}-\d{2}-\d{2}$/)) {
                    value = currentValue;
                } else if (currentValue.includes('-')) {
                    const parts = currentValue.split('-');
                    if (parts.length === 3) {
                        if (parts[0].length <= 2 && parts[1].length <= 2 && parts[2].length === 4) {
                            const day = parts[0].padStart(2, '0');
                            const month = parts[1].padStart(2, '0');
                            const year = parts[2];
                            value = `${year}-${month}-${day}`;
                        }
                    }
                }
            }
            const today = new Date().toISOString().split('T')[0];
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

        function createYearField(field, currentValue) {
            const displayValue = currentValue === '-' ? '' : currentValue;
            const currentYear = new Date().getFullYear();
            return `
                <div class="edit-form">
                    <input type="number" min="1900" max="${currentYear}" value="${displayValue}" class="form-control form-control-sm" placeholder="Year">
                    <div class="btn-group mt-1">
                        <button class="btn btn-success btn-sm save-edit">Save</button>
                        <button class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createCourseTypeSelect(currentValue) {
            return `
                <div class="edit-form">
                    <select class="form-select form-select-sm">
                        <option value="">Select</option>
                        <option value="UG" ${currentValue === 'UG' ? 'selected' : ''}>UG</option>
                        <option value="PG" ${currentValue === 'PG' ? 'selected' : ''}>PG</option>
                    </select>
                    <div class="btn-group mt-1">
                        <button type="button" class="btn btn-success btn-sm save-edit">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                    </div>
                </div>
            `;
        }

        function createUniversitySelect(currentValue) {
            let options = '<option value="">Select University</option>';
            @foreach($universities as $university)
                const selected{{ $university->id }} = String(currentValue) === '{{ $university->id }}' ? 'selected' : '';
                options += `<option value="{{ $university->id }}" ${selected{{ $university->id }}}>{{ $university->title }}</option>`;
            @endforeach
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

        function createUniversityCourseSelect(universityId, currentValue) {
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

        function loadUniversityCourses(universityId, $select, currentValue) {
            if (!universityId) {
                $select.html('<option value="">Select University First</option>');
                return;
            }
            
            $.get(`/api/university-courses/by-university/${universityId}`)
                .done(function(courses) {
                    let options = '<option value="">Select Course</option>';
                    courses.forEach(function(course) {
                        const isSelected = String(currentValue) === String(course.id) ? 'selected' : '';
                        options += `<option value="${course.id}" ${isSelected}>${course.title}</option>`;
                    });
                    $select.html(options);
                })
                .fail(function() {
                    $select.html('<option value="">Error loading courses</option>');
                });
        }

        function createPhoneField(currentCode, currentPhone, codeFieldName) {
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
            const safePhone = (currentPhone && currentPhone !== 'N/A' && currentPhone !== '-') ? currentPhone : '';
            return `
                <div class="edit-form">
                    <div class="row g-1">
                        <div class="col-5">
                            <select name="${codeFieldName}" class="form-select form-select-sm">${buildOptions(currentCode)}</select>
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

