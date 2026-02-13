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
</div>
