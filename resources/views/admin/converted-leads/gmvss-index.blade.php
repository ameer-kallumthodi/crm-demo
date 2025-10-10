@extends('layouts.mantis')

@section('title', 'GMVSS Converted Leads')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">GMVSS Converted Leads Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.converted-leads.index') }}">Converted Leads</a></li>
                    <li class="breadcrumb-item">GMVSS</li>
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
                        <i class="ti ti-graduation-cap"></i> BOSSE Converted Leads
                    </a>
                    <a href="{{ route('admin.gmvss-converted-leads.index') }}" class="btn btn-info active">
                        <i class="ti ti-certificate"></i> GMVSS Converted Leads
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Course Filter Buttons ] end -->

<!-- [ Filter Section ] start -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.gmvss-converted-leads.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Name, Phone, Email, Reg No">
                        </div>
                        <div class="col-md-3">
                            <label for="batch_id" class="form-label">Batch</label>
                            <select class="form-select" id="batch_id" name="batch_id">
                                <option value="">All Batches</option>
                                @foreach($batches as $batch)
                                    <option value="{{ $batch->id }}" {{ request('batch_id') == $batch->id ? 'selected' : '' }}>{{ $batch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="Paid" {{ request('status')=='Paid' ? 'selected' : '' }}>Paid</option>
                                <option value="Admission cancel" {{ request('status')=='Admission cancel' ? 'selected' : '' }}>Admission cancel</option>
                                <option value="Active" {{ request('status')=='Active' ? 'selected' : '' }}>Active</option>
                                <option value="Inactive" {{ request('status')=='Inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">Date From</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to" class="form-label">Date To</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="certificate_status" class="form-label">Certificate Status</label>
                            <select class="form-select" id="certificate_status" name="certificate_status">
                                <option value="">All Certificate Status</option>
                                <option value="In Progress" {{ request('certificate_status')=='In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Online Result Not Arrived" {{ request('certificate_status')=='Online Result Not Arrived' ? 'selected' : '' }}>Online Result Not Arrived</option>
                                <option value="One Result Arrived" {{ request('certificate_status')=='One Result Arrived' ? 'selected' : '' }}>One Result Arrived</option>
                                <option value="Certificate Arrived" {{ request('certificate_status')=='Certificate Arrived' ? 'selected' : '' }}>Certificate Arrived</option>
                                <option value="Not Received" {{ request('certificate_status')=='Not Received' ? 'selected' : '' }}>Not Received</option>
                                <option value="No Admission" {{ request('certificate_status')=='No Admission' ? 'selected' : '' }}>No Admission</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="ti ti-search"></i> Filter
                            </button>
                            <a href="{{ route('admin.gmvss-converted-leads.index') }}" class="btn btn-outline-secondary">
                                <i class="ti ti-refresh"></i> Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- [ Filter Section ] end -->

<!-- [ Table Section ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="gmvssConvertedLeadsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Registration Number</th>
                                <th>Converted Date</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Mail</th>
                                <th>Course</th>
                                <th>Session</th>
                                <th>Enrollment Number</th>
                                <th>Registration Link</th>
                                <th>Certificate</th>
                                <th>Certificate Received Date</th>
                                <th>Certificate Issued Date</th>
                                <th>Remarks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($convertedLeads as $index => $convertedLead)
                                <tr>
                                    <td>{{ $convertedLeads->firstItem() + $index }}</td>
                                    <td>
                                        <div class="inline-edit" data-field="registration_number" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->registration_number }}">
                                            <span class="display-value">{{ $convertedLead->studentDetails?->registration_number ?? 'N/A' }}</span>
                                            <div class="edit-form" style="display: none;">
                                                <input type="text" class="form-control form-control-sm" value="{{ $convertedLead->studentDetails?->registration_number }}">
                                                <div class="btn-group btn-group-sm mt-1">
                                                    <button type="button" class="btn btn-success btn-sm save-edit">
                                                        <i class="ti ti-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-secondary btn-sm cancel-edit">
                                                        <i class="ti ti-x"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $convertedLead->studentDetails?->converted_date ? \Carbon\Carbon::parse($convertedLead->studentDetails->converted_date)->format('d/m/Y') : 'N/A' }}</td>
                                    <td>{{ $convertedLead->name }}</td>
                                    <td>
                                        <div class="inline-edit" data-field="phone" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->phone }}">
                                            <span class="display-value">{{ $convertedLead->phone ?? 'N/A' }}</span>
                                            <div class="edit-form" style="display: none;">
                                                <input type="text" class="form-control form-control-sm" value="{{ $convertedLead->phone }}">
                                                <div class="btn-group btn-group-sm mt-1">
                                                    <button type="button" class="btn btn-success btn-sm save-edit">
                                                        <i class="ti ti-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-secondary btn-sm cancel-edit">
                                                        <i class="ti ti-x"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $convertedLead->email }}</td>
                                    <td>{{ $convertedLead->course?->name }}</td>
                                    <td>{{ $convertedLead->leadDetail?->passed_year ?? 'N/A' }}</td>
                                    <td>
                                        <div class="inline-edit" data-field="enrollment_number" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->enrollment_number }}">
                                            <span class="display-value">{{ $convertedLead->studentDetails?->enrollment_number ?? 'N/A' }}</span>
                                            <div class="edit-form" style="display: none;">
                                                <input type="text" class="form-control form-control-sm" value="{{ $convertedLead->studentDetails?->enrollment_number }}">
                                                <div class="btn-group btn-group-sm mt-1">
                                                    <button type="button" class="btn btn-success btn-sm save-edit">
                                                        <i class="ti ti-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-secondary btn-sm cancel-edit">
                                                        <i class="ti ti-x"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="registration_link_id" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->registration_link_id }}">
                                            <span class="display-value">{{ $convertedLead->studentDetails?->registrationLink?->title ?? 'N/A' }}</span>
                                            <div class="edit-form" style="display: none;">
                                                <select class="form-select form-select-sm">
                                                    <option value="">Select Registration Link</option>
                                                    @foreach(\App\Models\RegistrationLink::all() as $link)
                                                        <option value="{{ $link->id }}" {{ $convertedLead->studentDetails?->registration_link_id == $link->id ? 'selected' : '' }}>{{ $link->title }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="btn-group btn-group-sm mt-1">
                                                    <button type="button" class="btn btn-success btn-sm save-edit">
                                                        <i class="ti ti-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-secondary btn-sm cancel-edit">
                                                        <i class="ti ti-x"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="certificate_status" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->certificate_status }}">
                                            <span class="display-value">{{ $convertedLead->studentDetails?->certificate_status ?? 'N/A' }}</span>
                                            <div class="edit-form" style="display: none;">
                                                <select class="form-select form-select-sm">
                                                    <option value="">Select Certificate Status</option>
                                                    <option value="In Progress" {{ $convertedLead->studentDetails?->certificate_status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                                    <option value="Online Result Not Arrived" {{ $convertedLead->studentDetails?->certificate_status == 'Online Result Not Arrived' ? 'selected' : '' }}>Online Result Not Arrived</option>
                                                    <option value="One Result Arrived" {{ $convertedLead->studentDetails?->certificate_status == 'One Result Arrived' ? 'selected' : '' }}>One Result Arrived</option>
                                                    <option value="Certificate Arrived" {{ $convertedLead->studentDetails?->certificate_status == 'Certificate Arrived' ? 'selected' : '' }}>Certificate Arrived</option>
                                                    <option value="Not Received" {{ $convertedLead->studentDetails?->certificate_status == 'Not Received' ? 'selected' : '' }}>Not Received</option>
                                                    <option value="No Admission" {{ $convertedLead->studentDetails?->certificate_status == 'No Admission' ? 'selected' : '' }}>No Admission</option>
                                                </select>
                                                <div class="btn-group btn-group-sm mt-1">
                                                    <button type="button" class="btn btn-success btn-sm save-edit">
                                                        <i class="ti ti-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-secondary btn-sm cancel-edit">
                                                        <i class="ti ti-x"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="certificate_received_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->certificate_received_date }}">
                                            <span class="display-value">{{ $convertedLead->studentDetails?->certificate_received_date ? \Carbon\Carbon::parse($convertedLead->studentDetails->certificate_received_date)->format('d/m/Y') : 'N/A' }}</span>
                                            <div class="edit-form" style="display: none;">
                                                <input type="date" class="form-control form-control-sm" value="{{ $convertedLead->studentDetails?->certificate_received_date }}">
                                                <div class="btn-group btn-group-sm mt-1">
                                                    <button type="button" class="btn btn-success btn-sm save-edit">
                                                        <i class="ti ti-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-secondary btn-sm cancel-edit">
                                                        <i class="ti ti-x"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="certificate_issued_date" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->certificate_issued_date }}">
                                            <span class="display-value">{{ $convertedLead->studentDetails?->certificate_issued_date ? \Carbon\Carbon::parse($convertedLead->studentDetails->certificate_issued_date)->format('d/m/Y') : 'N/A' }}</span>
                                            <div class="edit-form" style="display: none;">
                                                <input type="date" class="form-control form-control-sm" value="{{ $convertedLead->studentDetails?->certificate_issued_date }}">
                                                <div class="btn-group btn-group-sm mt-1">
                                                    <button type="button" class="btn btn-success btn-sm save-edit">
                                                        <i class="ti ti-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-secondary btn-sm cancel-edit">
                                                        <i class="ti ti-x"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="inline-edit" data-field="remarks" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->studentDetails?->remarks }}">
                                            <span class="display-value">{{ $convertedLead->studentDetails?->remarks ?? 'N/A' }}</span>
                                            <div class="edit-form" style="display: none;">
                                                <input type="text" class="form-control form-control-sm" value="{{ $convertedLead->studentDetails?->remarks }}">
                                                <div class="btn-group btn-group-sm mt-1">
                                                    <button type="button" class="btn btn-success btn-sm save-edit">
                                                        <i class="ti ti-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-secondary btn-sm cancel-edit">
                                                        <i class="ti ti-x"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.converted-leads.show', $convertedLead->id) }}" class="btn btn-info btn-sm" title="View Details">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.invoices.index', ['converted_lead_id' => $convertedLead->id]) }}" class="btn btn-warning btn-sm" title="View Invoices">
                                                <i class="ti ti-receipt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="15" class="text-center">No GMVSS converted leads found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $convertedLeads->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Table Section ] end -->

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Inline editing functionality
    $('.inline-edit').on('click', '.display-value', function() {
        $(this).hide();
        $(this).siblings('.edit-form').show();
    });

    $('.inline-edit').on('click', '.cancel-edit', function() {
        $(this).closest('.edit-form').hide();
        $(this).closest('.inline-edit').find('.display-value').show();
    });

    $('.inline-edit').on('click', '.save-edit', function() {
        const $editForm = $(this).closest('.edit-form');
        const $inlineEdit = $(this).closest('.inline-edit');
        const field = $inlineEdit.data('field');
        const id = $inlineEdit.data('id');
        const value = $editForm.find('input, select').val();
        const currentValue = $inlineEdit.data('current');

        if (value === currentValue) {
            $editForm.hide();
            $inlineEdit.find('.display-value').show();
            return;
        }

        // Show loading state
        $(this).prop('disabled', true).html('<i class="ti ti-loader"></i>');

        $.ajax({
            url: '{{ route("admin.converted-leads.inline-update", ":id") }}'.replace(':id', id),
            method: 'POST',
            data: {
                field: field,
                value: value,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $inlineEdit.data('current', value);
                    $inlineEdit.find('.display-value').text(response.display_value || value);
                    $editForm.hide();
                    $inlineEdit.find('.display-value').show();
                    
                    // Show success message
                    toastr.success('Updated successfully');
                } else {
                    toastr.error(response.message || 'Update failed');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response?.message || 'Update failed');
            },
            complete: function() {
                // Reset button state
                $editForm.find('.save-edit').prop('disabled', false).html('<i class="ti ti-check"></i>');
            }
        });
    });
});
</script>
@endsection
