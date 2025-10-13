<div class="container p-2">
    <form action="{{ route('admin.sub-courses.updateForm', $subCourse->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" value="{{ $subCourse->title }}" required>
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <label for="course_id" class="form-label">Course <span class="text-danger">*</span></label>
                    <select class="form-select" id="course_id" name="course_id" required>
                        <option value="">Select Course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ $subCourse->course_id == $course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ $subCourse->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-success float-end">Update</button>
    </form>
</div>
