<form action="{{ route('admin.boards.submit') }}" method="post">
    @csrf
    <div class="row g-3">
        <div class="col-lg-12">
            <div class="p-1">
                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="title" id="title" required>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="p-1">
                <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="code" id="code" maxlength="10" required>
                <small class="text-muted">Maximum 10 characters</small>
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
                <textarea class="form-control" name="description" id="description" rows="3" placeholder="Enter board description"></textarea>
            </div>
        </div>

        <div class="col-12 p-2">
            <button class="btn btn-primary float-end" type="submit">Save Board</button>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const codeInput = document.getElementById('code');
    
    codeInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
});
</script>
