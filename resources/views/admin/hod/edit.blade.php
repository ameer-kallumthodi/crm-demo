<div class="container p-2">
    <form id="hodEditForm" action="{{ route('admin.hod.update', $hodUser->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" id="name" placeholder="Enter Name" value="{{ old('name', $hodUser->name) }}" required>
                    <div class="invalid-feedback" id="name-error"></div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" id="email" placeholder="Enter Email" value="{{ old('email', $hodUser->email) }}" required>
                    <div class="invalid-feedback" id="email-error"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label" for="code">Country Code</label>
                        <select class="form-select" id="code" name="code">
                            <option value="">Select Country</option>
                            @foreach($country_codes as $code => $country)
                                <option value="{{ $code }}" {{ (old('code', $hodUser->code) == $code) ? 'selected' : '' }}>{{ $code }} - {{ $country }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" id="code-error"></div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label" for="phone">Phone</label>
                        <input type="text" name="phone" class="form-control" id="phone" placeholder="Enter Phone" value="{{ old('phone', $hodUser->phone) }}">
                        <div class="invalid-feedback" id="phone-error"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ (old('is_active', $hodUser->is_active) == 1) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            <i class="ti ti-check me-1"></i>Is Active
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success float-end">Update</button>
    </form>
</div>

<script>
document.getElementById('hodEditForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    
    // Clear previous errors
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    
    submitButton.disabled = true;
    submitButton.textContent = 'Updating...';
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.href = '{{ route("admin.hod.index") }}';
        } else {
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const input = form.querySelector(`[name="${field}"]`);
                    const errorDiv = form.querySelector(`#${field}-error`);
                    if (input) {
                        input.classList.add('is-invalid');
                    }
                    if (errorDiv) {
                        errorDiv.textContent = data.errors[field][0];
                    }
                });
            }
            alert(data.message || 'Please correct the errors and try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    })
    .finally(() => {
        submitButton.disabled = false;
        submitButton.textContent = 'Update';
    });
});
</script>

