@extends('layouts.mantis')

@section('title', 'Revenue')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Revenue (Natdemy)</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Revenue</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-12">
        <p class="text-muted mb-0 small">
            Figures are from invoices linked to converted leads you can see. <strong>Cancelled</strong> admissions are excluded.
            <strong>Total payable</strong> and <strong>balance</strong> use amounts after <strong>discount</strong> (net of invoice discount).
        </p>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 border border-primary">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Total payable (Natdemy)</h6>
                <h3 class="mb-0 text-primary">₹{{ number_format(round($totals['total_payable'] ?? 0)) }}</h3>
                <small class="text-muted">Net billable after discount</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 border border-success">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Total paid amount</h6>
                <h3 class="mb-0 text-success">₹{{ number_format(round($totals['total_paid'] ?? 0)) }}</h3>
                <small class="text-muted">Recorded on invoices</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 border border-warning">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Balance amount</h6>
                <h3 class="mb-0 text-warning">₹{{ number_format(round($totals['total_balance'] ?? 0)) }}</h3>
                <small class="text-muted">Outstanding per invoice (net − paid)</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 border border-secondary">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Total discounts</h6>
                <h3 class="mb-0">₹{{ number_format(round($totals['total_discount'] ?? 0)) }}</h3>
                <small class="text-muted">Sum of invoice discount lines</small>
            </div>
        </div>
    </div>
</div>

@if(!empty($showTeamBreakdown))
<div class="row">
    <div class="col-12">
        <div class="card tbl-card">
            <div class="card-header">
                <h5 class="mb-0">By team</h5>
                <small class="text-muted">Organization-wide totals split by the lead’s team</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-borderless mb-0">
                        <thead>
                            <tr>
                                <th>Team</th>
                                <th class="text-end">Total payable</th>
                                <th class="text-end">Total paid</th>
                                <th class="text-end">Balance</th>
                                <th class="text-end">Discounts</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teamBreakdown ?? [] as $row)
                            <tr>
                                <td class="fw-medium">{{ $row['team_name'] }}</td>
                                <td class="text-end">₹{{ number_format(round($row['total_payable'])) }}</td>
                                <td class="text-end">₹{{ number_format(round($row['total_paid'])) }}</td>
                                <td class="text-end">₹{{ number_format(round($row['total_balance'])) }}</td>
                                <td class="text-end">₹{{ number_format(round($row['total_discount'])) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No invoice data to show by team.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
