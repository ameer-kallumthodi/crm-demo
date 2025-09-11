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
                        <a href="javascript:void(0);" class="btn btn-primary btn-sm px-3"
                            onclick="show_small_modal('{{ route('leads.add') }}', 'Add New Lead')">
                            <i class="ti ti-plus"></i> Add Lead
                        </a>
                        <a href="javascript:void(0);" class="btn btn-outline-primary btn-sm px-3"
                            onclick="show_small_modal('{{ route('leads.bulk-upload') }}', 'Bulk Upload Leads')">
                            <i class="ti ti-upload"></i> Bulk Upload
                        </a>
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


</script>
@endpush