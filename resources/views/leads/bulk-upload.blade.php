@extends('layouts.mantis')

@section('title', 'Bulk Upload Leads')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Bulk Upload Leads</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('leads.index') }}">Leads</a></li>
                    <li class="breadcrumb-item">Bulk Upload</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-xl-8 col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Upload Excel File</h5>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('leads.bulk-upload.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="form-group mb-4">
                        <label for="file" class="form-label">Select Excel File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" accept=".xlsx,.xls" required>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Supported formats: .xlsx, .xls (Max size: 10MB)</small>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Default Values</label>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="default_lead_status_id" class="form-label">Default Lead Status</label>
                                <select class="form-select @error('default_lead_status_id') is-invalid @enderror" name="default_lead_status_id" id="default_lead_status_id">
                                    <option value="">Select Default Status</option>
                                    @foreach($leadStatuses as $status)
                                        <option value="{{ $status->id }}" {{ old('default_lead_status_id') == $status->id ? 'selected' : '' }}>{{ $status->title }}</option>
                                    @endforeach
                                </select>
                                @error('default_lead_status_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="default_lead_source_id" class="form-label">Default Lead Source</label>
                                <select class="form-select @error('default_lead_source_id') is-invalid @enderror" name="default_lead_source_id" id="default_lead_source_id">
                                    <option value="">Select Default Source</option>
                                    @foreach($leadSources as $source)
                                        <option value="{{ $source->id }}" {{ old('default_lead_source_id') == $source->id ? 'selected' : '' }}>{{ $source->title }}</option>
                                    @endforeach
                                </select>
                                @error('default_lead_source_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="skip_duplicates" name="skip_duplicates" value="1" {{ old('skip_duplicates') ? 'checked' : '' }}>
                            <label class="form-check-label" for="skip_duplicates">
                                Skip duplicate entries (based on phone number)
                            </label>
                        </div>
                    </div>

                    <div class="form-group text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-upload"></i> Upload & Process
                        </button>
                        <a href="{{ route('leads.index') }}" class="btn btn-secondary ms-2">
                            <i class="ti ti-x"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Excel Format Guide</h5>
            </div>
            <div class="card-body">
                <h6 class="mb-3">Required Columns:</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="ti ti-check text-success me-2"></i>Name (Column A)</li>
                    <li class="mb-2"><i class="ti ti-check text-success me-2"></i>Phone (Column B)</li>
                    <li class="mb-2"><i class="ti ti-check text-success me-2"></i>Email (Column C)</li>
                    <li class="mb-2"><i class="ti ti-check text-success me-2"></i>Gender (Column D)</li>
                    <li class="mb-2"><i class="ti ti-check text-success me-2"></i>Age (Column E)</li>
                    <li class="mb-2"><i class="ti ti-check text-success me-2"></i>Place (Column F)</li>
                </ul>

                <h6 class="mb-3 mt-4">Optional Columns:</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="ti ti-info-circle text-info me-2"></i>WhatsApp (Column G)</li>
                    <li class="mb-2"><i class="ti ti-info-circle text-info me-2"></i>Qualification (Column H)</li>
                    <li class="mb-2"><i class="ti ti-info-circle text-info me-2"></i>Address (Column I)</li>
                    <li class="mb-2"><i class="ti ti-info-circle text-info me-2"></i>Course (Column J)</li>
                    <li class="mb-2"><i class="ti ti-info-circle text-info me-2"></i>Remarks (Column K)</li>
                </ul>

                <div class="mt-4">
                    <a href="{{ asset('assets/sample-leads.xlsx') }}" class="btn btn-outline-primary btn-sm" download>
                        <i class="ti ti-download"></i> Download Sample File
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Upload Instructions</h5>
            </div>
            <div class="card-body">
                <ol class="mb-0">
                    <li class="mb-2">Download the sample Excel file</li>
                    <li class="mb-2">Fill in your lead data following the format</li>
                    <li class="mb-2">Ensure all required columns are filled</li>
                    <li class="mb-2">Save the file as .xlsx format</li>
                    <li class="mb-2">Upload the file using the form</li>
                    <li class="mb-2">Review and confirm the upload</li>
                </ol>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Uploads</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">leads_2024_01_15.xlsx</h6>
                                <small class="text-muted">50 records uploaded</small>
                            </div>
                            <span class="badge bg-success">Success</span>
                        </div>
                    </div>
                    <div class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">leads_2024_01_14.xlsx</h6>
                                <small class="text-muted">25 records uploaded</small>
                            </div>
                            <span class="badge bg-success">Success</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
@endsection

@push('scripts')
<script>
// File upload validation
document.getElementById('file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const fileSize = file.size / 1024 / 1024; // Convert to MB
        if (fileSize > 10) {
            alert('File size must be less than 10MB');
            this.value = '';
            return;
        }
        
        const fileName = file.name.toLowerCase();
        if (!fileName.endsWith('.xlsx') && !fileName.endsWith('.xls')) {
            alert('Please select an Excel file (.xlsx or .xls)');
            this.value = '';
            return;
        }
    }
});

// Form submission with loading state
document.querySelector('form').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="ti ti-loader-2"></i> Processing...';
    
    // Re-enable button after 30 seconds as fallback
    setTimeout(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }, 30000);
});
</script>
@endpush
