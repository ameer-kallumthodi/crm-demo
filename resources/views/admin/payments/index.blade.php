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
                    <!-- Invoice Details -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-gradient-primary">
                                    <h6 class="mb-0 text-white"><i class="fas fa-file-invoice me-2"></i>Invoice Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                                        <i class="fas fa-tag text-primary"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-1 text-muted">Invoice Type</h6>
                                                    <div>
                                                        @if($invoice->invoice_type == 'course')
                                                            <span class="badge bg-primary fs-6 px-3 py-2">
                                                                <i class="fas fa-graduation-cap me-1"></i>Course
                                                            </span>
                                                        @elseif($invoice->invoice_type == 'e-service')
                                                            <span class="badge bg-info fs-6 px-3 py-2">
                                                                <i class="fas fa-laptop me-1"></i>E-Service
                                                            </span>
                                                        @elseif($invoice->invoice_type == 'batch_change')
                                                            <span class="badge bg-warning fs-6 px-3 py-2">
                                                                <i class="fas fa-exchange-alt me-1"></i>Batch Change
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-success bg-opacity-10 rounded-circle p-2">
                                                        <i class="fas fa-info-circle text-success"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-1 text-muted">Details</h6>
                                                    <div class="fw-semibold">
                                                        @if($invoice->invoice_type == 'course')
                                                            @if($invoice->course_id == 9 && $invoice->student->leadDetail)
                                                                @php
                                                                    $studentDetail = $invoice->student->leadDetail;
                                                                    $university = $studentDetail->university;
                                                                    $courseType = $studentDetail->course_type;
                                                                @endphp
                                                                @if($university && $courseType)
                                                                    <i class="fas fa-university text-primary me-1"></i>{{ $university->title }} - {{ $courseType }}
                                                                @else
                                                                    <i class="fas fa-book text-primary me-1"></i>{{ $invoice->course->title ?? 'N/A' }}
                                                                @endif
                                                            @else
                                                                <i class="fas fa-book text-primary me-1"></i>{{ $invoice->course->title ?? 'N/A' }}
                                                            @endif
                                                        @elseif($invoice->invoice_type == 'e-service')
                                                            <i class="fas fa-laptop text-info me-1"></i>{{ $invoice->service_name ?? 'N/A' }}
                                                        @elseif($invoice->invoice_type == 'batch_change')
                                                            <i class="fas fa-exchange-alt text-warning me-1"></i>{{ $invoice->batch->title ?? 'N/A' }} ({{ $invoice->batch->course->title ?? 'N/A' }})
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-gradient-success text-white">
                                    <h6 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Invoice Date</h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="display-6 text-success mb-2">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <h5 class="mb-0">{{ $invoice->invoice_date->format('M d, Y') }}</h5>
                                    <small class="text-muted">{{ $invoice->invoice_date->format('l') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Summary -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="fas fa-rupee-sign text-info fs-4"></i>
                                    </div>
                                    <h6 class="text-muted mb-2">Total Amount</h6>
                                    <h3 class="text-info mb-0">₹{{ number_format($invoice->total_amount, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="fas fa-check-circle text-success fs-4"></i>
                                    </div>
                                    <h6 class="text-muted mb-2">Paid Amount</h6>
                                    <h3 class="text-success mb-0">₹{{ number_format($invoice->paid_amount, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="fas fa-clock text-warning fs-4"></i>
                                    </div>
                                    <h6 class="text-muted mb-2">Pending Amount</h6>
                                    <h3 class="text-warning mb-0">₹{{ number_format($invoice->pending_amount, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payments Table -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-gradient-dark mb-3">
                            <h6 class="mb-0 text-white"><i class="fas fa-credit-card me-2"></i>Payment History</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" style="overflow-x: auto;">
                                <table class="table table-hover mb-0 datatable" id="paymentsTable" style="min-width: 1200px;">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="border-0">#</th>
                                            <th class="border-0">Payment Date</th>
                                            <th class="border-0">Amount</th>
                                            <th class="border-0">Previous Balance</th>
                                            <th class="border-0">Payment Type</th>
                                            <th class="border-0">Transaction ID</th>
                                            <th class="border-0">Status</th>
                                            <th class="border-0">Created By</th>
                                            <th class="border-0">Status Updated By</th>
                                            <th class="border-0">Status Date</th>
                                            <th class="border-0">File</th>
                                            <th class="border-0">Actions</th>
                                        </tr>
                                    </thead>
                            <tbody>
                                @forelse($payments as $index => $payment)
                                <tr class="align-middle">
                                    <td class="fw-semibold">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-1 me-2">
                                                <i class="fas fa-calendar text-primary" style="font-size: 12px;"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $payment->created_at->format('M d, Y') }}</div>
                                                <small class="text-muted">{{ $payment->created_at->format('h:i A') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success bg-opacity-10 rounded-circle p-1 me-2">
                                                <i class="fas fa-rupee-sign text-success" style="font-size: 12px;"></i>
                                            </div>
                                            <span class="fw-bold text-success">₹{{ number_format($payment->amount_paid, 2) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-info bg-opacity-10 rounded-circle p-1 me-2">
                                                <i class="fas fa-balance-scale text-info" style="font-size: 12px;"></i>
                                            </div>
                                            <span class="fw-semibold">₹{{ number_format($payment->invoice->total_amount - $payment->previous_balance, 2) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <i class="fas fa-credit-card me-1"></i>{{ $payment->payment_type }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($payment->transaction_id)
                                            <code class="bg-light px-2 py-1 rounded">{{ $payment->transaction_id }}</code>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($payment->status == 'Pending Approval')
                                        <span class="badge bg-warning fs-6 px-3 py-2">
                                            <i class="fas fa-clock me-1"></i>Pending Approval
                                        </span>
                                        @elseif($payment->status == 'Approved')
                                        <span class="badge bg-success fs-6 px-3 py-2">
                                            <i class="fas fa-check-circle me-1"></i>Approved
                                        </span>
                                        @else
                                        <span class="badge bg-danger fs-6 px-3 py-2">
                                            <i class="fas fa-times-circle me-1"></i>Rejected
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-1 me-2">
                                                <i class="fas fa-user text-primary" style="font-size: 12px;"></i>
                                            </div>
                                            <span class="fw-semibold">{{ $payment->createdBy->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($payment->status == 'Approved' && $payment->approvedBy)
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success bg-opacity-10 rounded-circle p-1 me-2">
                                                    <i class="fas fa-check-circle text-success" style="font-size: 12px;"></i>
                                                </div>
                                                <span class="fw-semibold">{{ $payment->approvedBy->name }}</span>
                                            </div>
                                        @elseif($payment->status == 'Rejected' && $payment->rejectedBy)
                                            <div class="d-flex align-items-center">
                                                <div class="bg-danger bg-opacity-10 rounded-circle p-1 me-2">
                                                    <i class="fas fa-times-circle text-danger" style="font-size: 12px;"></i>
                                                </div>
                                                <span class="fw-semibold">{{ $payment->rejectedBy->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($payment->status == 'Approved' && $payment->approved_date)
                                            <div>
                                                <div class="fw-semibold">{{ $payment->approved_date->format('M d, Y') }}</div>
                                                <small class="text-muted">{{ $payment->approved_date->format('h:i A') }}</small>
                                            </div>
                                        @elseif($payment->status == 'Rejected' && $payment->rejected_date)
                                            <div>
                                                <div class="fw-semibold">{{ $payment->rejected_date->format('M d, Y') }}</div>
                                                <small class="text-muted">{{ $payment->rejected_date->format('h:i A') }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
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
                                            <span class="text-muted">
                                                <i class="fas fa-file-slash me-1"></i>No file
                                            </span>
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

    // Custom DataTable configuration for payments table
    $(document).ready(function() {
        // Wait for global DataTable to initialize, then customize
        setTimeout(function() {
            if ($.fn.DataTable.isDataTable('#paymentsTable')) {
                var table = $('#paymentsTable').DataTable();
                
                // Add custom styling to DataTable elements
                $('.dataTables_length select').addClass('form-select form-select-sm');
                $('.dataTables_filter input').addClass('form-control form-control-sm');
                
                // Set initial sort by Payment Date (column 1) descending
                table.order([1, 'desc']).draw();
            }
        }, 100);
    });
</script>
@endsection

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.bg-gradient-dark {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
}

.card {
    transition: all 0.3s ease;
}
/* 
.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
} */

.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
}

.badge {
    font-weight: 500;
    letter-spacing: 0.5px;
}

.btn {
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.rounded-circle {
    transition: all 0.3s ease;
}

.rounded-circle:hover {
    transform: scale(1.1);
}

code {
    font-family: 'Courier New', monospace;
    font-size: 0.85em;
}

.fw-semibold {
    font-weight: 600;
}

.fw-bold {
    font-weight: 700;
}

/* Custom scrollbar for table */
.table-responsive::-webkit-scrollbar {
    height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* DataTable horizontal scroll styling */
.dataTables_wrapper .dataTables_scroll {
    overflow-x: auto;
}

.dataTables_wrapper .dataTables_scrollBody {
    overflow-x: auto;
}

/* Ensure table columns have proper width */
#paymentsTable th,
#paymentsTable td {
    white-space: nowrap;
    min-width: 100px;
}

#paymentsTable th:nth-child(1),
#paymentsTable td:nth-child(1) {
    min-width: 50px;
    width: 50px;
}

#paymentsTable th:nth-child(2),
#paymentsTable td:nth-child(2) {
    min-width: 120px;
}

#paymentsTable th:nth-child(3),
#paymentsTable td:nth-child(3) {
    min-width: 100px;
}

#paymentsTable th:nth-child(4),
#paymentsTable td:nth-child(4) {
    min-width: 120px;
}

#paymentsTable th:nth-child(5),
#paymentsTable td:nth-child(5) {
    min-width: 100px;
}

#paymentsTable th:nth-child(6),
#paymentsTable td:nth-child(6) {
    min-width: 120px;
}

#paymentsTable th:nth-child(7),
#paymentsTable td:nth-child(7) {
    min-width: 120px;
}

#paymentsTable th:nth-child(8),
#paymentsTable td:nth-child(8) {
    min-width: 100px;
}

#paymentsTable th:nth-child(9),
#paymentsTable td:nth-child(9) {
    min-width: 120px;
}

#paymentsTable th:nth-child(10),
#paymentsTable td:nth-child(10) {
    min-width: 120px;
}

#paymentsTable th:nth-child(11),
#paymentsTable td:nth-child(11) {
    min-width: 80px;
}

#paymentsTable th:nth-child(12),
#paymentsTable td:nth-child(12) {
    min-width: 150px;
}
</style>

@include('admin.payments.add-modal')