@extends('layouts.mantis')

@section('title', 'View Converted Lead')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Converted Lead Details</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.converted-leads.index') }}">Converted Leads</a></li>
                    <li class="breadcrumb-item">View</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Converted Lead Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Personal Information -->
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">Personal Information</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avtar avtar-s rounded-circle bg-light-success me-2" style="width: 60px; height: 60px;">
                                        <span class="text-white fw-bold" style="font-size: 1.5rem;">{{ strtoupper(substr($convertedLead->name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <h4 class="mb-1">{{ $convertedLead->name }}</h4>
                                        <p class="text-muted mb-0">Converted Lead</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted">Phone</label>
                                <p class="fw-bold">{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted">Email</label>
                                <p class="fw-bold">{{ $convertedLead->email ?? 'N/A' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted">Remarks</label>
                                <p class="fw-bold">{{ $convertedLead->remarks ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Information -->
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">Academic Information</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label text-muted">Course</label>
                                <p class="fw-bold">{{ $convertedLead->course ? $convertedLead->course->title : 'N/A' }}</p>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted">Academic Assistant</label>
                                <p class="fw-bold">{{ $convertedLead->academicAssistant ? $convertedLead->academicAssistant->name : 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Lead Information -->
                    @if($convertedLead->lead)
                    <div class="col-12">
                        <hr>
                        <h6 class="text-primary mb-3">Original Lead Information</h6>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label text-muted">Lead ID</label>
                                <p class="fw-bold">#{{ $convertedLead->lead->id }}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">Lead Name</label>
                                <p class="fw-bold">{{ $convertedLead->lead->title }}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">Lead Source</label>
                                <p class="fw-bold">{{ $convertedLead->lead->leadSource ? $convertedLead->lead->leadSource->title : 'N/A' }}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">Lead Status</label>
                                <p class="fw-bold">{{ $convertedLead->lead->leadStatus ? $convertedLead->lead->leadStatus->title : 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Conversion Information -->
                    <div class="col-12">
                        <hr>
                        <h6 class="text-primary mb-3">Conversion Information</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label text-muted">Converted By</label>
                                <p class="fw-bold">{{ $convertedLead->createdBy ? $convertedLead->createdBy->name : 'N/A' }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted">Converted Date</label>
                                <p class="fw-bold">{{ $convertedLead->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted">Last Updated</label>
                                <p class="fw-bold">{{ $convertedLead->updated_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <a href="{{ route('admin.converted-leads.index') }}" class="btn btn-secondary">
                            <i class="ti ti-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
@endsection
