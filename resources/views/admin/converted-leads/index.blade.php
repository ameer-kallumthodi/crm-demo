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

<!-- [ Course Filter Buttons ] start -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Filter by Course</h6>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.converted-leads.index') }}" class="btn btn-outline-primary {{ !request('course_id') ? 'active' : '' }}">
                        <i class="ti ti-list"></i> All Converted Leads
                    </a>
                    <a href="{{ route('admin.nios-converted-leads.index') }}" class="btn btn-outline-success">
                        <i class="ti ti-school"></i> NIOS Converted Leads
                    </a>
                    <a href="{{ route('admin.bosse-converted-leads.index') }}" class="btn btn-outline-warning">
                        <i class="ti ti-graduation-cap"></i> BOSSE Converted Leads
                    </a>
                    <a href="{{ route('admin.gmvss-converted-leads.index') }}" class="btn btn-outline-info">
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
                            <label for="batch_id" class="form-label">Batch</label>
                            <select class="form-select" id="batch_id" name="batch_id" data-selected="{{ request('batch_id') }}">
                                <option value="">All Batches</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="admission_batch_id" class="form-label">Admission Batch</label>
                            <select class="form-select" id="admission_batch_id" name="admission_batch_id" data-selected="{{ request('admission_batch_id') }}">
                                <option value="">All Admission Batches</option>
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
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All</option>
                                <option value="Paid" {{ request('status')==='Paid' ? 'selected' : '' }}>Paid</option>
                                <option value="Admission cancel" {{ request('status')==='Admission cancel' ? 'selected' : '' }}>Admission cancel</option>
                                <option value="Active" {{ request('status')==='Active' ? 'selected' : '' }}>Active</option>
                                <option value="Inactive" {{ request('status')==='Inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="reg_fee" class="form-label">REG. FEE</label>
                            <select class="form-select" id="reg_fee" name="reg_fee">
                                <option value="">All</option>
                                <option value="Received" {{ request('reg_fee')==='Received' ? 'selected' : '' }}>Received</option>
                                <option value="Not Received" {{ request('reg_fee')==='Not Received' ? 'selected' : '' }}>Not Received</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="exam_fee" class="form-label">EXAM FEE</label>
                            <select class="form-select" id="exam_fee" name="exam_fee">
                                <option value="">All</option>
                                <option value="Pending" {{ request('exam_fee')==='Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Not Paid" {{ request('exam_fee')==='Not Paid' ? 'selected' : '' }}>Not Paid</option>
                                <option value="Paid" {{ request('exam_fee')==='Paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="id_card" class="form-label">ID CARD</label>
                            <select class="form-select" id="id_card" name="id_card">
                                <option value="">All</option>
                                <option value="processing" {{ request('id_card')==='processing' ? 'selected' : '' }}>processing</option>
                                <option value="download" {{ request('id_card')==='download' ? 'selected' : '' }}>download</option>
                                <option value="not downloaded" {{ request('id_card')==='not downloaded' ? 'selected' : '' }}>not downloaded</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="tma" class="form-label">TMA</label>
                            <select class="form-select" id="tma" name="tma">
                                <option value="">All</option>
                                <option value="Uploaded" {{ request('tma')==='Uploaded' ? 'selected' : '' }}>Uploaded</option>
                                <option value="Not Upload" {{ request('tma')==='Not Upload' ? 'selected' : '' }}>Not Upload</option>
                            </select>
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
                                    <th>Register Number</th>
                                    <th>Date</th>
                                    <th>DOB</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Course</th>
                                    <th>Batch</th>
                                    <th>Mail</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($convertedLeads as $index => $convertedLead)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @if($convertedLead->register_number)
                                        <span class="badge bg-success">{{ $convertedLead->register_number }}</span>
                                        @else
                                        <span class="text-muted">Not Set</span>
                                        @endif
                                    </td>
                                    <td>{{ $convertedLead->created_at->format('M d, Y') }}</td>
                                    <td>
                                        @php
                                            $dobDisplay = $convertedLead->dob ? (strtotime($convertedLead->dob) ? date('d-m-Y', strtotime($convertedLead->dob)) : $convertedLead->dob) : 'N/A';
                                        @endphp
                                        {{ $dobDisplay }}
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
                                    <td>{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</td>
                                    <td>{{ $convertedLead->course ? $convertedLead->course->title : 'N/A' }}</td>
                                    <td>{{ $convertedLead->batch ? $convertedLead->batch->title : 'N/A' }}</td>
                                    <td>{{ $convertedLead->email ?? 'N/A' }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.converted-leads.show', $convertedLead->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.invoices.index', $convertedLead->id) }}" class="btn btn-sm btn-success" title="View Invoice">
                                                <i class="ti ti-receipt"></i>
                                            </a>
                                            @if($convertedLead->course_id == 1)
                                                <a href="{{ route('admin.nios-converted-leads.index') }}" class="btn btn-sm btn-outline-success" title="View NIOS Details">
                                                    <i class="ti ti-school"></i>
                                                </a>
                                            @elseif($convertedLead->course_id == 2)
                                                <a href="{{ route('admin.bosse-converted-leads.index') }}" class="btn btn-sm btn-outline-warning" title="View BOSSE Details">
                                                    <i class="ti ti-graduation-cap"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">No converted leads found</td>
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
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.invoices.index', $convertedLead->id) }}">
                                                <i class="ti ti-receipt me-2"></i>View Invoice
                                            </a>
                                        </li>
                                        @if($convertedLead->course_id == 1)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.nios-converted-leads.index') }}">
                                                <i class="ti ti-school me-2"></i>View NIOS Details
                                            </a>
                                        </li>
                                        @elseif($convertedLead->course_id == 2)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.bosse-converted-leads.index') }}">
                                                <i class="ti ti-graduation-cap me-2"></i>View BOSSE Details
                                            </a>
                                        </li>
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
                                    <small class="text-muted d-block">Batch</small>
                                    <span class="fw-medium">{{ $convertedLead->batch ? $convertedLead->batch->title : 'N/A' }}</span>
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
                                <a href="{{ route('admin.invoices.index', $convertedLead->id) }}"
                                    class="btn btn-sm btn-success">
                                    <i class="ti ti-receipt me-1"></i>View Invoice
                                </a>
                                @if($convertedLead->course_id == 1)
                                <a href="{{ route('admin.nios-converted-leads.index') }}"
                                    class="btn btn-sm btn-outline-success">
                                    <i class="ti ti-school me-1"></i>NIOS Details
                                </a>
                                @elseif($convertedLead->course_id == 2)
                                <a href="{{ route('admin.bosse-converted-leads.index') }}"
                                    class="btn btn-sm btn-outline-warning">
                                    <i class="ti ti-graduation-cap me-1"></i>BOSSE Details
                                </a>
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
    left: -8px; /* allow a bit wider than the cell */
    z-index: 10;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
    min-width: 320px; /* wider edit area */
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

/* Increase table column widths for readability */
#convertedLeadsTable thead th,
#convertedLeadsTable tbody td {
    white-space: nowrap;
}

/* Sticky header */
#convertedLeadsTable thead th {
    position: sticky;
    top: 0;
    z-index: 5;
    background: #fff;
    box-shadow: inset 0 -1px 0 #e9ecef;
}

/* Hover state */
#convertedLeadsTable tbody tr:hover {
    background: #fafbff;
}

/* Truncate long text */
#convertedLeadsTable td .display-value {
    display: inline-block;
    max-width: 220px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    vertical-align: middle;
}

/* Action buttons spacing */
#convertedLeadsTable .btn-group .btn { margin-right: 4px; }
#convertedLeadsTable .btn-group .btn:last-child { margin-right: 0; }

/* Filter form separation */
.card .card-body #filterForm {
    border-bottom: 1px dashed #e9ecef;
    padding-bottom: 8px;
}

