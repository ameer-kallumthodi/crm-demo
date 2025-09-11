<div class="container p-2">
    <form action="{{ route('admin.countries.submit') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="title">Country Name <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" id="title" placeholder="Enter Country Name" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="code">Country Code <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control" id="code" placeholder="e.g., US" maxlength="3" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="phone_code">Phone Code <span class="text-danger">*</span></label>
                    <input type="text" name="phone_code" class="form-control" id="phone_code" placeholder="e.g., +1" required>
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
