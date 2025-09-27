<form action="{{ route('admin.batches.submit') }}" method="post">
    @csrf
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="p-1">
                <label for="course_id" class="form-label">Course <span class="text-danger">*</span></label>
                <select class="form-control" name="course_id" id="course_id" required>
                    <option value="">Select Course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="p-1">
                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="title" id="title" required>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="p-1">
                <label for="is_active" class="form-label">Status</label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="p-1">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" name="description" id="description" rows="3" placeholder="Enter batch description"></textarea>
            </div>
        </div>

        <div class="col-12 p-2">
            <button class="btn btn-primary float-end" type="submit">Save Batch</button>
        </div>
    </div>
</form>
