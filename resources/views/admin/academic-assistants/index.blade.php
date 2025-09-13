@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Academic Assistants</h5>
                    <a href="{{ route('academic-assistants.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus"></i> Add Academic Assistant
                    </a>
                </div>
                <div class="card-body">
                    @if(session('message_success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('message_success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('message_danger'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('message_danger') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($academicAssistants as $assistant)
                                    <tr>
                                        <td>{{ $academicAssistants->firstItem() + $loop->index }}</td>
                                        <td>{{ $assistant->name }}</td>
                                        <td>{{ $assistant->email }}</td>
                                        <td>{{ \App\Helpers\PhoneNumberHelper::display($assistant->code, $assistant->phone) }}</td>
                                        <td>{{ $assistant->address ? Str::limit($assistant->address, 30) : 'N/A' }}</td>
                                        <td>
                                            <span class="badge {{ $assistant->is_active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $assistant->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>{{ $assistant->createdBy ? $assistant->createdBy->name : 'N/A' }}</td>
                                        <td>{{ $assistant->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('academic-assistants.show', $assistant->id) }}" 
                                                   class="btn btn-sm btn-outline-info" title="View">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                                <a href="{{ route('academic-assistants.edit', $assistant->id) }}" 
                                                   class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="ti ti-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        onclick="confirmDelete('{{ route('academic-assistants.destroy', $assistant->id) }}', '{{ $assistant->name }}')" 
                                                        title="Delete">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No academic assistants found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($academicAssistants->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $academicAssistants->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this academic assistant?</p>
                <p><strong id="deleteItemName"></strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(url, name) {
    document.getElementById('deleteForm').action = url;
    document.getElementById('deleteItemName').textContent = name;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endsection
