@extends('layouts.mantis')

@section('title', 'Telecaller Tracking Reports')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/telecaller-tracking.css') }}">
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<style>
    .dropdown-menu {
        z-index: 1050 !important;
    }
    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>
@endpush

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Detailed Telecaller Reports</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Telecaller Tracking</li>
                    <li class="breadcrumb-item">Reports</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        <div class="card telecaller-card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="avtar avtar-s rounded-circle bg-light-primary me-3 d-flex align-items-center justify-content-center">
                            <i class="ti ti-chart-bar f-20 text-primary"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Session Reports</h5>
                            <small class="text-muted">Detailed telecaller activity and performance reports</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2 no-print">
                        <a href="{{ route('admin.telecaller-tracking.dashboard') }}" class="btn btn-outline-primary btn-sm">
                            <i class="ti ti-arrow-left"></i> Dashboard
                        </a>
                        <button class="btn btn-primary btn-sm" onclick="printTable()">
                            <i class="ti ti-printer"></i> Print
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- [ Date Filter ] start -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body bg-light">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="ti ti-filter f-20 text-primary me-2"></i>
                                    <h6 class="mb-0 text-primary">Filter & Export Reports</h6>
                                </div>
                                <form method="GET" action="{{ route('admin.telecaller-tracking.reports') }}" id="dateFilterForm">
                                    <div class="row align-items-end">
                                        <div class="col-md-2">
                                            <label for="start_date" class="form-label fw-semibold">
                                                <i class="ti ti-calendar-event me-1"></i>From Date
                                            </label>
                                            <input type="date" class="form-control form-control-sm" name="start_date" 
                                                   value="{{ $startDate }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="end_date" class="form-label fw-semibold">
                                                <i class="ti ti-calendar-event me-1"></i>To Date
                                            </label>
                                            <input type="date" class="form-control form-control-sm" name="end_date" 
                                                   value="{{ $endDate }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="telecaller_id" class="form-label fw-semibold">
                                                <i class="ti ti-user me-1"></i>Telecaller
                                            </label>
                                            <select class="form-select form-select-sm" name="telecaller_id" id="telecaller_id">
                                                <option value="">All Telecallers</option>
                                                @foreach($telecallers as $telecaller)
                                                    <option value="{{ $telecaller->id }}" {{ $telecallerId == $telecaller->id ? 'selected' : '' }}>
                                                        {{ $telecaller->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 mt-3">
                                            <div class="d-flex gap-2 flex-wrap">
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="ti ti-search me-1"></i> Search
                                                </button>
                                                <a href="{{ route('admin.telecaller-tracking.reports') }}" class="btn btn-outline-secondary btn-sm">
                                                    <i class="ti ti-refresh me-1"></i> Reset
                                                </a>
                                                <div class="dropdown">
                                                    <button class="btn btn-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="ti ti-download me-1"></i> Export
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><a class="dropdown-item" href="{{ route('admin.telecaller-tracking.export.excel', request()->query()) }}">
                                                            <i class="ti ti-file-excel text-success me-2"></i>Excel
                                                        </a></li>
                                                        <li><a class="dropdown-item" href="{{ route('admin.telecaller-tracking.export.pdf', request()->query()) }}">
                                                            <i class="ti ti-file-pdf text-danger me-2"></i>PDF
                                                        </a></li>
                                                    </ul>
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

                <!-- Table -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover datatable mb-0 telecaller-table" id="sessionsTable">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">
                                    <i class="ti ti-hash f-16"></i>
                                </th>
                                <th class="text-center">
                                    <i class="ti ti-settings f-16"></i>
                                </th>
                                <th>
                                    <i class="ti ti-user me-1"></i>Telecaller
                                </th>
                                <th>
                                    <i class="ti ti-login me-1"></i>Login Time
                                </th>
                                <th>
                                    <i class="ti ti-logout me-1"></i>Logout Time
                                </th>
                                <th class="text-center">
                                    <i class="ti ti-clock me-1"></i>Duration
                                </th>
                                <th class="text-center">
                                    <i class="ti ti-activity me-1"></i>Active Time
                                </th>
                                <th class="text-center">
                                    <i class="ti ti-pause me-1"></i>Idle Time
                                </th>
                                <th class="text-center">
                                    <i class="ti ti-power me-1"></i>Logout Type
                                </th>
                                <th class="text-center">
                                    <i class="ti ti-world me-1"></i>IP Address
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sessions as $index => $session)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.telecaller-tracking.telecaller-report', $session->user_id) }}?start_date={{ $startDate }}&end_date={{ $endDate }}" 
                                           class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avtar avtar-s rounded-circle bg-light-primary me-2 d-flex align-items-center justify-content-center">
                                            <span class="f-16 fw-bold text-primary">{{ strtoupper(substr($session->user->name ?? 'U', 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $session->user->name ?? 'Unknown User' }}</h6>
                                            <small class="text-muted">{{ $session->user->email ?? 'No email' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $session->login_time->format('M d, Y g:i:s A') }}</td>
                                <td>
                                    @if($session->logout_time)
                                        {{ $session->logout_time->format('M d, Y g:i:s A') }}
                                    @else
                                        <span class="badge bg-success">Active</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $totalMinutes = $session->total_duration_minutes ?: $session->calculateTotalDuration() / 60;
                                        $hours = floor($totalMinutes / 60);
                                        $minutes = floor($totalMinutes % 60);
                                        $seconds = floor(($totalMinutes % 1) * 60);
                                    @endphp
                                    {{ $hours }}h {{ $minutes }}m {{ $seconds }}s
                                </td>
                                <td>
                                    @php
                                        $activeMinutes = $session->active_duration_minutes ?: $session->calculateActiveDuration() / 60;
                                        $hours = floor($activeMinutes / 60);
                                        $minutes = floor($activeMinutes % 60);
                                        $seconds = floor(($activeMinutes % 1) * 60);
                                    @endphp
                                    {{ $hours }}h {{ $minutes }}m {{ $seconds }}s
                                </td>
                                <td>
                                    @php
                                        $idleSeconds = $session->idleTimes()->sum('idle_duration_seconds');
                                        $hours = floor($idleSeconds / 3600);
                                        $minutes = floor(($idleSeconds % 3600) / 60);
                                        $seconds = $idleSeconds % 60;
                                    @endphp
                                    {{ $hours }}h {{ $minutes }}m {{ $seconds }}s
                                </td>
                                <td>
                                    <span class="badge {{ $session->logout_type == 'manual' ? 'bg-primary' : ($session->logout_type == 'auto' ? 'bg-warning' : 'bg-secondary') }}">
                                        {{ ucfirst($session->logout_type) }}
                                    </span>
                                </td>
                                <td>{{ $session->ip_address ?? 'N/A' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="ti ti-inbox f-48 mb-3 d-block"></i>
                                        No sessions found
                                    </div>
                                </td>
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
</div>
<!-- [ Main Content ] end -->
@endsection

@push('scripts')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable for telecaller reports
    if ($('#sessionsTable').length && !$.fn.DataTable.isDataTable('#sessionsTable')) {
        try {
            $('#sessionsTable').DataTable({
                "processing": true,
                "serverSide": false,
                "responsive": true,
                "pageLength": 25,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                "columnDefs": [
                    { "orderable": false, "targets": [0, 1] }, // Disable sorting on serial number and actions columns
                    { "searchable": false, "targets": [0, 1] }, // Disable searching on serial number and actions columns
                    { "className": "text-center", "targets": [0, 1, 5, 6, 7, 8, 9] } // Center align specific columns
                ],
                "language": {
                    "processing": "Loading sessions...",
                    "emptyTable": "No sessions found",
                    "zeroRecords": "No matching sessions found",
                    "search": "Search:",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    }
                }
            });
        } catch (error) {
            console.error('Error initializing DataTable:', error);
        }
    }
});

// Print function for the print button
function printTable() {
    // Hide elements that shouldn't be printed
    $('.no-print').hide();
    
    // Get the table content
    var table = $('#sessionsTable').clone();
    table.find('th:first, td:first').remove(); // Remove serial number column
    table.find('th:nth-child(2), td:nth-child(2)').remove(); // Remove actions column
    
    // Create a new window for printing
    var printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>Telecaller Sessions Report</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; font-weight: bold; }
                .header { text-align: center; margin-bottom: 20px; }
                .date-range { margin-bottom: 10px; color: #666; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>Telecaller Sessions Report</h2>
                <div class="date-range">
                    From: {{ $startDate }} To: {{ $endDate }}
                </div>
            </div>
            ${table[0].outerHTML}
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.print();
    printWindow.close();
    
    // Show hidden elements again
    $('.no-print').show();
}
</script>
@endpush
