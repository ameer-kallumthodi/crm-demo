@extends('layouts.mantis')

@section('title', 'Pullbacked Leads')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Pullbacked Leads</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Pullbacked Leads</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Filters</h5>
            </div>
            <div class="card-body">
                <form method="GET" id="pullbackedFilterForm" action="{{ route('admin.leads.pullbacked') }}">
                    <div class="row g-3">
                        <div class="col-lg-3">
                            <label class="form-label">Search</label>
                            <input type="text" name="search_key" class="form-control" placeholder="Name / Phone / Email"
                                value="{{ $filters['search_key'] }}">
                        </div>
                        <div class="col-lg-2">
                            <label class="form-label">Telecaller</label>
                            <select name="telecaller_id" class="form-control">
                                <option value="">All</option>
                                @foreach ($telecallers as $telecaller)
                                    <option value="{{ $telecaller->id }}" {{ $filters['telecaller_id'] == $telecaller->id ? 'selected' : '' }}>
                                        {{ $telecaller->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label class="form-label">Lead Status</label>
                            <select name="lead_status_id" class="form-control">
                                <option value="">All</option>
                                @foreach ($leadStatuses as $status)
                                    <option value="{{ $status->id }}" {{ $filters['lead_status_id'] == $status->id ? 'selected' : '' }}>
                                        {{ $status->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label class="form-label">Lead Source</label>
                            <select name="lead_source_id" class="form-control">
                                <option value="">All</option>
                                @foreach ($leadSources as $source)
                                    <option value="{{ $source->id }}" {{ $filters['lead_source_id'] == $source->id ? 'selected' : '' }}>
                                        {{ $source->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-1">
                            <label class="form-label">From</label>
                            <input type="date" name="from_date" class="form-control" value="{{ $filters['from_date'] }}">
                        </div>
                        <div class="col-lg-1">
                            <label class="form-label">To</label>
                            <input type="date" name="to_date" class="form-control" value="{{ $filters['to_date'] }}">
                        </div>
                        <div class="col-lg-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-filter"></i> Filter
                            </button>
                        </div>
                        <div class="col-lg-1 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-secondary w-100" id="pullbackedResetBtn">
                                <i class="ti ti-refresh"></i> Reset
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <h5 class="mb-0">Pullbacked Leads List</h5>
                    <span class="badge bg-danger" id="pullbackedTotal">Total: 0</span>
                </div>
                @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_general_manager())
                <button type="button" class="btn btn-success btn-sm"
                    onclick="show_large_modal('{{ route('admin.leads.pullbacked.assign') }}', 'Assign Pullbacked Leads')">
                    <i class="ti ti-user-check me-1"></i> Assign Telecaller
                </button>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="pullbackedLeadsTable" style="width:100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Lead Status</th>
                                <th>Lead Source</th>
                                <th>Telecaller</th>
                                <th>Course</th>
                                <th>Pullbacked On</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        const $pullbackedTotal = $('#pullbackedTotal');

        function getFilterParams() {
            return {
                search_key: $('input[name="search_key"]').val(),
                telecaller_id: $('select[name="telecaller_id"]').val(),
                lead_status_id: $('select[name="lead_status_id"]').val(),
                lead_source_id: $('select[name="lead_source_id"]').val(),
                from_date: $('input[name="from_date"]').val(),
                to_date: $('input[name="to_date"]').val(),
            };
        }

        function updateUrlWithFilters(reset = false) {
            const params = new URLSearchParams();
            if (!reset) {
                const filters = getFilterParams();
                Object.keys(filters).forEach(function (key) {
                    if (filters[key]) {
                        params.append(key, filters[key]);
                    }
                });
            }

            const newUrl = params.toString()
                ? `${window.location.pathname}?${params.toString()}`
                : window.location.pathname;
            window.history.replaceState({}, '', newUrl);
        }

        const tableSelector = '#pullbackedLeadsTable';

        if ($.fn.DataTable.isDataTable(tableSelector)) {
            $(tableSelector).DataTable().destroy();
            $(tableSelector + ' tbody').empty();
        }

        const pullbackedTable = $(tableSelector).DataTable({
            processing: true,
            serverSide: true,
            order: [[7, 'desc']],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            dom: "Bfrtip",
            buttons: ["csv", "excel", "print", "pdf"],
            stateSave: true,
            scrollX: true,
            ajax: {
                url: '{{ route('admin.leads.pullbacked.data') }}',
                data: function (d) {
                    return $.extend({}, d, getFilterParams());
                },
                error: function (xhr, error) {
                    console.error('Error loading pullbacked leads:', error);
                }
            },
            columns: [
                { data: 'index', name: 'index', orderable: false, searchable: false },
                { data: 'name', name: 'title' },
                { data: 'contact', name: 'phone', orderable: false, searchable: false },
                { data: 'status', name: 'lead_status_id', orderable: false, searchable: false },
                { data: 'source', name: 'lead_source_id' },
                { data: 'telecaller', name: 'telecaller_id' },
                { data: 'course', name: 'course_id' },
                { data: 'pullbacked_on', name: 'updated_at' },
                { data: 'remarks', name: 'remarks', orderable: false },
            ],
            columnDefs: [
                { targets: [1, 2, 3, 4, 5, 6, 7, 8], render: function (data) { return data; } }
            ],
            drawCallback: function (settings) {
                if (settings.json && typeof settings.json.recordsFiltered !== 'undefined') {
                    $pullbackedTotal.text('Total: ' + settings.json.recordsFiltered);
                }
            },
            language: {
                processing: "Loading...",
                emptyTable: "No pullbacked leads found.",
                zeroRecords: "No matching pullbacked leads found.",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                search: "Search:",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });

        $('#pullbackedFilterForm').on('submit', function (e) {
            e.preventDefault();
            updateUrlWithFilters();
            pullbackedTable.ajax.reload();
        });

        $('#pullbackedFilterForm .form-control').on('change', function () {
            updateUrlWithFilters();
            pullbackedTable.ajax.reload();
        });

        $('#pullbackedResetBtn').on('click', function () {
            $('#pullbackedFilterForm')[0].reset();
            updateUrlWithFilters(true);
            pullbackedTable.ajax.reload();
        });

        // Ensure URL reflects initial filter state on load
        updateUrlWithFilters();
    });
</script>
@endpush
