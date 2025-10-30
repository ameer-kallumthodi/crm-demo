@extends('layouts.mantis')

@section('title', 'BOSSE Support - Converted Lead Details')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">BOSSE Support - Converted Lead Details</h5>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end align-items-center gap-2">
                    <a href="{{ route('admin.support-bosse-converted-leads.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left"></i> Back to Support List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;">
                            <i class="ti ti-user"></i>
                        </div>
                        <div>
                            <div class="h5 mb-1">{{ $convertedLead->name }}
                                @if($convertedLead->course?->title)
                                <span class="badge bg-light text-dark border ms-2"><i class="ti ti-book"></i> {{ $convertedLead->course?->title }}</span>
                                @endif
                                @if($convertedLead->register_number)
                                <span class="badge bg-success ms-2"><i class="ti ti-id"></i> {{ $convertedLead->register_number }}</span>
                                @endif
                            </div>
                            <div class="text-muted small">Application: {{ $convertedLead->studentDetails?->application_number ?? '-' }} • Converted: {{ $convertedLead->created_at?->format('d-m-Y') }}</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="tel:{{ $convertedLead->code }}{{ $convertedLead->phone }}" class="btn btn-outline-primary"><i class="ti ti-phone"></i> Call</a>
                        <a href="https://wa.me/{{ $convertedLead->code }}{{ $convertedLead->phone }}" target="_blank" class="btn btn-outline-success"><i class="ti ti-brand-whatsapp"></i> WhatsApp</a>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-lg-4">
                        <div class="card shadow-none border h-100">
                            <div class="card-body">
                                <h6 class="text-muted mb-3"><i class="ti ti-user-circle"></i> Student</h6>
                                <div class="mb-2"><span class="text-muted">Phone:</span> <span class="fw-medium">{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</span></div>
                                <div class="mb-2"><span class="text-muted">Email:</span> <span class="fw-medium">{{ $convertedLead->email ?? '-' }}</span></div>
                                <div class=""><span class="text-muted">DOB:</span> <span class="fw-medium">{{ $convertedLead->dob ? \Carbon\Carbon::parse($convertedLead->dob)->format('d-m-Y') : '-' }}</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card shadow-none border h-100">
                            <div class="card-body">
                                <h6 class="text-muted mb-3"><i class="ti ti-school"></i> Course</h6>
                                <div class="mb-2"><span class="text-muted">Subject:</span> <span class="fw-medium">{{ $convertedLead->subject?->title ?? '-' }}</span></div>
                                <div class="mb-2"><span class="text-muted">Batch:</span> <span class="fw-medium">{{ $convertedLead->batch?->title ?? '-' }}</span></div>
                                <div class=""><span class="text-muted">Admission Batch:</span> <span class="fw-medium">{{ $convertedLead->admissionBatch?->title ?? '-' }}</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card shadow-none border h-100">
                            <div class="card-body">
                                <h6 class="text-muted mb-3"><i class="ti ti-headphones"></i> Support Snapshot</h6>
                                
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-lg-6">
                        <div class="card shadow-none border h-100">
                            <div class="card-body">
                                <h6 class="text-muted mb-3"><i class="ti ti-brand-whatsapp"></i> Channels</h6>
                                <div class="mb-2"><span class="text-muted">APP:</span> <span class="fw-medium">{{ $convertedLead->supportDetails?->app ?? '-' }}</span></div>
                                <div class="mb-2"><span class="text-muted">WhatsApp Group:</span> <span class="fw-medium">{{ $convertedLead->supportDetails?->whatsapp_group ?? '-' }}</span></div>
                                <div class=""><span class="text-muted">Telegram Group:</span> <span class="fw-medium">{{ $convertedLead->supportDetails?->telegram_group ?? '-' }}</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card shadow-none border h-100">
                            <div class="card-body">
                                <h6 class="text-muted mb-3"><i class="ti ti-phone"></i> Contact & Issues</h6>
                                <div class="mb-2"><span class="text-muted">Call - 1:</span> <span class="fw-medium">{{ $convertedLead->supportDetails?->call_1 ?? '-' }}</span></div>
                                <div class=""><span class="text-muted">Problems:</span>
                                    <div class="mt-1 p-2 rounded border bg-light">{{ $convertedLead->supportDetails?->problems ?? '—' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


