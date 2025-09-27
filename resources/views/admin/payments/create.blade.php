@extends('layouts.mantis')

@section('title', 'Add Payment - ' . $invoice->invoice_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Add Payment for Invoice {{ $invoice->invoice_number }}</h4>
                        <a href="{{ route('admin.payments.index', $invoice->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Payments
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Invoice Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Invoice Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Invoice Number:</strong></td>
                                    <td>{{ $invoice->invoice_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Amount:</strong></td>
                                    <td>₹{{ number_format($invoice->total_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Paid Amount:</strong></td>
                                    <td>₹{{ number_format($invoice->paid_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Pending Amount:</strong></td>
                                    <td>₹{{ number_format($invoice->pending_amount, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Student Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $invoice->student->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $invoice->student->code }} {{ $invoice->student->phone }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Course:</strong></td>
                                    <td>{{ $invoice->course->title }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <form action="{{ route('admin.payments.store', $invoice->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="amount_paid" class="form-label">Payment Amount <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('amount_paid') is-invalid @enderror" 
                                           name="amount_paid" id="amount_paid" step="0.01" min="0.01" 
                                           max="{{ $invoice->pending_amount }}" value="{{ old('amount_paid') }}" required>
                                    <div class="form-text">Maximum amount: ₹{{ number_format($invoice->pending_amount, 2) }}</div>
                                    @error('amount_paid')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_type" class="form-label">Payment Type <span class="text-danger">*</span></label>
                                    <select class="form-control @error('payment_type') is-invalid @enderror" name="payment_type" id="payment_type" required>
                                        <option value="">Select Payment Type</option>
                                        <option value="Cash" {{ old('payment_type') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="Online" {{ old('payment_type') == 'Online' ? 'selected' : '' }}>Online</option>
                                        <option value="Bank" {{ old('payment_type') == 'Bank' ? 'selected' : '' }}>Bank</option>
                                        <option value="Cheque" {{ old('payment_type') == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                        <option value="Card" {{ old('payment_type') == 'Card' ? 'selected' : '' }}>Card</option>
                                        <option value="Other" {{ old('payment_type') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('payment_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="transaction_id" class="form-label">Transaction ID</label>
                                    <input type="text" class="form-control @error('transaction_id') is-invalid @enderror" 
                                           name="transaction_id" id="transaction_id" 
                                           value="{{ old('transaction_id') }}" placeholder="Enter transaction ID">
                                    @error('transaction_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="file_upload" class="form-label">Upload Receipt/Proof</label>
                                    <input type="file" class="form-control @error('file_upload') is-invalid @enderror" 
                                           name="file_upload" id="file_upload" accept=".pdf,.jpg,.jpeg,.png">
                                    <div class="form-text">Accepted formats: PDF, JPG, JPEG, PNG (Max: 2MB)</div>
                                    @error('file_upload')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Add Payment
                                </button>
                                <a href="{{ route('admin.payments.index', $invoice->id) }}" class="btn btn-secondary">
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
    const amountInput = document.getElementById('amount_paid');
    const maxAmount = {{ $invoice->pending_amount }};

    amountInput.addEventListener('input', function() {
        if (parseFloat(this.value) > maxAmount) {
            this.value = maxAmount;
            alert('Payment amount cannot exceed the pending amount of ₹' + maxAmount.toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
        }
    });
});
</script>
@endsection
