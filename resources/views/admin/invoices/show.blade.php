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
                                            <th>Payment Date</th>
                                            <th>Amount</th>
                                            <th>Payment Type</th>
                                            <th>Transaction ID</th>
                                            <th>Status</th>
                                            <th>Created By</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($invoice->payments as $payment)
                                        <tr>
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
                                                <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($payment->status == 'Pending Approval')
                                                    <form action="{{ route('admin.payments.approve', $payment->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to approve this payment?')">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.payments.reject', $payment->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to reject this payment?')">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                @if($payment->file_upload)
                                                    <a href="{{ route('admin.payments.download', $payment->id) }}" class="btn btn-sm btn-secondary">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No payments found for this invoice.</td>
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
@endsection
