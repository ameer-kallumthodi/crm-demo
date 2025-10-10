<form action="{{ route('admin.registration-links.update', $edit_data->id) }}" method="post">
    @csrf
    @method('PUT')
    <div class="row g-3">
        <div class="col-12">
            <div class="p-1">
                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="title" id="title" value="{{ $edit_data->title }}" required>
            </div>
        </div>

        <div class="col-12 p-2">
            <button class="btn btn-primary float-end" type="submit">Update Registration Link</button>
        </div>
    </div>
</form>