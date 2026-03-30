<div class="p-2">
    <div class="d-flex align-items-start justify-content-between mb-3">
        <div>
            <h5 class="mb-1">Team Revenue Details</h5>
            <div class="text-muted">
                Team: <span class="fw-medium">{{ $details['team_name'] ?? '-' }}</span>
            </div>
        </div>
    </div>

    @php
        $totals = $details['totals'] ?? [];
        $totalPayable = $totals['total_payable'] ?? 0;
        $totalPaid = $totals['total_paid'] ?? 0;
        $totalBalance = $totals['total_balance'] ?? 0;
        $totalDiscount = $totals['total_discount'] ?? 0;
    @endphp

    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card h-100 border border-primary">
                <div class="card-body py-2">
                    <h6 class="mb-1 f-w-400 text-muted">Total payable</h6>
                    <h3 class="mb-0 text-primary">₹{{ number_format(round($totalPayable)) }}</h3>
                    <small class="text-muted">Net after discount</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card h-100 border border-success">
                <div class="card-body py-2">
                    <h6 class="mb-1 f-w-400 text-muted">Total paid</h6>
                    <h3 class="mb-0 text-success">₹{{ number_format(round($totalPaid)) }}</h3>
                    <small class="text-muted">Recorded on invoices</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card h-100 border border-warning">
                <div class="card-body py-2">
                    <h6 class="mb-1 f-w-400 text-muted">Balance</h6>
                    <h3 class="mb-0 text-warning">₹{{ number_format(round($totalBalance)) }}</h3>
                    <small class="text-muted">Outstanding per invoice</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card h-100 border border-secondary">
                <div class="card-body py-2">
                    <h6 class="mb-1 f-w-400 text-muted">Discounts</h6>
                    <h3 class="mb-0">₹{{ number_format(round($totalDiscount)) }}</h3>
                    <small class="text-muted">Sum of invoice discounts</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card tbl-card">
        <div class="card-header">
            <h5 class="mb-0">Course-wise breakdown</h5>
            <small class="text-muted">Totals are net of discount (payable) and include paid amount.</small>
        </div>
        <div class="card-body p-3">
            <div class="table-responsive px-1">
                <table class="table table-hover table-borderless mb-0">
                    <thead>
                        <tr>
                            <th>Sl No</th>
                            <th>Course</th>
                            <th class="text-end">Total payable</th>
                            <th class="text-end">Total paid</th>
                            <th class="text-end">Balance</th>
                            <th class="text-end">Discounts</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($details['course_wise'] ?? []) as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-medium">{{ $row['course_title'] }}</td>
                                <td class="text-end">₹{{ number_format(round($row['total_payable'])) }}</td>
                                <td class="text-end">₹{{ number_format(round($row['total_paid'])) }}</td>
                                <td class="text-end">₹{{ number_format(round($row['total_balance'])) }}</td>
                                <td class="text-end">₹{{ number_format(round($row['total_discount'])) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No invoice data to show for this team.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

