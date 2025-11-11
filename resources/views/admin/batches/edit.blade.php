<form action="{{ route('admin.batches.update', $edit_data->id) }}" method="post">
    @csrf
    @method('PUT')
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="p-1">
                <label for="course_id" class="form-label">Course <span class="text-danger">*</span></label>
                <select class="form-control" name="course_id" id="course_id" required>
                    <option value="">Select Course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ $edit_data->course_id == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="p-1">
                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="title" id="title" value="{{ $edit_data->title }}" required>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="p-1">
                <label for="amount" class="form-label">Amount</label>
                <div class="input-group">
                    <span class="input-group-text">₹</span>
                    <input type="number" step="0.01" min="0" class="form-control" name="amount" id="amount" value="{{ old('amount', $edit_data->amount) }}" placeholder="Enter amount">
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="p-1">
                <label for="is_active" class="form-label">Status</label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" {{ $edit_data->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="p-1">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" name="description" id="description" rows="3" placeholder="Enter batch description">{{ $edit_data->description }}</textarea>
            </div>
        </div>

        <div class="col-12 p-2">
            <button class="btn btn-primary float-end" type="submit">Update Batch</button>
        </div>
    </div>
</form>
