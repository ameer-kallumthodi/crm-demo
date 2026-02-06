<form action="{{ route('admin.admission-batches.update', $edit_data->id) }}" method="post">
    @csrf
    @method('PUT')
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="p-1">
                <label for="batch_id" class="form-label">Batch <span class="text-danger">*</span></label>
                <select class="form-control" name="batch_id" id="batch_id" required>
                    <option value="">Select Batch</option>
                    @foreach($batches as $batch)
                        <option value="{{ $batch->id }}" {{ $edit_data->batch_id == $batch->id ? 'selected' : '' }}>
                            {{ $batch->course ? $batch->course->title . ' - ' : '' }}{{ $batch->title }}
                        </option>
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
                <label for="mentor_id" class="form-label">Mentor</label>
                <select class="form-control" name="mentor_id" id="mentor_id">
                    <option value="">Select Mentor (Optional)</option>
                    @foreach($mentors as $mentor)
                        <option value="{{ $mentor->id }}" {{ $edit_data->mentor_id == $mentor->id ? 'selected' : '' }}>{{ $mentor->name }}</option>
                    @endforeach
                </select>
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
                <textarea class="form-control" name="description" id="description" rows="3" placeholder="Enter admission batch description">{{ $edit_data->description }}</textarea>
            </div>
        </div>

        <div class="col-12 p-2">
            <button class="btn btn-primary float-end" type="submit">Update Admission Batch</button>
        </div>
    </div>
</form>
