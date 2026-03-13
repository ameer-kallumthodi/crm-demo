<form action="{{ route('admin.academic-delivery-structures.submit') }}" method="POST">
    @csrf
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group mb-3">
                    <label class="form-label">Course <span class="text-danger">*</span></label>
                    <select class="form-select @error('course_id') is-invalid @enderror" name="course_id" required>
                        <option value="">Select Course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group mb-3">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" required placeholder="Enter Title">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group mb-3">
                    <label class="form-label">Descriptions</label>
                    <div id="descriptions-container">
                        <div class="input-group mb-2 description-row">
                            <input type="text" class="form-control @error('descriptions.0') is-invalid @enderror" name="descriptions[]" placeholder="Enter description">
                            <button type="button" class="btn btn-outline-danger btn-remove-description" title="Remove"><i class="ti ti-trash"></i></button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-1" id="add-description-btn">
                        <i class="ti ti-plus"></i> Add Description
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
<script>
(function() {
    var container = document.getElementById('descriptions-container');
    var addBtn = document.getElementById('add-description-btn');
    if (!container || !addBtn) return;
    addBtn.addEventListener('click', function() {
        var row = document.createElement('div');
        row.className = 'input-group mb-2 description-row';
        row.innerHTML = '<input type="text" class="form-control" name="descriptions[]" placeholder="Enter description"><button type="button" class="btn btn-outline-danger btn-remove-description" title="Remove"><i class="ti ti-trash"></i></button>';
        container.appendChild(row);
    });
    container.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-description')) {
            var row = e.target.closest('.description-row');
            if (container.querySelectorAll('.description-row').length > 1) row.remove();
        }
    });
})();
</script>
