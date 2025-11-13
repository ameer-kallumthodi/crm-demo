@extends('layouts.mantis')

@section('title', 'Marketing Leads')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Marketing Leads</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Marketing Leads</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">D2D Marketing Leads</h5>
                    <div class="d-flex gap-2">
                        @if(isset($isAdminOrManager) && $isAdminOrManager)
                        <button type="button" class="btn btn-success btn-sm px-3" onclick="show_large_modal('{{ route('admin.marketing.bulk-assign.ajax') }}', 'Bulk Assign to Telecaller')">
                            <i class="ti ti-users"></i> Bulk Assign
                        </button>
                        @endif
                        <a href="{{ route('admin.marketing.d2d-form') }}" class="btn btn-primary btn-sm px-3">
                            <i class="ti ti-plus"></i> Add New Lead
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <div class="row g-3 mb-4" id="filterForm">
                    @if(!$isMarketing)
                    <div class="col-md-3">
                        <label class="form-label">BDE</label>
                        <select name="bde_id" id="filter_bde_id" class="form-select">
                            <option value="">All BDEs</option>
                            @foreach($marketingUsers as $user)
                                <option value="{{ $user->id }}" {{ request('bde_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-3">
                        <label class="form-label">Date From</label>
                        <input type="date" name="date_from" id="filter_date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date To</label>
                        <input type="date" name="date_to" id="filter_date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Assignment Status</label>
                        <select name="is_assigned" id="filter_is_assigned" class="form-select">
                            <option value="">All</option>
                            <option value="1" {{ request('is_assigned') == '1' ? 'selected' : '' }}>Assigned</option>
                            <option value="0" {{ request('is_assigned') == '0' ? 'selected' : '' }}>Not Assigned</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary btn-sm" id="applyFilters">
                            <i class="ti ti-filter"></i> Filter
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" id="clearFilters">
                            <i class="ti ti-x"></i> Clear
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-hover" id="marketingLeadsTable" style="min-width: 2000px;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date of Visit</th>
                                <th>BDE Name</th>
                                <th>Lead Name</th>
                                <th>Phone</th>
                                <th>WhatsApp</th>
                                <th>Address</th>
                                <th>Location</th>
                                <th>House Number</th>
                                <th>Lead Type</th>
                                <th>Interested Courses</th>
                                <th>Remarks</th>
                                <th>Telecaller Remarks</th>
                                <th>Lead Status</th>
                                <th>Telecaller Name</th>
                                <th>Assignment Status</th>
                                <th>Assigned At</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

@endsection

@php
// Build columns array for DataTables
$isAdminOrManager = isset($isAdminOrManager) ? $isAdminOrManager : false;

$columns = [
    ['data' => 'index', 'name' => 'index', 'orderable' => false, 'searchable' => false],
    ['data' => 'date_of_visit', 'name' => 'date_of_visit'],
    ['data' => 'bde_name', 'name' => 'bde_name'],
    ['data' => 'lead_name', 'name' => 'lead_name'],
    ['data' => 'phone', 'name' => 'phone'],
    ['data' => 'whatsapp', 'name' => 'whatsapp'],
    ['data' => 'address', 'name' => 'address'],
    ['data' => 'location', 'name' => 'location'],
    ['data' => 'house_number', 'name' => 'house_number'],
    ['data' => 'lead_type', 'name' => 'lead_type', 'orderable' => false, 'searchable' => false],
    ['data' => 'interested_courses', 'name' => 'interested_courses', 'orderable' => false, 'searchable' => false],
    ['data' => 'remarks', 'name' => 'remarks'],
    ['data' => 'telecaller_remarks', 'name' => 'telecaller_remarks', 'orderable' => false, 'searchable' => false],
    ['data' => 'lead_status', 'name' => 'lead_status', 'orderable' => false, 'searchable' => false],
    ['data' => 'telecaller_name', 'name' => 'telecaller_name', 'orderable' => false, 'searchable' => false],
    ['data' => 'assignment_status', 'name' => 'assignment_status', 'orderable' => false, 'searchable' => false],
    ['data' => 'assigned_at', 'name' => 'assigned_at'],
    ['data' => 'created_at', 'name' => 'created_at'],
    ['data' => 'actions', 'name' => 'actions', 'orderable' => false, 'searchable' => false],
];
@endphp

@push('scripts')
<script>
// Store table instance globally
var marketingLeadsTable = null;

$(document).ready(function() {
    // Check for success message in URL parameter
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success')) {
        var successMessage = decodeURIComponent(urlParams.get('success'));
        if (typeof toast_success === 'function') {
            toast_success(successMessage);
        } else if (typeof alert_modal_success === 'function') {
            alert_modal_success(successMessage);
        }
        // Clean up URL by removing the success parameter
        urlParams.delete('success');
        var newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
        window.history.replaceState({}, '', newUrl);
    }
    
    // Prevent global initialization for this table - remove classes that trigger auto-init
    $('#marketingLeadsTable').removeClass('data_table_basic datatable');
    
    // Get filter values from form
    function getFilterParams() {
        var params = {};
        @if(!$isMarketing)
        if ($('#filter_bde_id').val()) {
            params.bde_id = $('#filter_bde_id').val();
        }
        @endif
        if ($('#filter_date_from').val()) {
            params.date_from = $('#filter_date_from').val();
        }
        if ($('#filter_date_to').val()) {
            params.date_to = $('#filter_date_to').val();
        }
        if ($('#filter_is_assigned').val() !== '') {
            params.is_assigned = $('#filter_is_assigned').val();
        }
        return params;
    }
    
    // Use setTimeout to defer initialization and allow global scripts to finish first
    setTimeout(function() {
        // Destroy existing DataTable instance if any (from global init or previous load)
        if ($.fn.DataTable.isDataTable('#marketingLeadsTable')) {
            $('#marketingLeadsTable').DataTable().destroy();
            $('#marketingLeadsTable').empty();
        }
        
        // Rebuild thead structure if it was removed
        if ($('#marketingLeadsTable thead').length === 0) {
            $('#marketingLeadsTable').prepend('<thead><tr>' +
                '<th>#</th><th>Date of Visit</th><th>BDE Name</th><th>Lead Name</th>' +
                '<th>Phone</th><th>WhatsApp</th><th>Address</th><th>Location</th>' +
                '<th>House Number</th><th>Lead Type</th><th>Interested Courses</th>' +
                '<th>Remarks</th><th>Telecaller Remarks</th><th>Lead Status</th>' +
                '<th>Telecaller Name</th><th>Assignment Status</th><th>Assigned At</th>' +
                '<th>Created At</th><th>Actions</th></tr></thead>');
        }
        
        // Initialize DataTables with AJAX
        marketingLeadsTable = $('#marketingLeadsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('admin.marketing.marketing-leads.data') }}',
            type: 'GET',
            data: function(d) {
                // Merge DataTables parameters with filter parameters
                var filters = getFilterParams();
                $.extend(d, filters);
            },
            error: function(xhr, error, thrown) {
                console.error('DataTables error:', error);
                alert_modal_error('Error loading marketing leads data. Please try again.');
            }
        },
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        order: [[17, 'desc']], // Sort by created_at (column 17)
        dom: "Bfrtip",
        buttons: ["csv", "excel", "print", "pdf"],
        stateSave: true,
        scrollCollapse: true,
        autoWidth: false,
        scrollX: true,
        searchHighlight: false,
        columns: @json($columns),
        drawCallback: function(settings) {
            // Re-initialize tooltips or other plugins if needed
        }
    });
    
    // Apply filters button
    $('#applyFilters').on('click', function() {
        if (marketingLeadsTable) {
            marketingLeadsTable.ajax.reload();
        }
    });
    
    // Clear filters button
    $('#clearFilters').on('click', function() {
        @if(!$isMarketing)
        $('#filter_bde_id').val('');
        @endif
        $('#filter_date_from').val('');
        $('#filter_date_to').val('');
        $('#filter_is_assigned').val('');
        if (marketingLeadsTable) {
            marketingLeadsTable.ajax.reload();
        }
    });
    
    // Auto-reload on filter change (optional)
    $('#filterForm select, #filterForm input').on('change', function() {
        // Uncomment below if you want auto-reload on change
        // if (marketingLeadsTable) {
        //     marketingLeadsTable.ajax.reload();
        // }
    });
    }, 300); // Delay initialization by 300ms to ensure global scripts finish
});
</script>
@endpush
