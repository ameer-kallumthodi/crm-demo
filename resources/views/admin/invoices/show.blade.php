@extends('layouts.mantis')

@section('title', 'Invoice Details - ' . $invoice->invoice_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Invoice Details - {{ $invoice->invoice_number }}</h4>
                        <div>
                            <a href="{{ route('admin.payments.create', $invoice->id) }}" class="btn btn-success">
                                <i class="fas fa-plus"></i> Add Payment
                            </a>
                            <a href="{{ route('admin.payments.index', $invoice->id) }}" class="btn btn-primary">
                                <i class="fas fa-credit-card"></i> View Payments
                            </a> 
                            <a href="{{ route('admin.invoices.index', $invoice->student_id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Invoices
                            </a>
                        </div>
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
                                    <td><strong>Invoice Date:</strong></td>
                                    <td>{{ $invoice->invoice_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($invoice->status == 'Not Paid')
                                            <span class="badge bg-danger">Not Paid</span>
                                        @elseif($invoice->status == 'Partially Paid')
                                            <span class="badge bg-warning">Partially Paid</span>
                                        @else
                                            <span class="badge bg-success">Fully Paid</span>
                                        @endif
                                    </td>
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
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $invoice->student->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Course:</strong></td>
                                    <td>{{ $invoice->course->title }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Batch:</strong></td>
                                    <td>{{ $invoice->student->batch->title ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Academic Assistant:</strong></td>
                                    <td>{{ $invoice->student->academicAssistant->name ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Payments History -->
                    <div class="row">
                        <div class="col-12">
                            <h6>Payments History</h6>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Payment Date</th>
                                            <th>Amount</th>
                                            <th>Payment Type</th>
                                            <th>Transaction ID</th>
                                            <th>Status</th>
                                            <th>Created By</th>
                                            <th>File</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($invoice->payments as $index => $payment)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $payment->created_at->format('M d, Y h:i A') }}</td>
                                            <td>₹{{ number_format($payment->amount_paid, 2) }}</td>
                                            <td>{{ $payment->payment_type }}</td>
                                            <td>{{ $payment->transaction_id ?? 'N/A' }}</td>
                                            <td>
                                                @if($payment->status == 'Pending Approval')
                                                    <span class="badge bg-warning">Pending Approval</span>
                                                @elseif($payment->status == 'Approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @else
                                                    <span class="badge bg-danger">Rejected</span>
                                                @endif
                                            </td>
                                            <td>{{ $payment->createdBy->name }}</td>
                                            <td>
                                                @if($payment->file_upload)
                                                    <div class="btn-group btn-group-sm" role="group" aria-label="Receipt/Proof">
                                                        <a href="{{ route('admin.payments.download', $payment->id) }}" class="btn btn-outline-primary" title="Download Receipt/Proof">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                        <a href="{{ route('admin.payments.view', $payment->id) }}" class="btn btn-primary" title="View Receipt/Proof" target="_blank">
                                                            <i class="fas fa-file-alt"></i>
                                                        </a>
                                                    </div>
                                                @else
                                                    <span class="text-muted">No file</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-1 justify-content-start">
                                                    <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-sm btn-info" title="View Payment">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($payment->status == 'Approved')
                                                        @if($firstPayment && $payment->id == $firstPayment->id)
                                                            <!-- Show Tax Invoice for first payment -->
                                                            <a href="{{ route('admin.payments.tax-invoice', $payment->id) }}" class="btn btn-sm btn-warning" title="Tax Invoice" target="_blank">
                                                                <i class="fas fa-file-invoice"></i>
                                                            </a>
                                                            <a href="{{ route('admin.payments.tax-invoice-pdf', $payment->id) }}" class="btn btn-sm btn-danger" title="View PDF" target="_blank">
                                                                <i class="fas fa-file-pdf"></i>
                                                            </a>
                                                        @else
                                                            <!-- Show Payment Receipt for other payments -->
                                                            <a href="{{ route('admin.payments.payment-receipt', $payment->id) }}" class="btn btn-sm btn-warning" title="Payment Receipt" target="_blank">
                                                                <i class="fas fa-receipt"></i>
                                                            </a>
                                                            <a href="{{ route('admin.payments.payment-receipt-pdf', $payment->id) }}" class="btn btn-sm btn-danger" title="View PDF" target="_blank">
                                                                <i class="fas fa-file-pdf"></i>
                                                            </a>
                                                        @endif
                                                    @endif
                                                    @if($payment->status == 'Pending Approval')
                                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_finance())
                                                        <button type="button" class="btn btn-sm btn-success approve-payment-btn" 
                                                                data-payment-id="{{ $payment->id }}"
                                                                data-amount="{{ $payment->amount_paid }}"
                                                                data-previous-balance="{{ $invoice->total_amount - $payment->previous_balance }}"
                                                                data-payment-type="{{ $payment->payment_type }}"
                                                                data-transaction-id="{{ $payment->transaction_id }}"
                                                                data-file-upload="{{ $payment->file_upload }}"
                                                                title="Approve Payment">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger reject-payment-btn" 
                                                                data-payment-id="{{ $payment->id }}"
                                                                data-amount="{{ $payment->amount_paid }}"
                                                                data-payment-type="{{ $payment->payment_type }}"
                                                                title="Reject Payment">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="9" class="text-center">No payments found for this invoice.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Approval Modal -->
<div class="modal fade" id="approvePaymentModal" tabindex="-1" aria-labelledby="approvePaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approvePaymentModalLabel">Approve Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Please review the payment details before approving:</strong>
                </div>
                <div class="row">
                    <div class="col-6">
                        <strong>Amount:</strong>
                    </div>
                    <div class="col-6" id="approveAmount">
                        <!-- Amount will be populated here -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <strong>Previous Balance:</strong>
                    </div>
                    <div class="col-6" id="approvePreviousBalance">
                        <!-- Previous balance will be populated here -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <strong>Payment Type:</strong>
                    </div>
                    <div class="col-6" id="approvePaymentType">
                        <!-- Payment type will be populated here -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <strong>Transaction ID:</strong>
                    </div>
                    <div class="col-6" id="approveTransactionId">
                        <!-- Transaction ID will be populated here -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <strong>Receipt/Proof:</strong>
                    </div>
                    <div class="col-6" id="approveFile">
                        <!-- File will be populated here -->
                    </div>
                </div>
                <hr>
                <p class="text-muted">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Once approved, this payment will be added to the invoice total and cannot be undone.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="approvePaymentForm" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Approve Payment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Payment Rejection Modal -->
<div class="modal fade" id="rejectPaymentModal" tabindex="-1" aria-labelledby="rejectPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectPaymentModalLabel">Reject Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Are you sure you want to reject this payment?</strong>
                </div>
                <div class="row">
                    <div class="col-6">
                        <strong>Amount:</strong>
                    </div>
                    <div class="col-6" id="rejectAmount">
                        <!-- Amount will be populated here -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <strong>Payment Type:</strong>
                    </div>
                    <div class="col-6" id="rejectPaymentType">
                        <!-- Payment type will be populated here -->
                    </div>
                </div>
                <hr>
                <p class="text-muted">
                    <i class="fas fa-info-circle me-2"></i>
                    Rejected payments will not be added to the invoice total.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="rejectPaymentForm" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-2"></i>Reject Payment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle approve payment button clicks
        document.querySelectorAll('.approve-payment-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const paymentId = this.getAttribute('data-payment-id');
                const amount = this.getAttribute('data-amount');
                const previousBalance = this.getAttribute('data-previous-balance');
                const paymentType = this.getAttribute('data-payment-type');
                const transactionId = this.getAttribute('data-transaction-id');
                const fileUpload = this.getAttribute('data-file-upload');

                showApproveModal(paymentId, amount, previousBalance, paymentType, transactionId, fileUpload);
            });
        });

        // Handle reject payment button clicks
        document.querySelectorAll('.reject-payment-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const paymentId = this.getAttribute('data-payment-id');
                const amount = this.getAttribute('data-amount');
                const paymentType = this.getAttribute('data-payment-type');

                showRejectModal(paymentId, amount, paymentType);
            });
        });
    });

    // Show approve payment modal
    function showApproveModal(paymentId, amount, previousBalance, paymentType, transactionId, fileUpload) {
        document.getElementById('approveAmount').textContent = '₹' + parseFloat(amount).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        document.getElementById('approvePreviousBalance').textContent = '₹' + parseFloat(previousBalance).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        document.getElementById('approvePaymentType').textContent = paymentType;
        document.getElementById('approveTransactionId').textContent = transactionId || 'N/A';

        // Handle file display
        const fileElement = document.getElementById('approveFile');
        if (fileUpload && fileUpload !== '') {
            const fileName = fileUpload.split('/').pop(); // Get filename from path
            const fileUrl = '{{ route("admin.payments.view", ":id") }}'.replace(':id', paymentId);
            fileElement.innerHTML = `<a href="${fileUrl}" target="_blank" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-eye me-1"></i>View ${fileName}
        </a>`;
        } else {
            fileElement.innerHTML = '<span class="text-muted">No file uploaded</span>';
        }

        document.getElementById('approvePaymentForm').action = '{{ route("admin.payments.approve", ":id") }}'.replace(':id', paymentId);

        const modal = new bootstrap.Modal(document.getElementById('approvePaymentModal'), {
            backdrop: 'static',
            keyboard: false
        });
        modal.show();
    }

    // Show reject payment modal
    function showRejectModal(paymentId, amount, paymentType) {
        document.getElementById('rejectAmount').textContent = '₹' + parseFloat(amount).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        document.getElementById('rejectPaymentType').textContent = paymentType;
        document.getElementById('rejectPaymentForm').action = '{{ route("admin.payments.reject", ":id") }}'.replace(':id', paymentId);

        const modal = new bootstrap.Modal(document.getElementById('rejectPaymentModal'), {
            backdrop: 'static',
            keyboard: false
        });
        modal.show();
    }
</script>
@endsection
