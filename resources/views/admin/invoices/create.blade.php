@extends('layouts.mantis')

@section('title', 'Create Invoice - ' . $student->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Create Invoice for {{ $student->name }}</h4>
                        <a href="{{ route('admin.invoices.index', $student->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Invoices
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.invoices.store', $student->id) }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="invoice_type" class="form-label">Invoice Type <span class="text-danger">*</span></label>
                                    <select class="form-control @error('invoice_type') is-invalid @enderror" name="invoice_type" id="invoice_type" required>
                                        <option value="">Select Invoice Type</option>
                                        <option value="course" {{ old('invoice_type') == 'course' ? 'selected' : '' }}>Course</option>
                                        <option value="e-service" {{ old('invoice_type') == 'e-service' ? 'selected' : '' }}>E-Service</option>
                                        <option value="batch_change" {{ old('invoice_type') == 'batch_change' ? 'selected' : '' }}>Batch Change</option>
                                    </select>
                                    @error('invoice_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Course Selection (shown when course is selected) -->
                            <div class="col-md-6" id="course_selection" style="display: none;">
                                <div class="mb-3">
                                    <label for="course_id" class="form-label">Course <span class="text-danger">*</span></label>
                                    <select class="form-control @error('course_id') is-invalid @enderror" name="course_id" id="course_id">
                                        <option value="">Select Course</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" data-amount="{{ $course->amount }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                                {{ $course->title }} - ₹{{ number_format($course->amount, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('course_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Batch Selection (shown when batch_change is selected) -->
                            <div class="col-md-6" id="batch_selection" style="display: none;">
                                <div class="mb-3">
                                    <label for="batch_id" class="form-label">Batch <span class="text-danger">*</span></label>
                                    <select class="form-control @error('batch_id') is-invalid @enderror" name="batch_id" id="batch_id">
                                        <option value="">Select Batch</option>
                                        @foreach($batches as $batch)
                                            <option value="{{ $batch->id }}" {{ old('batch_id') == $batch->id ? 'selected' : '' }}>
                                                {{ $batch->title }} ({{ $batch->course->title }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('batch_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- E-Service Fields (shown when e-service is selected) -->
                            <div class="col-md-6" id="service_name_field" style="display: none;">
                                <div class="mb-3">
                                    <label for="service_name" class="form-label">Service Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('service_name') is-invalid @enderror" 
                                           name="service_name" id="service_name" 
                                           value="{{ old('service_name') }}">
                                    @error('service_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6" id="service_amount_field" style="display: none;">
                                <div class="mb-3">
                                    <label for="service_amount" class="form-label">Service Amount <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('service_amount') is-invalid @enderror" 
                                           name="service_amount" id="service_amount" step="0.01" min="0" 
                                           value="{{ old('service_amount') }}">
                                    @error('service_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="total_amount" class="form-label">Total Amount <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('total_amount') is-invalid @enderror" 
                                           name="total_amount" id="total_amount" step="0.01" min="0" 
                                           value="{{ old('total_amount') }}" required>
                                    @error('total_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="invoice_date" class="form-label">Invoice Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('invoice_date') is-invalid @enderror" 
                                           name="invoice_date" id="invoice_date" 
                                           value="{{ old('invoice_date', now()->toDateString()) }}" required>
                                    @error('invoice_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Student Information</label>
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong>Name:</strong> {{ $student->name }}</p>
                                                    <p><strong>Phone:</strong> {{ $student->code }} {{ $student->phone }}</p>
                                                    <p><strong>Email:</strong> {{ $student->email ?? 'N/A' }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Current Course:</strong> {{ $student->course->title }}</p>
                                                    <p><strong>Batch:</strong> {{ $student->batch->title ?? 'N/A' }}</p>
                                                    <p><strong>Academic Assistant:</strong> {{ $student->academicAssistant->name ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Invoice
                                </button>
                                <a href="{{ route('admin.invoices.index', $student->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const invoiceTypeSelect = document.getElementById('invoice_type');
    const courseSelect = document.getElementById('course_id');
    const totalAmountInput = document.getElementById('total_amount');
    const serviceAmountInput = document.getElementById('service_amount');

    // Show/hide fields based on invoice type
    function toggleFields() {
        const invoiceType = invoiceTypeSelect.value;
        
        // Hide all conditional fields
        document.getElementById('course_selection').style.display = 'none';
        document.getElementById('batch_selection').style.display = 'none';
        document.getElementById('service_name_field').style.display = 'none';
        document.getElementById('service_amount_field').style.display = 'none';
        
        // Show relevant fields
        if (invoiceType === 'course') {
            document.getElementById('course_selection').style.display = 'block';
        } else if (invoiceType === 'batch_change') {
            document.getElementById('batch_selection').style.display = 'block';
            totalAmountInput.value = '2000';
            totalAmountInput.readOnly = true;
        } else if (invoiceType === 'e-service') {
            document.getElementById('service_name_field').style.display = 'block';
            document.getElementById('service_amount_field').style.display = 'block';
            totalAmountInput.readOnly = false;
        }
    }

    // Handle invoice type change
    invoiceTypeSelect.addEventListener('change', function() {
        toggleFields();
        
        // Clear total amount when changing type
        if (this.value !== 'batch_change') {
            totalAmountInput.value = '';
            totalAmountInput.readOnly = false;
        }
    });

    // Handle course selection
    courseSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption && selectedOption.dataset.amount) {
            totalAmountInput.value = selectedOption.dataset.amount;
        } else {
            totalAmountInput.value = '';
        }
    });

    // Handle service amount change
    serviceAmountInput.addEventListener('change', function() {
        if (invoiceTypeSelect.value === 'e-service') {
            totalAmountInput.value = this.value;
        }
    });

    // Initialize on page load
    toggleFields();
    
    if (courseSelect.value) {
        const selectedOption = courseSelect.options[courseSelect.selectedIndex];
        if (selectedOption && selectedOption.dataset.amount) {
            totalAmountInput.value = selectedOption.dataset.amount;
        }
    }
});
</script>
@endsection
