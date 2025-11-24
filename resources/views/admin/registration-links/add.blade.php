<form action="{{ route('admin.registration-links.submit') }}" method="post">
    @csrf
    <div class="row g-3">
        <div class="col-12">
            <div class="p-1">
                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="title" id="title" required>
            </div>
        </div>

        <div class="col-12">
            <div class="p-1">
                <label for="color_code" class="form-label">Display Color <span class="text-danger">*</span></label>
                <input type="color" class="form-control form-control-color w-100" name="color_code" id="color_code" value="{{ old('color_code', '#0d6efd') }}" required>
                <small class="text-muted d-block mt-1">Choose the color that should be used wherever this registration link is shown.</small>
            </div>
        </div>

        <div class="col-12 p-2">
            <button class="btn btn-primary float-end" type="submit">Save Registration Link</button>
        </div>
    </div>
</form>
