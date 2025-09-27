@extends('layouts.mantis')

@section('title', 'Payment Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Payment Details</h4>
                        <div>
                            @if($payment->status == 'Approved')
                                <a href="{{ route('admin.payments.tax-invoice', $payment->id) }}" class="btn btn-warning me-2" target="_blank">
                                    <i class="fas fa-file-invoice"></i> Tax Invoice
                                </a>
                                <a href="{{ route('admin.payments.tax-invoice-pdf', $payment->id) }}" class="btn btn-danger me-2" target="_blank">
                                    <i class="fas fa-file-pdf"></i> View PDF
                                </a>
                            @endif
                            <a href="{{ route('admin.payments.index', $payment->invoice_id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Payments
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Payment Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Payment ID:</strong></td>
                                    <td>#{{ $payment->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Amount:</strong></td>
                                    <td>₹{{ number_format($payment->amount_paid, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Type:</strong></td>
                                    <td>{{ $payment->payment_type }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Transaction ID:</strong></td>
                                    <td>{{ $payment->transaction_id ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($payment->status == 'Pending Approval')
                                            <span class="badge bg-warning">Pending Approval</span>
                                        @elseif($payment->status == 'Approved')
                                            <span class="badge bg-success">Approved</span>
                                        @else
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Date:</strong></td>
                                    <td>{{ $payment->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created By:</strong></td>
                                    <td>{{ $payment->createdBy->name }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Invoice Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Invoice Number:</strong></td>
                                    <td>{{ $payment->invoice->invoice_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Student Name:</strong></td>
                                    <td>{{ $payment->invoice->student->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Student Phone:</strong></td>
                                    <td>{{ $payment->invoice->student->code }} {{ $payment->invoice->student->phone }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Course:</strong></td>
                                    <td>{{ $payment->invoice->course->title }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Amount:</strong></td>
                                    <td>₹{{ number_format($payment->invoice->total_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Paid Amount:</strong></td>
                                    <td>₹{{ number_format($payment->invoice->paid_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Pending Amount:</strong></td>
                                    <td>₹{{ number_format($payment->invoice->pending_amount, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($payment->file_upload)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6>Uploaded File</h6>
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-file fa-3x text-primary mb-3"></i>
                                    <p class="mb-3">Receipt/Proof Document</p>
                                    <a href="{{ route('admin.payments.download', $payment->id) }}" class="btn btn-primary">
                                        <i class="fas fa-download"></i> Download File
                                    </a>
                                    <a href="{{ route('admin.payments.view', $payment->id) }}" class="btn btn-primary me-2" target="_blank">
                                        <i class="fas fa-file-alt"></i> Receipt/Proof
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($payment->status == 'Pending Approval')
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6>Payment Actions</h6>
                            <div class="d-flex gap-2">
                                <form action="{{ route('admin.payments.approve', $payment->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this payment?')">
                                        <i class="fas fa-check"></i> Approve Payment
                                    </button>
                                </form>
                                <form action="{{ route('admin.payments.reject', $payment->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to reject this payment?')">
                                        <i class="fas fa-times"></i> Reject Payment
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
