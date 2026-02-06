@extends('layouts.mantis')

@section('title', 'Course Converted Leads Report - ' . $course->title)

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Course Converted Leads Report</h5>
                    <p class="m-b-0">{{ $course->title }} - Converted leads analysis</p>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reports.course-summary') }}">Course Reports</a></li>
                    <li class="breadcrumb-item">Converted Leads</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Filter Section ] start -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.course-converted-leads', $course->id) }}" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" name="date_from" id="date_from" 
                                   value="{{ $fromDate }}">
                        </div>
                        <div class="col-md-4">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" name="date_to" id="date_to" 
                                   value="{{ $toDate }}">
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-search"></i> Filter
                                </button>
                                <a href="{{ route('admin.reports.course-converted-leads', $course->id) }}" class="btn btn-outline-secondary">
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

<!-- [ Course Info Card ] start -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avtar avtar-lg bg-light-success me-3">
                        <i class="ti ti-user-check text-success"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="mb-1">{{ $course->title }} - Converted Leads</h4>
                        <p class="text-muted mb-0">Course ID: {{ $course->id }}</p>
                    </div>
                    <div class="text-end">
                        <h6 class="mb-1">Total Converted</h6>
                        <h3 class="mb-0 text-success">{{ $convertedLeads->total() }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Course Info Card ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <a href="{{ route('admin.reports.course-summary') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="ti ti-arrow-left"></i> Back to Course Summary
                        </a>
                        <h5 class="mb-0">Converted Leads for {{ $course->title }}</h5>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.reports.course-leads', $course->id) }}?date_from={{ $fromDate }}&date_to={{ $toDate }}" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="ti ti-users"></i> View All Leads
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover data_table_basic">
                        <thead class="table-light">
                            <tr>
                                <th class="no-sort">#</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>WhatsApp</th>
                                @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                <th>Parent Phone</th>
                                @endif
                                <th>Email</th>
                                <th>Register Number</th>
                                <th>Academic Assistant</th>
                                <th>Converted Date</th>
                                <th class="no-sort">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($convertedLeads as $index => $convertedLead)
                            <tr>
                                <td>{{ $convertedLeads->firstItem() + $index }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avtar avtar-s bg-light-success me-2">
                                            <span class="text-success fw-bold">{{ strtoupper(substr($convertedLead->name, 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $convertedLead->name }}</h6>
                                            <small class="text-muted">Lead ID: {{ $convertedLead->lead_id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</td>
                                <td>
                                    @if($convertedLead->leadDetail && $convertedLead->leadDetail->whatsapp_number)
                                        {{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->whatsapp_code, $convertedLead->leadDetail->whatsapp_number) }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                <td>
                                    @if($convertedLead->leadDetail && $convertedLead->leadDetail->parents_number)
                                        {{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->parents_code, $convertedLead->leadDetail->parents_number) }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                @endif
                                <td>{{ $convertedLead->email ?? 'N/A' }}</td>
                                <td>
                                    @if($convertedLead->register_number)
                                    <span class="badge bg-success">{{ $convertedLead->register_number }}</span>
                                    @else
                                    <span class="text-muted">Not Set</span>
                                    @endif
                                </td>
                                <td>{{ $convertedLead->academicAssistant->name ?? 'N/A' }}</td>
                                <td>{{ $convertedLead->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.converted-leads.show', $convertedLead->id) }}" 
                                           class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.invoices.index', $convertedLead->id) }}" 
                                           class="btn btn-sm btn-outline-success" title="View Invoice">
                                            <i class="ti ti-file-text"></i>
                                        </a>
                                        @if($convertedLead->register_number)
                                        <a href="{{ route('admin.converted-leads.id-card-pdf', $convertedLead->id) }}" 
                                           class="btn btn-sm btn-outline-warning" title="Generate ID Card PDF" target="_blank">
                                            <i class="ti ti-id-badge"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="avtar avtar-xl bg-light-secondary mb-3">
                                            <i class="ti ti-user-x text-secondary"></i>
                                        </div>
                                        <h5 class="text-muted">No Converted Leads Found</h5>
                                        <p class="text-muted">No converted leads found for {{ $course->title }} in the selected date range.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($convertedLeads->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $convertedLeads->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

@endsection
