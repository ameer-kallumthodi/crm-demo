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

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">All Leads</h5>
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLeadModal">
                            <i class="ti ti-plus"></i> Add Lead
                        </button>
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#bulkUploadModal">
                            <i class="ti ti-upload"></i> Bulk Upload
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter">
                            <option value="">All Status</option>
                            @foreach($leadStatuses as $status)
                                <option value="{{ $status->id }}">{{ $status->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="sourceFilter">
                            <option value="">All Sources</option>
                            @foreach($leadSources as $source)
                                <option value="{{ $source->id }}">{{ $source->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" id="searchInput" placeholder="Search leads...">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-outline-secondary" onclick="clearFilters()">Clear Filters</button>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-hover" id="leadsTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Source</th>
                                <th>Telecaller</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leads as $lead)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avtar avtar-s rounded-circle bg-light-primary me-2">
                                            <i class="ti ti-user f-16"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $lead->title }}</h6>
                                            <small class="text-muted">{{ $lead->place }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $lead->phone }}</td>
                                <td>{{ $lead->email ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-light-{{ $lead->leadStatus->id == 4 ? 'success' : ($lead->leadStatus->id == 7 ? 'danger' : 'warning') }} text-{{ $lead->leadStatus->id == 4 ? 'success' : ($lead->leadStatus->id == 7 ? 'danger' : 'warning') }}">
                                        {{ $lead->leadStatus->title }}
                                    </span>
                                </td>
                                <td>{{ $lead->leadSource->title ?? 'N/A' }}</td>
                                <td>{{ $lead->telecaller->name ?? 'Unassigned' }}</td>
                                <td>{{ $lead->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <a href="{{ route('leads.edit', $lead->id) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteLead({{ $lead->id }})">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
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

<!-- Add Lead Modal -->
<div class="modal fade" id="addLeadModal" tabindex="-1" aria-labelledby="addLeadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addLeadModalLabel">Add New Lead</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('leads.store') }}" method="post" id="addLeadForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="title" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" placeholder="Enter name" required />
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter phone number" required />
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" />
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="lead_status_id" class="form-label">Lead Status <span class="text-danger">*</span></label>
                                <select class="form-select" name="lead_status_id" id="lead_status_id" required>
                                    <option value="">Select Lead Status</option>
                                    @foreach($leadStatuses as $status)
                                        <option value="{{ $status->id }}">{{ $status->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="lead_source_id" class="form-label">Lead Source <span class="text-danger">*</span></label>
                                <select class="form-select" name="lead_source_id" id="lead_source_id" required>
                                    <option value="">Select Source</option>
                                    @foreach($leadSources as $source)
                                        <option value="{{ $source->id }}">{{ $source->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" name="address" id="address" placeholder="Enter Address" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Lead</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Upload Modal -->
<div class="modal fade" id="bulkUploadModal" tabindex="-1" aria-labelledby="bulkUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkUploadModalLabel">Bulk Upload Leads</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('leads.bulk-upload.post') }}" method="post" enctype="multipart/form-data" id="bulkUploadForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-4">
                        <label for="file" class="form-label">Select Excel File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls" required />
                        <small class="text-muted">Supported formats: .xlsx, .xls (Max size: 10MB)</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="default_lead_status_id" class="form-label">Default Lead Status</label>
                                <select class="form-select" name="default_lead_status_id" id="default_lead_status_id">
                                    <option value="">Select Default Status</option>
                                    @foreach($leadStatuses as $status)
                                        <option value="{{ $status->id }}">{{ $status->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="default_lead_source_id" class="form-label">Default Lead Source</label>
                                <select class="form-select" name="default_lead_source_id" id="default_lead_source_id">
                                    <option value="">Select Default Source</option>
                                    @foreach($leadSources as $source)
                                        <option value="{{ $source->id }}">{{ $source->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="skip_duplicates" name="skip_duplicates" value="1">
                            <label class="form-check-label" for="skip_duplicates">
                                Skip duplicate entries (based on phone number)
                            </label>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <h6>Excel Format Guide:</h6>
                        <p class="mb-2">Required columns: Name, Phone, Email, Gender, Age, Place</p>
                        <p class="mb-0">Optional columns: WhatsApp, Qualification, Address, Course, Remarks</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload & Process</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function clearFilters() {
    document.getElementById('statusFilter').value = '';
    document.getElementById('sourceFilter').value = '';
    document.getElementById('searchInput').value = '';
    window.location.reload();
}

function deleteLead(id) {
    if (confirm('Are you sure you want to delete this lead?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/leads/${id}`;
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        const tokenField = document.createElement('input');
        tokenField.type = 'hidden';
        tokenField.name = '_token';
        tokenField.value = '{{ csrf_token() }}';
        
        form.appendChild(methodField);
        form.appendChild(tokenField);
        document.body.appendChild(form);
        form.submit();
    }
}

// Search functionality
document.getElementById('searchInput').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const tableRows = document.querySelectorAll('#leadsTable tbody tr');
    
    tableRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Form submission with loading state
document.getElementById('addLeadForm').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="ti ti-loader-2"></i> Saving...';
    
    setTimeout(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }, 10000);
});

document.getElementById('bulkUploadForm').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="ti ti-loader-2"></i> Processing...';
    
    setTimeout(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }, 30000);
});
</script>
@endpush