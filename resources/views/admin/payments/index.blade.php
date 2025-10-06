@extends('layouts.mantis')

@section('title', 'Payments - ' . $invoice->invoice_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Payments for Invoice {{ $invoice->invoice_number }}</h4>
                        <div>
                            <a href="{{ route('admin.payments.create', $invoice->id) }}" class="btn btn-success">
                                <i class="fas fa-plus"></i> Add Payment
                            </a>
                            <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Invoice
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Invoice Summary -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Amount</h5>
                                    <h3>₹{{ number_format($invoice->total_amount, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Paid Amount</h5>
                                    <h3>₹{{ number_format($invoice->paid_amount, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Pending Amount</h5>
                                    <h3>₹{{ number_format($invoice->pending_amount, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payments Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Payment Date</th>
                                    <th>Amount</th>
                                    <th>Previous Balance</th>
                                    <th>Payment Type</th>
                                    <th>Transaction ID</th>
                                    <th>Status</th>
                                    <th>Created By</th>
                                    <th>Approved By</th>
                                    <th>Approved Date</th>
                                    <th>Rejected By</th>
                                    <th>Rejected Date</th>
                                    <th>File</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $index => $payment)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $payment->created_at->format('M d, Y h:i A') }}</td>
                                    <td>₹{{ number_format($payment->amount_paid, 2) }}</td>
                                    <td>₹{{ number_format($payment->invoice->total_amount - $payment->previous_balance, 2) }}</td>
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
                                    <td>{{ $payment->approvedBy->name ?? 'N/A' }}</td>
                                    <td>{{ $payment->approved_date ? $payment->approved_date->format('M d, Y h:i A') : 'N/A' }}</td>
                                    <td>{{ $payment->rejectedBy->name ?? 'N/A' }}</td>
                                    <td>{{ $payment->rejected_date ? $payment->rejected_date->format('M d, Y h:i A') : 'N/A' }}</td>
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
                                                @if($payment->invoice->invoice_type === 'course' && $firstPayment && $payment->id == $firstPayment->id)
                                                    <!-- Tax Invoice only for course invoices, first approved payment -->
                                                    <a href="{{ route('admin.payments.tax-invoice', $payment->id) }}" class="btn btn-sm btn-warning" title="Tax Invoice" target="_blank">
                                                        <i class="fas fa-file-invoice"></i>
                                                    </a>
                                                    <a href="{{ route('admin.payments.tax-invoice-pdf', $payment->id) }}" class="btn btn-sm btn-danger" title="View PDF" target="_blank">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                @else
                                                    <!-- Receipt for all payments, and for non-course types -->
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
                                                <button type="button" class="btn btn-sm btn-success" onclick="showApproveModal({{ $payment->id }}, '{{ $payment->amount_paid }}', '{{ $payment->invoice->total_amount - $payment->previous_balance }}', '{{ $payment->payment_type }}', '{{ $payment->transaction_id }}', '{{ $payment->file_upload }}')" title="Approve Payment">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="showRejectModal({{ $payment->id }}, '{{ $payment->amount_paid }}', '{{ $payment->payment_type }}')" title="Reject Payment">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="14" class="text-center">No payments found for this invoice.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Print Invoice Button -->
                    <!-- <div class="row mt-4">
                        <div class="col-12 text-center">
                            <button class="btn btn-primary btn-lg" onclick="printInvoice()">
                                <i class="fas fa-print"></i> Print Invoice
                            </button>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function printInvoice() {
        // Placeholder for print functionality
        alert('Print functionality will be implemented in the future.');
    }
</script>

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

@include('admin.payments.add-modal')