/* Column-specific min-widths by position */
#convertedLeadsTable thead th:nth-child(1),
#convertedLeadsTable tbody td:nth-child(1) { min-width: 60px; }
#convertedLeadsTable thead th:nth-child(2),
#convertedLeadsTable tbody td:nth-child(2) { min-width: 140px; }
#convertedLeadsTable thead th:nth-child(3),
#convertedLeadsTable tbody td:nth-child(3) { min-width: 120px; }
#convertedLeadsTable thead th:nth-child(4),
#convertedLeadsTable tbody td:nth-child(4) { min-width: 120px; }
#convertedLeadsTable thead th:nth-child(5),
#convertedLeadsTable tbody td:nth-child(5) { min-width: 220px; }
#convertedLeadsTable thead th:nth-child(6),
#convertedLeadsTable tbody td:nth-child(6) { min-width: 140px; }
#convertedLeadsTable thead th:nth-child(7),
#convertedLeadsTable tbody td:nth-child(7) { min-width: 180px; }
#convertedLeadsTable thead th:nth-child(8),
#convertedLeadsTable tbody td:nth-child(8) { min-width: 180px; }
#convertedLeadsTable thead th:nth-child(9),
#convertedLeadsTable tbody td:nth-child(9) { min-width: 200px; }
#convertedLeadsTable thead th:nth-child(10),
#convertedLeadsTable tbody td:nth-child(10) { min-width: 200px; }
</style>
@endpush

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

        // Dependent filters: load batches by course
        function loadBatchesByCourse(courseId, selectedId) {
            const $batch = $('#batch_id');
            $batch.html('<option value="">Loading...</option>');
            if (!courseId) {
                $batch.html('<option value="">All Batches</option>');
                return;
            }
            $.get(`/api/batches/by-course/${courseId}`).done(function(response) {
                let opts = '<option value="">All Batches</option>';
                if (response.success && response.batches) {
                    response.batches.forEach(function(b) {
                        const sel = String(selectedId) === String(b.id) ? 'selected' : '';
                        opts += `<option value="${b.id}" ${sel}>${b.title}</option>`;
                    });
                }
                $batch.html(opts);
            }).fail(function() {
                $batch.html('<option value="">All Batches</option>');
            });
        }

        function loadAdmissionBatchesByBatch(batchId, selectedId) {
            const $admission = $('#admission_batch_id');
            $admission.html('<option value="">Loading...</option>');
            if (!batchId) {
                $admission.html('<option value="">All Admission Batches</option>');
                return;
            }
            $.get(`/api/admission-batches/by-batch/${batchId}`).done(function(list) {
                let opts = '<option value="">All Admission Batches</option>';
                list.forEach(function(i) {
                    const sel = String(selectedId) === String(i.id) ? 'selected' : '';
                    opts += `<option value="${i.id}" ${sel}>${i.title}</option>`;
                });
                $admission.html(opts);
            }).fail(function() {
                $admission.html('<option value="">All Admission Batches</option>');
            });
        }

        // Initialize dependent dropdowns on load
        const initialCourse = $('#course_id').val();
        loadBatchesByCourse(initialCourse, $('#batch_id').data('selected'));
        loadAdmissionBatchesByBatch($('#batch_id').data('selected'), $('#admission_batch_id').data('selected'));

        // On course change → reload batches, clear admission batch
        $('#course_id').on('change', function() {
            const cid = $(this).val();
            $('#admission_batch_id').html('<option value="">All Admission Batches</option>');
            loadBatchesByCourse(cid, '');
        });

        // On batch change → reload admission batches
        $('#batch_id').on('change', function() {
            const bid = $(this).val();
            loadAdmissionBatchesByBatch(bid, '');
        });
    });
</script>
@endpush