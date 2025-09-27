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
                                    <label for="course_id" class="form-label">Course <span class="text-danger">*</span></label>
                                    <select class="form-control @error('course_id') is-invalid @enderror" name="course_id" id="course_id" required>
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
    const courseSelect = document.getElementById('course_id');
    const totalAmountInput = document.getElementById('total_amount');

    courseSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption && selectedOption.dataset.amount) {
            totalAmountInput.value = selectedOption.dataset.amount;
        } else {
            totalAmountInput.value = '';
        }
    });

    // Initialize on page load
    if (courseSelect.value) {
        const selectedOption = courseSelect.options[courseSelect.selectedIndex];
        if (selectedOption && selectedOption.dataset.amount) {
            totalAmountInput.value = selectedOption.dataset.amount;
        }
    }
});
</script>
@endsection
