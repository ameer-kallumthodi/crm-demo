<div class="row">
    @if($postponedBatches->isEmpty())
    <div class="alert alert-info text-center">
        <i class="ti ti-info-circle me-2"></i>
        No postponed batches found.
    </div>
    @else
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Batch Title</th>
                    <th>Course</th>
                    <th>Postponed To Batch</th>
                    <th>Postponed Start Date</th>
                    <th>Postponed End Date</th>
                    <th>Postponed Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($postponedBatches as $index => $batch)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $batch->title }}</strong>
                        @if($batch->description)
                        <br><small class="text-muted">{{ Str::limit($batch->description, 50) }}</small>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-primary">{{ $batch->course->title ?? 'N/A' }}</span>
                    </td>
                    <td>
                        @if($batch->postponeBatch)
                        <span class="badge bg-info">{{ $batch->postponeBatch->title }}</span>
                        @else
                        <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td>
                        @if($batch->postpone_start_date)
                        <span class="fw-semibold">{{ \Carbon\Carbon::parse($batch->postpone_start_date)->format('d M Y') }}</span>
                        @else
                        <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td>
                        @if($batch->postpone_end_date)
                        <span class="fw-semibold">{{ \Carbon\Carbon::parse($batch->postpone_end_date)->format('d M Y') }}</span>
                        @else
                        <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td>
                        @if(!is_null($batch->batch_postpone_amount))
                        <span class="fw-semibold">₹ {{ number_format($batch->batch_postpone_amount, 2) }}</span>
                        @else
                        <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td>
                        @php
                        $today = now()->toDateString();
                        $startDate = $batch->postpone_start_date ? $batch->postpone_start_date->toDateString() : null;
                        $endDate = $batch->postpone_end_date ? $batch->postpone_end_date->toDateString() : null;

                        if ($startDate && $endDate) {
                        if ($today < $startDate) {
                            $status='upcoming' ;
                            $badgeClass='bg-info' ;
                            $statusText='Upcoming' ;
                            } elseif ($today>= $startDate && $today <= $endDate) {
                                $status='active' ;
                                $badgeClass='bg-success' ;
                                $statusText='Active' ;
                                } else {
                                $status='expired' ;
                                $badgeClass='bg-secondary' ;
                                $statusText='Expired' ;
                                }
                                } else {
                                $status='unknown' ;
                                $badgeClass='bg-secondary' ;
                                $statusText='Unknown' ;
                                }
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>