@extends('layouts.mantis')

@section('title', 'Invoices - ' . $student->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Invoices for {{ $student->name }}</h4>
                        <div>
                            <a href="{{ route('admin.invoices.create', $student->id) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create Invoice
                            </a>
                            <a href="{{ route('admin.converted-leads.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Students
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Student Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Student Information</h6>
                            <p><strong>Name:</strong> {{ $student->name }}</p>
                            <p><strong>Phone:</strong> {{ $student->code }} {{ $student->phone }}</p>
                            <p><strong>Email:</strong> {{ $student->email ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Course Information</h6>
                            <p><strong>Course:</strong> 
                                @if($student->course_id == 9 && $student->leadDetail)
                                    @php
                                        $studentDetail = $student->leadDetail;
                                        $university = $studentDetail->university;
                                        $courseType = $studentDetail->course_type;
                                    @endphp
                                    @if($university && $courseType)
                                        {{ $university->title }} - {{ $courseType }}
                                    @else
                                        {{ $student->course->title ?? 'N/A' }}
                                    @endif
                                @else
                                    {{ $student->course->title ?? 'N/A' }}
                                @endif
                            </p>
                            <p><strong>Batch:</strong> {{ $student->batch->title ?? 'N/A' }}</p>
                            <p><strong>Academic Assistant:</strong> {{ $student->academicAssistant->name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Invoices</h5>
                                    <h3>{{ $summary['total_invoices'] }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Amount</h5>
                                    <h3>₹{{ number_format($summary['total_amount'], 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Paid</h5>
                                    <h3>₹{{ number_format($summary['total_paid'], 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Pending</h5>
                                    <h3>₹{{ number_format($summary['total_pending'], 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Invoices Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Invoice #</th>
                                    <th>Type</th>
                                    <th>Details</th>
                                    <th>Total Amount</th>
                                    <th>Paid Amount</th>
                                    <th>Pending Amount</th>
                                    <th>Status</th>
                                    <th>Invoice Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $index => $invoice)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $invoice->invoice_number }}</td>
                                    <td>
                                        @if($invoice->invoice_type == 'course')
                                            <span class="badge bg-primary">Course</span>
                                        @elseif($invoice->invoice_type == 'e-service')
                                            <span class="badge bg-info">E-Service</span>
                                        @elseif($invoice->invoice_type == 'batch_change')
                                            <span class="badge bg-warning">Batch Change</span>
                                        @elseif($invoice->invoice_type == 'batch_postpond')
                                            <span class="badge bg-warning text-dark">Batch Postponed</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($invoice->invoice_type == 'course')
                                            @if($invoice->course_id == 9 && $invoice->student->leadDetail)
                                                @php
                                                    $studentDetail = $invoice->student->leadDetail;
                                                    $university = $studentDetail->university;
                                                    $courseType = $studentDetail->course_type;
                                                @endphp
                                                @if($university && $courseType)
                                                    {{ $university->title }} - {{ $courseType }}
                                                @else
                                                    {{ $invoice->course->title ?? 'N/A' }}
                                                @endif
                                            @else
                                                {{ $invoice->course->title ?? 'N/A' }}
                                            @endif
                                        @elseif($invoice->invoice_type == 'e-service')
                                            {{ $invoice->service_name }}
                                        @elseif($invoice->invoice_type == 'batch_change' || $invoice->invoice_type == 'batch_postpond')
                                            {{ $invoice->batch->title ?? 'N/A' }} ({{ $invoice->batch->course->title ?? 'N/A' }})
                                        @endif
                                    </td>
                                    <td>₹{{ number_format($invoice->total_amount, 2) }}</td>
                                    <td>₹{{ number_format($invoice->paid_amount, 2) }}</td>
                                    <td>₹{{ number_format($invoice->pending_amount, 2) }}</td>
                                    <td>
                                        @if($invoice->status == 'Not Paid')
                                            <span class="badge bg-danger">Not Paid</span>
                                        @elseif($invoice->status == 'Partially Paid')
                                            <span class="badge bg-warning">Partially Paid</span>
                                        @else
                                            <span class="badge bg-success">Fully Paid</span>
                                        @endif
                                    </td>
                                    <td>{{ $invoice->invoice_date->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="btn btn-sm btn-info" title="View Invoice">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @php
                                            $canEditInvoice = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_finance();
                                            $hasApprovedPayments = $invoice->payments->where('status', 'Approved')->count() > 0;
                                        @endphp
                                        @if($canEditInvoice && !$hasApprovedPayments)
                                        <button type="button" class="btn btn-sm btn-outline-warning" title="Edit Amount"
                                            onclick="show_small_modal('{{ route('admin.invoices.edit-amount', $invoice->id) }}', 'Edit Invoice Amount')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @endif
                                        <a href="{{ route('admin.payments.index', $invoice->id) }}" class="btn btn-sm btn-primary" title="Manage Payments">
                                            <i class="fas fa-credit-card"></i>
                                        </a>
                                        @if($invoice->payments->where('status', 'Approved')->count() > 0)
                                            <a href="{{ route('admin.payments.tax-invoice', $invoice->payments->where('status', 'Approved')->first()->id) }}" class="btn btn-sm btn-warning" title="Tax Invoice" target="_blank">
                                                <i class="fas fa-file-invoice"></i>
                                            </a>
                                            <a href="{{ route('admin.payments.tax-invoice-pdf', $invoice->payments->where('status', 'Approved')->first()->id) }}" class="btn btn-sm btn-danger" title="View PDF" target="_blank">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">No invoices found for this student.</td>
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
@endsection
