@extends('layouts.mantis')

@section('title', 'View Converted Lead')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Converted Lead Details</h5>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end align-items-center gap-3">
                    <ul class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.converted-leads.index') }}">Converted Leads</a></li>
                        <li class="breadcrumb-item">View</li>
                    </ul>
                    <a href="{{ route('admin.converted-leads.details-pdf', $convertedLead->id) }}" target="_blank" class="btn btn-outline-primary">
                        <i class="ti ti-file-type-pdf"></i> Download PDF
                    </a>
                    <a href="{{ route('admin.converted-leads.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0 d-flex align-items-center gap-2"><i class="ti ti-user-check text-primary"></i> Converted Lead Information</h5>
                <span class="badge bg-light-primary text-primary">ID #{{ $convertedLead->id }}</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Personal Information -->
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3 d-flex align-items-center gap-2"><i class="ti ti-address-book"></i> Personal Information</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avtar avtar-s rounded-circle bg-light-success me-2" style="width: 60px; height: 60px;">
                                        <span class="text-info fw-bold" style="font-size: 1.5rem;">{{ strtoupper(substr($convertedLead->name, 0, 1)) }}</span>
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
                                <label class="form-label text-muted">Register Number</label>
                                <p class="fw-bold">{{ $convertedLead->register_number ?? 'N/A' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted">DOB</label>
                                @php
                                    $dobDisplay = $convertedLead->dob ? (strtotime($convertedLead->dob) ? date('d-m-Y', strtotime($convertedLead->dob)) : $convertedLead->dob) : 'N/A';
                                @endphp
                                <p class="fw-bold">{{ $dobDisplay }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted">Remarks</label>
                                <p class="fw-bold">{{ $convertedLead->remarks ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Information -->
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3 d-flex align-items-center gap-2"><i class="ti ti-school"></i> Academic Information</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label text-muted">Course</label>
                                <p class="fw-bold">{{ $convertedLead->course ? $convertedLead->course->title : 'N/A' }}</p>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted">Batch</label>
                                <p class="fw-bold">{{ $convertedLead->batch ? $convertedLead->batch->title : 'N/A' }}</p>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted">Admission Batch</label>
                                <p class="fw-bold">{{ $convertedLead->admissionBatch ? $convertedLead->admissionBatch->title : 'N/A' }}</p>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted">Subject</label>
                                <p class="fw-bold">{{ $convertedLead->subject ? $convertedLead->subject->title : 'N/A' }}</p>
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
                        <h6 class="text-primary mb-3 d-flex align-items-center gap-2"><i class="ti ti-user"></i> Original Lead Information</h6>
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
                            <div class="col-md-3">
                                <label class="form-label text-muted">Interest Status</label>
                                <p class="fw-bold">
                                    @if($convertedLead->lead->interest_status)
                                        <span class="badge bg-{{ $convertedLead->lead->interest_status_color }}">{{ $convertedLead->lead->interest_status_label }}</span>
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Conversion Information -->
                    <div class="col-12">
                        <hr>
                        <h6 class="text-primary mb-3 d-flex align-items-center gap-2"><i class="ti ti-clipboard-check"></i> Conversion & Account Information</h6>
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
                            @php
                                $statusBadge = $convertedLead->status === 'Paid' ? 'success' : ($convertedLead->status === 'Admission cancel' ? 'danger' : 'secondary');
                                $regFeeBadge = $convertedLead->studentDetails?->reg_fee === 'Received' ? 'success' : ($convertedLead->studentDetails?->reg_fee ? 'warning' : 'secondary');
                                $examFeeBadge = $convertedLead->studentDetails?->exam_fee === 'Paid' ? 'success' : ($convertedLead->studentDetails?->exam_fee === 'Pending' ? 'warning' : ($convertedLead->studentDetails?->exam_fee ? 'danger' : 'secondary'));
                                $idCardBadge = $convertedLead->studentDetails?->id_card === 'download' ? 'success' : ($convertedLead->studentDetails?->id_card === 'processing' ? 'warning' : ($convertedLead->studentDetails?->id_card ? 'secondary' : 'secondary'));
                                $tmaBadge = $convertedLead->studentDetails?->tma === 'Uploaded' ? 'success' : ($convertedLead->studentDetails?->tma ? 'secondary' : 'secondary');
                            @endphp
                            <div class="col-md-3">
                                <label class="form-label text-muted">Username</label>
                                <p class="fw-bold"><span class="badge bg-light text-dark border">{{ $convertedLead->username ?? 'N/A' }}</span></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">Password</label>
                                <p class="fw-bold"><span class="badge bg-light text-dark border">{{ $convertedLead->password ?? 'N/A' }}</span></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">Status</label>
                                <p class="fw-bold"><span class="badge bg-{{ $statusBadge }}">{{ $convertedLead->status ?? 'N/A' }}</span></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">ID Card</label>
                                <p class="fw-bold"><span class="badge bg-{{ $idCardBadge }} text-uppercase">{{ $convertedLead->studentDetails?->id_card ?? 'N/A' }}</span></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">REG. FEE</label>
                                <p class="fw-bold"><span class="badge bg-{{ $regFeeBadge }}">{{ $convertedLead->studentDetails?->reg_fee ?? 'N/A' }}</span></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">EXAM FEE</label>
                                <p class="fw-bold"><span class="badge bg-{{ $examFeeBadge }}">{{ $convertedLead->studentDetails?->exam_fee ?? 'N/A' }}</span></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">Ref No</label>
                                <p class="fw-bold"><span class="badge bg-light text-dark border">{{ $convertedLead->ref_no ?? 'N/A' }}</span></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">Enroll No</label>
                                <p class="fw-bold"><span class="badge bg-light text-dark border">{{ $convertedLead->studentDetails?->enroll_no ?? 'N/A' }}</span></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">TMA</label>
                                <p class="fw-bold"><span class="badge bg-{{ $tmaBadge }}">{{ $convertedLead->studentDetails?->tma ?? 'N/A' }}</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Course-Specific Information -->
                    @if($convertedLead->studentDetails)
                    <div class="col-12">
                        <hr>
                        <h6 class="text-primary mb-3 d-flex align-items-center gap-2"><i class="ti ti-school"></i> Course-Specific Information</h6>
                        <div class="row g-3">
                            @if($convertedLead->course_id == 16) {{-- GMVSS --}}
                                <div class="col-md-3">
                                    <label class="form-label text-muted">Registration Link</label>
                                    <p class="fw-bold">{{ $convertedLead->studentDetails->registrationLink?->title ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-muted">Certificate Status</label>
                                    <p class="fw-bold"><span class="badge bg-info">{{ $convertedLead->studentDetails->certificate_status ?? 'N/A' }}</span></p>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-muted">Certificate Received Date</label>
                                    <p class="fw-bold">{{ $convertedLead->studentDetails->certificate_received_date ? $convertedLead->studentDetails->certificate_received_date->format('d-m-Y') : 'N/A' }}</p>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-muted">Certificate Issued Date</label>
                                    <p class="fw-bold">{{ $convertedLead->studentDetails->certificate_issued_date ? $convertedLead->studentDetails->certificate_issued_date->format('d-m-Y') : 'N/A' }}</p>
                                </div>
                            @endif
                            
                            @if($convertedLead->studentDetails->registration_number)
                            <div class="col-md-3">
                                <label class="form-label text-muted">Registration Number</label>
                                <p class="fw-bold">{{ $convertedLead->studentDetails->registration_number }}</p>
                            </div>
                            @endif
                            
                            @if($convertedLead->studentDetails->enrollment_number)
                            <div class="col-md-3">
                                <label class="form-label text-muted">Enrollment Number</label>
                                <p class="fw-bold">{{ $convertedLead->studentDetails->enrollment_number }}</p>
                            </div>
                            @endif
                            
                            @if($convertedLead->studentDetails->converted_date)
                            <div class="col-md-3">
                                <label class="form-label text-muted">Converted Date</label>
                                <p class="fw-bold">{{ $convertedLead->studentDetails->converted_date->format('d-m-Y') }}</p>
                            </div>
                            @endif
                            
                            @if($convertedLead->studentDetails->remarks)
                            <div class="col-12">
                                <label class="form-label text-muted">Remarks</label>
                                <p class="fw-bold">{{ $convertedLead->studentDetails->remarks }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($convertedLead->leadDetail)
                    <div class="col-12 mt-4">
                        <hr>
                        <h6 class="text-primary mb-3">Lead Details</h6>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label text-muted">Father's Name</label>
                                <p class="fw-bold">{{ $convertedLead->leadDetail->father_name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">Mother's Name</label>
                                <p class="fw-bold">{{ $convertedLead->leadDetail->mother_name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">Date of Birth</label>
                                <p class="fw-bold">{{ $convertedLead->leadDetail->date_of_birth ? $convertedLead->leadDetail->date_of_birth->format('d M Y') : 'N/A' }}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">Second Language</label>
                                <p class="fw-bold">{{ $convertedLead->leadDetail->second_language ?? 'N/A' }}</p>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label text-muted">Personal Phone</label>
                                <p class="fw-bold">{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->personal_code, $convertedLead->leadDetail->personal_number) }}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">Parent Phone</label>
                                <p class="fw-bold">{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->parents_code, $convertedLead->leadDetail->parents_number) }}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">WhatsApp</label>
                                <p class="fw-bold">{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->whatsapp_code, $convertedLead->leadDetail->whatsapp_number) }}</p>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label text-muted">Batch</label>
                                <p class="fw-bold">{{ optional($convertedLead->leadDetail->batch)->title ?? 'N/A' }}</p>
                            </div>

                            <div class="col-12">
                                <h6 class="text-primary mt-2">Uploaded Documents</h6>
                            </div>
                            @php
                                $doc = $convertedLead->leadDetail;
                                $files = [
                                    'passport_photo' => 'Passport Photo',
                                    'adhar_front' => 'Aadhar Front',
                                    'adhar_back' => 'Aadhar Back',
                                    'signature' => 'Signature',
                                    'birth_certificate' => 'Birth Certificate',
                                    'sslc_certificate' => 'SSLC Certificate',
                                    'plustwo_certificate' => 'Plus Two Certificate',
                                ];
                            @endphp
                            @foreach($files as $field => $label)
                                <div class="col-md-3">
                                    <label class="form-label text-muted">{{ $label }}</label>
                                    @php
                                        $path = $doc->$field ?? null;
                                        $exists = $path ? \Illuminate\Support\Facades\Storage::disk('public')->exists($path) : false;
                                        $fileUrl = $exists ? asset('storage/' . $path) : null;
                                        $isPdf = $exists ? \Illuminate\Support\Str::endsWith(strtolower($path), '.pdf') : false;
                                    @endphp
                                    <div class="card p-2">
                                        @if($exists)
                                            @if($isPdf)
                                                <div class="text-center mb-2">
                                                    <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                                </div>
                                                <div class="d-grid gap-1">
                                                    <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-eye me-1"></i> View PDF
                                                    </a>
                                                    <a href="{{ $fileUrl }}" download class="btn btn-sm btn-outline-secondary">
                                                        <i class="ti ti-download me-1"></i> Download
                                                    </a>
                                                </div>
                                            @else
                                                <a href="{{ $fileUrl }}" target="_blank" class="d-block text-center mb-2">
                                                    <img src="{{ $fileUrl }}" alt="{{ $label }}" style="max-width: 100%; max-height: 140px; object-fit: contain;" onerror="this.onerror=null;this.src='{{ asset('assets/img/file.png') }}';">
                                                </a>
                                                <div class="d-grid">
                                                    <a href="{{ $fileUrl }}" download class="btn btn-sm btn-outline-secondary">
                                                        <i class="ti ti-download me-1"></i> Download
                                                    </a>
                                                </div>
                                            @endif
                                        @else
                                            <div class="text-center text-muted py-4">
                                                <i class="ti ti-file-alert f-24 d-block mb-1"></i>
                                                <small>File not found</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <!-- Lead Activities History -->
    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Lead Activities History</h5>
            </div>
            <div class="card-body">
                @if($leadActivities->count() > 0)
                    <div class="timeline">
                        @foreach($leadActivities as $activity)
                        <div class="timeline-item">
                            <div class="timeline-marker">
                                <div class="avtar avtar-s rounded-circle bg-light-{{ $activity->activity_type === 'converted' ? 'success' : 'primary' }}">
                                    <i class="ti ti-{{ $activity->activity_type === 'converted' ? 'check' : 'activity' }} f-16"></i>
                                </div>
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}</h6>
                                        <p class="mb-1 text-muted">{{ $activity->description }}</p>
                                        @if($activity->reason)
                                            <p class="mb-1"><strong>Reason:</strong> <span class="badge bg-info">{{ $activity->formatted_reason }}</span></p>
                                        @endif
                                        @if($activity->rating)
                                            <p class="mb-1"><strong>Rating:</strong> <span class="badge bg-success">{{ $activity->rating }}/10</span></p>
                                        @endif
                                        @if($activity->lead_status_id == 2 && $activity->followup_date)
                                            <p class="mb-1"><strong>Followup Date:</strong> <span class="badge bg-warning">{{ $activity->followup_date->format('d M Y') }}</span></p>
                                        @endif
                                        @if($activity->remarks)
                                            <p class="mb-1"><small class="text-info">{{ $activity->remarks }}</small></p>
                                        @endif
                                        @if($activity->leadStatus)
                                            <span class="badge bg-light-{{ \App\Helpers\StatusHelper::getLeadStatusColor($activity->leadStatus->id) }}">
                                                {{ $activity->leadStatus->title }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">{{ $activity->created_at->format('M d, Y h:i A') }}</small>
                                        @if($activity->createdBy)
                                            <p class="mb-0"><small class="text-muted">by {{ $activity->createdBy->name }}</small></p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="ti ti-activity f-48 mb-3"></i>
                        <h6>No Activities Found</h6>
                        <p class="mb-0">No activities have been recorded for this lead.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    z-index: 1;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #7366ff;
    margin-left: 10px;
}

.timeline-item:last-child .timeline::before {
    display: none;
}
</style>
@endpush
