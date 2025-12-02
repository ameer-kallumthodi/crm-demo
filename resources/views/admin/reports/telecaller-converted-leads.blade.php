@extends('layouts.mantis')

@section('title', 'Telecaller Converted Leads')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">
                        Telecaller Converted Leads
                    </h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reports.telecallers-sales') }}">Reports</a></li>
                    <li class="breadcrumb-item">Telecaller Converted Leads</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Header ] start -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div>
                    <h5 class="mb-1">
                        {{ $telecaller->name }}
                        @if($telecaller->email)
                            <small class="text-muted">({{ $telecaller->email }})</small>
                        @endif
                    </h5>
                    <p class="mb-0 text-muted">
                        Converted leads from
                        <strong>{{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }}</strong>
                        to
                        <strong>{{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}</strong>
                    </p>
                </div>
                <div class="mt-3 mt-md-0">
                    @if($mode === 'thanzeels-eschool-sales')
                        <a href="{{ route('admin.reports.thanzeels-eschool-sales', ['from_date' => $fromDate, 'to_date' => $toDate, 'telecaller_id' => $telecaller->id]) }}" class="btn btn-outline-secondary">
                            <i class="ti ti-arrow-left"></i> Back to Thanzeels & E-School Report
                        </a>
                    @else
                        <a href="{{ route('admin.reports.telecallers-sales', ['from_date' => $fromDate, 'to_date' => $toDate, 'telecaller_id' => $telecaller->id]) }}" class="btn btn-outline-secondary">
                            <i class="ti ti-arrow-left"></i> Back to Telecallers Sales Report
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Header ] end -->

<!-- [ Table ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    Converted Leads List
                    <small class="text-muted">(No additional filters applied)</small>
                </h5>
            </div>
            <div class="card-body">
                @if($rows->count() === 0)
                    <div class="text-center py-5">
                        <i class="ti ti-inbox f-48 text-muted mb-3"></i>
                        <p class="text-muted mb-0">No converted leads found for the selected telecaller and date range.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 80px;">Sl No</th>
                                    <th>Student Name</th>
                                    <th>Phone</th>
                                    <th class="text-end">Total Sale Amount</th>
                                    <th class="text-end">Received Amount (This Telecaller)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rows as $index => $row)
                                    @php
                                        /** @var \App\Models\ConvertedLead $student */
                                        $student = $row['student'];
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $student->name }}</strong>
                                            @if($student->course)
                                                <br>
                                                <small class="text-muted">{{ $student->course->title }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="tel:{{ $student->phone }}">{{ $student->phone }}</a>
                                        </td>
                                        <td class="text-end">
                                            ₹{{ number_format(round($row['total_sale_amount'])) }}
                                        </td>
                                        <td class="text-end">
                                            <strong class="text-success">
                                                ₹{{ number_format(round($row['received_amount'])) }}
                                            </strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- [ Table ] end -->

@endsection


