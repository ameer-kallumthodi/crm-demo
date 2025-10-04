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
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from"
                                value="{{ request('date_from') }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to"
                                value="{{ request('date_to') }}">
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
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Register Number</th>
                                    <th>Course</th>
                                    <th>Academic Assistant</th>
                                    <th>Converted Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($convertedLeads as $index => $convertedLead)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
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
                                    <td>{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</td>
                                    <td>{{ $convertedLead->email ?? 'N/A' }}</td>
                                    <td>
                                        @if($convertedLead->register_number)
                                        <span class="badge bg-success">{{ $convertedLead->register_number }}</span>
                                        @else
                                        <span class="text-muted">Not Set</span>
                                        @endif
                                    </td>
                                    <td>{{ $convertedLead->course ? $convertedLead->course->title : 'N/A' }}</td>
                                    <td>{{ $convertedLead->academicAssistant ? $convertedLead->academicAssistant->name : 'N/A' }}</td>
                                    <td>{{ $convertedLead->created_at->format('M d, Y') }}</td>
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
                                            <a href="{{ route('admin.converted-leads.id-card-pdf', $convertedLead->id) }}" class="btn btn-sm btn-warning" title="Generate ID Card PDF" target="_blank">
                                                <i class="ti ti-id"></i>
                                            </a>
                                            @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">No converted leads found</td>
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
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
@endsection

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

        // Handle update register number button clicks
        $('.update-register-btn').on('click', function(e) {
            e.preventDefault();
            const url = $(this).data('url');
            const title = $(this).data('title');
            show_small_modal(url, title);
        });
    });
</script>
@endpush