<div class="container p-2">
    <form action="{{ route('admin.courses.submit') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="title">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" id="title" placeholder="Enter Course Title" required>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="description">Description</label>
                    <textarea name="description" class="form-control" id="description" rows="3" placeholder="Enter Description"></textarea>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="duration">Duration</label>
                    <input type="text" name="duration" class="form-control" id="duration" placeholder="e.g., 3 months">
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="fees">Fees</label>
                    <input type="number" name="fees" class="form-control" id="fees" step="0.01" min="0" placeholder="0.00">
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                    <label class="form-check-label" for="is_active">
                        Active
                    </label>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success float-end">Submit</button>
    </form>
</div>
