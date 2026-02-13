<div class="btn-group" role="group">
    <a href="{{ route('admin.online-teaching-faculties.show', $faculty->id) }}" class="btn btn-outline-primary btn-sm" title="View">
        <i class="ti ti-eye"></i>
    </a>
    <button type="button" class="btn btn-outline-success btn-sm js-copy-form-link" 
            data-faculty-id="{{ $faculty->id }}" 
            title="Copy Form Link">
        <i class="ti ti-copy"></i>
    </button>
    <button type="button" class="btn btn-outline-info btn-sm js-open-form-link" 
            data-faculty-id="{{ $faculty->id }}" 
            title="Open Form Link">
        <i class="ti ti-external-link"></i>
    </button>
    
    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
    <form action="{{ route('admin.online-teaching-faculties.delete', $faculty->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this faculty member?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete">
            <i class="ti ti-trash"></i>
        </button>
    </form>
    @endif
</div>
