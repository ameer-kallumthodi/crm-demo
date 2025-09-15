@extends('layouts.mantis')

@section('title', 'Leads')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Leads Management</h5>
                </div>
                <ul class="breadcrumb">
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
                <div class="d-flex justify-content-between align-items-center">
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
            </div>
            <div class="card-body">
                <!-- Table -->
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
                                            onclick="show_large_modal('{{ route('leads.ajax-show', $lead->id) }}', 'View Lead')">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-secondary"
                                            onclick="show_ajax_modal('{{ route('leads.ajax-edit', $lead->id) }}', 'Edit Lead')">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-success"
                                            onclick="show_ajax_modal('{{ route('leads.status-update', $lead->id) }}', 'Update Status')">
                                            <i class="ti ti-arrow-up"></i>
                                        </a>
                                        @if(!$lead->is_converted)
                                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-warning"
                                            onclick="show_ajax_modal('{{ route('leads.convert', $lead->id) }}', 'Convert Lead')">
                                            <i class="ti ti-refresh"></i>
                                        </a>
                                        @endif
                                        @if(!$isTelecaller || $isTeamLead)
                                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-danger"
                                            onclick="show_ajax_modal('{{ route('leads.delete', $lead->id) }}', 'Delete Lead')">
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
});

</script>
@endpush