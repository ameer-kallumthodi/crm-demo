@extends('layouts.mantis')

@section('title', 'Follow-up Leads')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Follow-up Leads Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('leads.index') }}">Leads</a></li>
                    <li class="breadcrumb-item">Follow-up Leads</li>
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
            <strong>Search Results:</strong> Showing follow-up leads matching "{{ request('search_key') }}"
        </div>
        <a href="{{ route('leads.followup') }}" class="btn btn-sm btn-outline-info">
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
                <form method="GET" action="{{ route('leads.followup') }}" id="dateFilterForm">
                    <!-- Desktop Filter Layout -->
                    <div class="d-none d-lg-block">
                        <div class="row align-items-end">
                            <div class="col-md-2">
                                <label for="search_key" class="form-label">Search</label>
                                <input type="text" class="form-control" name="search_key" id="search_key" 
                                       value="{{ request('search_key') }}" placeholder="Search leads...">
                            </div>
                            <div class="col-md-2">
                                <label for="lead_source_id" class="form-label">Lead Source</label>
                                <select class="form-select" name="lead_source_id" id="lead_source_id">
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
                                <select class="form-select" name="course_id" id="course_id">
                                    <option value="">All Courses</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                            {{ $course->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="country_id" class="form-label">Country</label>
                                <select class="form-select" name="country_id" id="country_id">
                                    <option value="">All Countries</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                                            {{ $country->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @if(!$isTelecaller || $isTeamLead)
                            <div class="col-md-2">
                                <label for="telecaller_id" class="form-label">Telecaller</label>
                                <select class="form-select" name="telecaller_id" id="telecaller_id">
                                    <option value="">All Telecallers</option>
                                    @foreach($telecallers as $telecaller)
                                        <option value="{{ $telecaller->id }}" {{ request('telecaller_id') == $telecaller->id ? 'selected' : '' }}>
                                            {{ $telecaller->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="ti ti-search"></i> Filter
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Filter Layout -->
                    <div class="d-lg-none">
                        <div class="row g-2">
                            <div class="col-12">
                                <label for="search_key_mobile" class="form-label f-12">Search</label>
                                <input type="text" class="form-control" name="search_key" id="search_key_mobile" 
                                       value="{{ request('search_key') }}" placeholder="Search leads...">
                            </div>
                            <div class="col-6">
                                <label for="lead_source_id_mobile" class="form-label f-12">Lead Source</label>
                                <select class="form-select" name="lead_source_id" id="lead_source_id_mobile">
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
                                <select class="form-select" name="course_id" id="course_id_mobile">
                                    <option value="">All Courses</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                            {{ $course->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="country_id_mobile" class="form-label f-12">Country</label>
                                <select class="form-select" name="country_id" id="country_id_mobile">
                                    <option value="">All Countries</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                                            {{ $country->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @if(!$isTelecaller || $isTeamLead)
                            <div class="col-6">
                                <label for="telecaller_id_mobile" class="form-label f-12">Telecaller</label>
                                <select class="form-select" name="telecaller_id" id="telecaller_id_mobile">
                                    <option value="">All Telecallers</option>
                                    @foreach($telecallers as $telecaller)
                                        <option value="{{ $telecaller->id }}" {{ request('telecaller_id') == $telecaller->id ? 'selected' : '' }}>
                                            {{ $telecaller->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="ti ti-search"></i> Filter
                                </button>
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
                    <h5 class="mb-0">Follow-up Leads ({{ $leads->count() }} leads)</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('leads.index') }}" class="btn btn-secondary btn-sm px-3">
                            <i class="ti ti-arrow-left"></i> All Leads
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($leads->count() > 0)
                    <!-- Desktop Table View -->
                    <div class="d-none d-lg-block">
                        <div class="table-responsive" style="overflow-x: auto;">
                            <table class="table table-hover data_table_basic" id="followupLeadsTable" style="min-width: 1700px;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Actions</th>
                                        <th>Name</th>
                                        <th>Profile</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Interest</th>
                                        <th>Rating</th>
                                        <th>Source</th>
                                        <th>Course</th>
                                        <th>Telecaller</th>
                                        <th>Place</th>
                                        <th>Followup Date</th>
                                        <th>Last Reason</th>
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
                                                @if(!$isTelecaller || $isTeamLead)
                                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-danger"
                                                    onclick="delete_modal('{{ route('leads.destroy', $lead->id) }}')"
                                                    title="Delete Lead">
                                                    <i class="ti ti-trash"></i>
                                                </a>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $lead->title }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avtar avtar-s rounded-circle bg-light-primary me-2 d-flex align-items-center justify-content-center">
                                                    <span class="f-16 fw-bold text-primary">{{ strtoupper(substr($lead->title, 0, 1)) }}</span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $lead->title }}</h6>
                                                    <small class="text-muted">{{ \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone) }}</small>
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
                                        <td>
                                            @if($lead->interest_status)
                                                <span class="badge bg-{{ $lead->interest_status_color }}">
                                                    {{ $lead->interest_status_label }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">Not Set</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($lead->rating)
                                                <span class="badge bg-primary">{{ $lead->rating }}/10</span>
                                            @else
                                                <span class="badge bg-secondary">Not Rated</span>
                                            @endif
                                        </td>
                                        <td>{{ $lead->leadSource->title ?? '-' }}</td>
                                        <td>{{ $lead->course->title ?? '-' }}</td>
                                        <td>{{ $lead->telecaller->name ?? 'Unassigned' }}</td>
                                        <td>{{ $lead->place ?? '-' }}</td>
                                        <td>
                                            @if($lead->followup_date)
                                                <span class="badge bg-warning">{{ $lead->followup_date->format('M d, Y') }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $lastActivityWithReason = $lead->leadActivities->first();
                                            @endphp
                                            @if($lastActivityWithReason)
                                                <span class="badge bg-info" title="{{ $lastActivityWithReason->reason }}">
                                                    {{ Str::limit($lastActivityWithReason->reason, 20) }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $lead->remarks ? Str::limit($lead->remarks, 30) : '-' }}</td>
                                        <td>{{ $lead->created_at->format('M d, Y') }}</td>
                                        <td>{{ $lead->created_at->format('H:i A') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="18" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="ti ti-inbox f-48 mb-3 d-block"></i>
                                                No follow-up leads found
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
                        <div class="row g-3">
                            @foreach($leads as $index => $lead)
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-3">
                                        <!-- Card Header with Actions -->
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avtar avtar-s rounded-circle bg-light-primary me-2 d-flex align-items-center justify-content-center">
                                                    <span class="f-16 fw-bold text-primary">{{ strtoupper(substr($lead->title, 0, 1)) }}</span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $lead->title }}</h6>
                                                    <small class="text-muted">{{ \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone) }}</small>
                                                </div>
                                            </div>
                                            <div class="d-flex gap-1">
                                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary" 
                                                   onclick="show_large_modal('{{ route('leads.ajax-show', $lead->id) }}', 'View Lead')"
                                                   title="View Lead">
                                                    <i class="ti ti-eye f-12"></i>
                                                </a>
                                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-secondary" 
                                                   onclick="show_ajax_modal('{{ route('leads.ajax-edit', $lead->id) }}', 'Edit Lead')"
                                                   title="Edit Lead">
                                                    <i class="ti ti-edit f-12"></i>
                                                </a>
                                                @if(!$isTelecaller || $isTeamLead)
                                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-danger" 
                                                   onclick="delete_modal('{{ route('leads.destroy', $lead->id) }}')"
                                                   title="Delete Lead">
                                                    <i class="ti ti-trash f-12"></i>
                                                </a>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Lead Information Grid -->
                                        <div class="row g-2 mb-3">
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
                                                    <i class="ti ti-flame f-12 text-muted me-1"></i>
                                                    @if($lead->interest_status)
                                                        <span class="badge bg-{{ $lead->interest_status_color }} f-10">
                                                            {{ $lead->interest_status_label }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary f-10">Not Set</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="d-flex align-items-center">
                                                    <i class="ti ti-star f-12 text-muted me-1"></i>
                                                    @if($lead->rating)
                                                        <span class="badge bg-primary f-10">{{ $lead->rating }}/10</span>
                                                    @else
                                                        <span class="badge bg-secondary f-10">Not Rated</span>
                                                    @endif
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
                                            @if($lead->followup_date)
                                            <div class="col-6">
                                                <div class="d-flex align-items-center">
                                                    <i class="ti ti-clock f-12 text-muted me-1"></i>
                                                    <span class="badge bg-warning f-10">{{ $lead->followup_date->format('M d') }}</span>
                                                </div>
                                            </div>
                                            @endif
                                            @php
                                                $lastActivityWithReason = $lead->leadActivities->first();
                                            @endphp
                                            @if($lastActivityWithReason)
                                            <div class="col-6">
                                                <div class="d-flex align-items-center">
                                                    <i class="ti ti-message f-12 text-muted me-1"></i>
                                                    <span class="badge bg-info f-10" title="{{ $lastActivityWithReason->reason }}">{{ Str::limit($lastActivityWithReason->reason, 15) }}</span>
                                                </div>
                                            </div>
                                            @endif
                                            @if($lead->remarks)
                                            <div class="col-12">
                                                <div class="d-flex align-items-start">
                                                    <i class="ti ti-note f-12 text-muted me-1 mt-1"></i>
                                                    <small class="text-muted f-11" title="{{ $lead->remarks }}">{{ Str::limit($lead->remarks, 50) }}</small>
                                                </div>
                                            </div>
                                            @endif
                                        </div>

                                        <!-- Action Buttons - Enhanced -->
                                        <div class="d-flex gap-1 flex-wrap justify-content-between">
                                            <!-- Left side - Status and Convert buttons -->
                                            <div class="d-flex gap-1">
                                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-warning" 
                                                   onclick="show_ajax_modal('{{ route('leads.status-update', $lead->id) }}', 'Update Status')"
                                                   title="Update Status">
                                                    <i class="ti ti-arrow-up f-12"></i>
                                                </a>
                                                @if(!$lead->is_converted)
                                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-success" 
                                                   onclick="show_ajax_modal('{{ route('leads.convert', $lead->id) }}', 'Convert Lead')"
                                                   title="Convert Lead">
                                                    <i class="ti ti-refresh f-12"></i>
                                                </a>
                                                @endif
                                                @if($lead->lead_status_id == 6)
                                                <a href="https://docs.google.com/forms/d/e/1FAIpQLSchtc8xlKUJehZNmzoKTkRvwLwk4-SGjzKSHM2UFToAhgdTlQ/viewform?usp=sf_link" 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   title="Demo Conduction Form">
                                                    <i class="ti ti-file-text f-12"></i>
                                                </a>
                                                @endif
                                            </div>
                                            
                                            <!-- Right side - Call and Logs buttons -->
                                            <div class="d-flex gap-1">
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
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="text-muted">
                            <i class="ti ti-inbox f-48 mb-3 d-block"></i>
                            <h5>No Follow-up Leads Found</h5>
                            <p class="mb-0">There are no leads with follow-up status at the moment.</p>
                        </div>
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
    // Handle search form submission
    $('.header-search form, .drp-search form').on('submit', function(e) {
        e.preventDefault();
        const searchValue = $(this).find('input[name="search_key"]').val().trim();
        if (searchValue) {
            window.location.href = '{{ route("leads.followup") }}?search_key=' + encodeURIComponent(searchValue);
        } else {
            window.location.href = '{{ route("leads.followup") }}';
        }
    });

    // Handle search input enter key
    $('.header-search input, .drp-search input').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            $(this).closest('form').submit();
        }
    });

    // Action buttons are now directly accessible without dropdown
    // All functionality is handled by onclick attributes on the buttons
});
</script>
@endpush

@push('styles')
<style>
/* Enhanced mobile responsiveness */
@media (max-width: 991.98px) {
    .card-body {
        padding: 1rem;
    }
    
    .btn-sm {
        min-width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-sm i {
        font-size: 12px;
    }
}

/* Fix DataTables info and pagination on mobile */
.dataTables_info,
.dataTables_paginate {
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .dataTables_info,
    .dataTables_paginate {
        font-size: 0.75rem;
    }
}

/* Additional responsive improvements */
@media (max-width: 1200px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    #followupLeadsTable th,
    #followupLeadsTable td {
        padding: 0.5rem 0.25rem;
    }
}

/* Enhanced action button styling */
.btn-sm {
    min-width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-sm i {
    font-size: 12px;
}

/* Mobile action buttons layout */
@media (max-width: 991.98px) {
    .d-flex.gap-1 {
        gap: 0.25rem !important;
    }
    
    .btn-sm {
        min-width: 28px;
        height: 28px;
        padding: 0.25rem;
    }
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.8rem;
    }
    
    #followupLeadsTable th,
    #followupLeadsTable td {
        padding: 0.375rem 0.125rem;
    }
}
</style>
@endpush
