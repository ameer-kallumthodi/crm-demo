@extends('layouts.mantis')

@section('title', 'NIOS Support - Converted Lead Details')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">NIOS Support - Converted Lead Details</h5>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end align-items-center gap-2">
                    <a href="{{ route('admin.support-nios-converted-leads.index') }}" class="btn btn-secondary">
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
                <div class="row g-4">
                    <div class="col-md-4">
                        <h6 class="text-muted">Student</h6>
                        <div><strong>Name:</strong> {{ $convertedLead->name }}</div>
                        <div><strong>Phone:</strong> {{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</div>
                        <div><strong>Email:</strong> {{ $convertedLead->email ?? '-' }}</div>
                        <div><strong>DOB:</strong> {{ $convertedLead->dob ? \Carbon\Carbon::parse($convertedLead->dob)->format('d-m-Y') : '-' }}</div>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted">Course</h6>
                        <div><strong>Course:</strong> {{ $convertedLead->course?->title ?? 'NIOS' }}</div>
                        <div><strong>Subject:</strong> {{ $convertedLead->subject?->title ?? '-' }}</div>
                        <div><strong>Batch:</strong> {{ $convertedLead->batch?->title ?? '-' }}</div>
                        <div><strong>Admission Batch:</strong> {{ $convertedLead->admissionBatch?->title ?? '-' }}</div>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted">Registration</h6>
                        <div><strong>Register No:</strong> {{ $convertedLead->register_number ?? '-' }}</div>
                        <div><strong>Application No:</strong> {{ $convertedLead->studentDetails?->application_number ?? '-' }}</div>
                        <div><strong>Converted At:</strong> {{ $convertedLead->created_at?->format('d-m-Y') }}</div>
                    </div>
                </div>

                <hr>

                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Support Status</h6>
                        <div><strong>Registration Status:</strong> {{ $convertedLead->supportDetails?->registration_status ?? '-' }}</div>
                        <div><strong>Technology Side:</strong> {{ $convertedLead->supportDetails?->technology_side ?? '-' }}</div>
                        <div><strong>Student Status:</strong> {{ $convertedLead->supportDetails?->student_status ?? '-' }}</div>
                        <div><strong>APP:</strong> {{ $convertedLead->supportDetails?->app ?? '-' }}</div>
                        <div><strong>WhatsApp Group:</strong> {{ $convertedLead->supportDetails?->whatsapp_group ?? '-' }}</div>
                        <div><strong>Telegram Group:</strong> {{ $convertedLead->supportDetails?->telegram_group ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Contacts</h6>
                        <div><strong>Call - 1:</strong> {{ $convertedLead->supportDetails?->call_1 ?? '-' }}</div>
                        <div><strong>Problems:</strong> {{ $convertedLead->supportDetails?->problems ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


