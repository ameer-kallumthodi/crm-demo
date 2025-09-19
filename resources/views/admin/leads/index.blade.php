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

<!-- [ Date Filter ] start -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('leads.index') }}" id="dateFilterForm">
                    <!-- Desktop Filter Layout -->
                    <div class="d-none d-lg-block">
                        <div class="row align-items-end">
                            <div class="col-md-2">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" class="form-control" name="date_from" 
                                       value="{{ request('date_from', \Carbon\Carbon::now()->subDays(7)->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date" class="form-control" name="date_to" 
                                       value="{{ request('date_to', \Carbon\Carbon::now()->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-2">
                                <label for="lead_status_id" class="form-label">Status</label>
                                <select class="form-select" name="lead_status_id">
                                    <option value="">All Statuses</option>
                                    @foreach($leadStatuses as $status)
                                        <option value="{{ $status->id }}" {{ request('lead_status_id') == $status->id ? 'selected' : '' }}>
                                            {{ $status->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="lead_source_id" class="form-label">Source</label>
                                <select class="form-select" name="lead_source_id">
                                    <option value="">All Sources</option>
                                    @foreach($leadSources as $source)
                                        <option value="{{ $source->id }}" {{ request('lead_source_id') == $source->id ? 'selected' : '' }}>
                                            {{ $source->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="course_id" class="form-label">Course</label>
                                <select class="form-select" name="course_id">
                                    <option value="">All Courses</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                            {{ $course->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @if(!$isTelecaller || $isTeamLead)
                            <div class="col-md-2">
                                <label for="telecaller_id" class="form-label">Telecaller</label>
                                <select class="form-select" id="telecaller_id_filter" name="telecaller_id">
                                    <option value="">All Telecallers</option>
                                    @foreach($telecallers as $telecaller)
                                        <option value="{{ $telecaller->id }}" {{ request('telecaller_id') == $telecaller->id ? 'selected' : '' }}>
                                            {{ $telecaller->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="col-md-3 mt-3">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-filter"></i> Filter
                                    </button>
                                    <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary">
                                        <i class="ti ti-x"></i> Clear
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mobile Filter Layout -->
                    <div class="d-lg-none">
                        <div class="row g-2">
                            <div class="col-6">
                                <label for="date_from_mobile" class="form-label f-12">From Date</label>
                                <input type="date" class="form-control form-control-sm" name="date_from" id="date_from_mobile"
                                       value="{{ request('date_from', \Carbon\Carbon::now()->subDays(7)->format('Y-m-d')) }}">
                            </div>
                            <div class="col-6">
                                <label for="date_to_mobile" class="form-label f-12">To Date</label>
                                <input type="date" class="form-control form-control-sm" name="date_to" id="date_to_mobile"
                                       value="{{ request('date_to', \Carbon\Carbon::now()->format('Y-m-d')) }}">
                            </div>
                            <div class="col-6">
                                <label for="lead_status_id_mobile" class="form-label f-12">Status</label>
                                <select class="form-select form-select-sm" name="lead_status_id" id="lead_status_id_mobile">
                                    <option value="">All Statuses</option>
                                    @foreach($leadStatuses as $status)
                                        <option value="{{ $status->id }}" {{ request('lead_status_id') == $status->id ? 'selected' : '' }}>
                                            {{ $status->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="lead_source_id_mobile" class="form-label f-12">Source</label>
                                <select class="form-select form-select-sm" name="lead_source_id" id="lead_source_id_mobile">
                                    <option value="">All Sources</option>
                                    @foreach($leadSources as $source)
                                        <option value="{{ $source->id }}" {{ request('lead_source_id') == $source->id ? 'selected' : '' }}>
                                            {{ $source->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="course_id_mobile" class="form-label f-12">Course</label>
                                <select class="form-select form-select-sm" name="course_id" id="course_id_mobile">
                                    <option value="">All Courses</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                            {{ $course->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @if(!$isTelecaller || $isTeamLead)
                            <div class="col-6">
                                <label for="telecaller_id_mobile" class="form-label f-12">Telecaller</label>
                                <select class="form-select form-select-sm" name="telecaller_id" id="telecaller_id_mobile">
                                    <option value="">All Telecallers</option>
                                    @foreach($telecallers as $telecaller)
                                        <option value="{{ $telecaller->id }}" {{ request('telecaller_id') == $telecaller->id ? 'selected' : '' }}>
                                            {{ $telecaller->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="col-12 mt-2">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                        <i class="ti ti-filter me-1"></i> Filter
                                    </button>
                                    <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary btn-sm flex-fill">
                                        <i class="ti ti-x me-1"></i> Clear
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- [ Date Filter ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <!-- Desktop Header -->
                <div class="d-none d-md-flex justify-content-between align-items-center">
                    <h5 class="mb-0">All Leads</h5>
                    <div class="d-flex gap-2">
                        @if(!$isTelecaller || $isTeamLead)
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
                        <a href="javascript:void(0);" class="btn btn-outline-danger btn-sm px-3"
                            onclick="show_ajax_modal('{{ route('admin.leads.bulk-delete') }}', 'Bulk Delete Leads')">
                            <i class="ti ti-trash"></i> Bulk Delete
                        </a>
                        @endif
                    </div>
                </div>
                
                <!-- Mobile Header -->
                <div class="d-md-none">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">All Leads</h5>
                        @if(!$isTelecaller || $isTeamLead)
                        <a href="javascript:void(0);" class="btn btn-primary btn-sm"
                            onclick="show_ajax_modal('{{ route('leads.add') }}', 'Add New Lead')">
                            <i class="ti ti-plus"></i> Add
                        </a>
                        @endif
                    </div>
                    
                    @if(!$isTelecaller || $isTeamLead)
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
                        <div class="col-6">
                            <a href="javascript:void(0);" class="btn btn-outline-danger btn-sm w-100"
                                onclick="show_ajax_modal('{{ route('admin.leads.bulk-delete') }}', 'Bulk Delete Leads')">
                                <i class="ti ti-trash me-1"></i> Delete
                            </a>
                        </div>
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
                    <div class="table-responsive">
                        <table class="table table-hover" id="leadsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Actions</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Source</th>
                                    <th>Course</th>
                                    <th>Telecaller</th>
                                    <th>Place</th>
                                    <th>Remarks</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leads as $index => $lead)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary"
                                                onclick="show_large_modal('{{ route('leads.ajax-show', $lead->id) }}', 'View Lead')"
                                                title="View Lead">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            <a href="javascript:void(0);" class="btn btn-sm btn-outline-secondary"
                                                onclick="show_ajax_modal('{{ route('leads.ajax-edit', $lead->id) }}', 'Edit Lead')"
                                                title="Edit Lead">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <a href="javascript:void(0);" class="btn btn-sm btn-outline-success"
                                                onclick="show_ajax_modal('{{ route('leads.status-update', $lead->id) }}', 'Update Status')"
                                                title="Update Status">
                                                <i class="ti ti-arrow-up"></i>
                                            </a>
                                            @if(!$lead->is_converted)
                                            <a href="javascript:void(0);" class="btn btn-sm btn-outline-warning"
                                                onclick="show_ajax_modal('{{ route('leads.convert', $lead->id) }}', 'Convert Lead')"
                                                title="Convert Lead">
                                                <i class="ti ti-refresh"></i>
                                            </a>
                                            @endif
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
                                            @if(!$isTelecaller || $isTeamLead)
                                            <a href="javascript:void(0);" class="btn btn-sm btn-outline-danger"
                                                onclick="show_ajax_modal('{{ route('leads.delete', $lead->id) }}', 'Delete Lead')"
                                                title="Delete Lead">
                                                <i class="ti ti-trash"></i>
                                            </a>
                                            @endif
                                        </div>
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
                                    <td>{{ \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone) }}</td>
                                    <td>{{ $lead->email ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ \App\Helpers\StatusHelper::getLeadStatusColorClass($lead->leadStatus->id) }}">
                                            {{ $lead->leadStatus->title }}
                                        </span>
                                    </td>
                                    <td>{{ $lead->leadSource->title ?? '-' }}</td>
                                    <td>{{ $lead->course->title ?? '-' }}</td>
                                    <td>{{ $lead->telecaller->name ?? 'Unassigned' }}</td>
                                    <td>{{ $lead->place ?? '-' }}</td>
                                    <td>{{ $lead->remarks ? Str::limit($lead->remarks, 30) : '-' }}</td>
                                    <td>{{ $lead->created_at->format('M d, Y') }}</td>
                                    <td>{{ $lead->created_at->format('H:i A') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="13" class="text-center py-4">
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

                <!-- Mobile Card View -->
                <div class="d-lg-none">
                    @forelse($leads as $index => $lead)
                    <div class="card mb-2">
                        <div class="card-body p-3">
                            <!-- Lead Header -->
                            <div class="d-flex align-items-start justify-content-between mb-2">
                                <div class="d-flex align-items-center flex-grow-1">
                                    <div class="avtar avtar-s rounded-circle bg-light-primary me-2 d-flex align-items-center justify-content-center">
                                        <span class="f-14 fw-bold text-primary">{{ strtoupper(substr($lead->title, 0, 1)) }}</span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fw-bold f-14">{{ $lead->title }}</h6>
                                        <small class="text-muted f-11">#{{ $index + 1 }}</small>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false" id="dropdownMenuButton{{ $lead->id }}">
                                        <i class="ti ti-dots-vertical f-12"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="javascript:void(0);" onclick="show_large_modal('{{ route('leads.ajax-show', $lead->id) }}', 'View Lead')">
                                            <i class="ti ti-eye me-2"></i>View Lead
                                        </a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0);" onclick="show_ajax_modal('{{ route('leads.ajax-edit', $lead->id) }}', 'Edit Lead')">
                                            <i class="ti ti-edit me-2"></i>Edit Lead
                                        </a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0);" onclick="show_ajax_modal('{{ route('leads.status-update', $lead->id) }}', 'Update Status')">
                                            <i class="ti ti-arrow-up me-2"></i>Update Status
                                        </a></li>
                                        @if(!$lead->is_converted)
                                        <li><a class="dropdown-item" href="javascript:void(0);" onclick="show_ajax_modal('{{ route('leads.convert', $lead->id) }}', 'Convert Lead')">
                                            <i class="ti ti-refresh me-2"></i>Convert Lead
                                        </a></li>
                                        @endif
                                        @if($lead->lead_status_id == 6)
                                        <li><a class="dropdown-item" href="https://docs.google.com/forms/d/e/1FAIpQLSchtc8xlKUJehZNmzoKTkRvwLwk4-SGjzKSHM2UFToAhgdTlQ/viewform?usp=sf_link" target="_blank">
                                            <i class="ti ti-file-text me-2"></i>Demo Form
                                        </a></li>
                                        @endif
                                        @if(!$isTelecaller || $isTeamLead)
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="javascript:void(0);" onclick="show_ajax_modal('{{ route('leads.delete', $lead->id) }}', 'Delete Lead')">
                                            <i class="ti ti-trash me-2"></i>Delete Lead
                                        </a></li>
                                        @endif
                                    </ul>
                                </div>
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
                            </div>

                            <!-- Action Buttons - Compact -->
                            <div class="d-flex gap-1 flex-wrap justify-content-end">
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
                                <a href="javascript:void(0);" class="btn btn-sm btn-primary"
                                   onclick="show_large_modal('{{ route('leads.ajax-show', $lead->id) }}', 'View Lead')">
                                    <i class="ti ti-eye f-12"></i>
                                </a>
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

                <!-- Pagination -->
                @if($leads->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $leads->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->


@endsection

@push('scripts')
<script>
$(document).ready(function() {

    // Only initialize if not already initialized
    if (!$.fn.DataTable.isDataTable('#leadsTable')) {
        // Initialize DataTable
        var table = $('#leadsTable').DataTable({
        "processing": true,
        "serverSide": false,
        "responsive": true,
        "pageLength": 25,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "columnDefs": [
            { "orderable": false, "targets": [0, 8] }, // Disable sorting on serial number and actions columns
            { "searchable": false, "targets": [0, 8] } // Disable searching on serial number and actions columns
        ],
        "language": {
            "processing": "Loading leads...",
            "emptyTable": "No leads found",
            "zeroRecords": "No matching leads found"
        }
        });


        // Remove the old search input functionality since DataTable handles it
        $('#searchInput').remove();
    }

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

    // Initialize Bootstrap dropdowns
    function initializeDropdowns() {
        // Check if Bootstrap 5 is available
        if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });
        } else {
            // Fallback for Bootstrap 4 or if Bootstrap 5 is not available
            $('.dropdown-toggle').dropdown();
        }
    }

    // Initialize dropdowns on page load
    initializeDropdowns();

    // Re-initialize dropdowns when new content is loaded (for AJAX)
    $(document).on('click', '.dropdown-toggle', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var $this = $(this);
        var $dropdown = $this.next('.dropdown-menu');
        
        // Toggle dropdown manually if Bootstrap is not working
        if ($dropdown.length) {
            $dropdown.toggleClass('show');
            $this.attr('aria-expanded', $dropdown.hasClass('show'));
        }
    });

    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.dropdown').length) {
            $('.dropdown-menu').removeClass('show');
            $('.dropdown-toggle').attr('aria-expanded', 'false');
        }
    });
});

</script>
@endpush