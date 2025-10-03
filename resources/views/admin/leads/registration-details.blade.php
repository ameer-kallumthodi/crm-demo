@extends('layouts.mantis')

@section('title', 'Registration Details - ' . $lead->title)

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">
                        <i class="ti ti-user-check me-2 text-primary"></i>Registration Details
                    </h5>
                    <p class="m-b-0 text-muted">{{ $lead->title }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('leads.index') }}">Leads</a></li>
                    <li class="breadcrumb-item active">Registration Details</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

@if(isset($error))
<!-- [ Error Alert ] start -->
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <div class="d-flex align-items-center">
        <i class="ti ti-alert-circle me-2"></i>
        <div class="flex-grow-1">
            <strong>Error:</strong> {{ $error }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<!-- [ Error Alert ] end -->
@endif

@if($studentDetail)

<div class="row registration-details-container">
    <!-- Lead Information Card -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-gradient-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="ti ti-user me-2"></i>Lead Information
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="avtar avtar-xl rounded-circle bg-light-primary mx-auto mb-3">
                        <span class="f-24 fw-bold text-primary">{{ strtoupper(substr($lead->title, 0, 1)) }}</span>
                    </div>
                    <h5 class="mb-1">{{ $lead->title }}</h5>
                    <p class="text-muted mb-0">{{ $lead->leadSource->title ?? 'N/A' }}</p>
                </div>
                
                <div class="row g-3">
                    <div class="col-12">
                        <div class="d-flex align-items-center p-2 bg-light rounded">
                            <i class="ti ti-phone text-primary me-3"></i>
                            <div>
                                <small class="text-muted d-block">Phone</small>
                                <span class="fw-medium">{{ \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center p-2 bg-light rounded">
                            <i class="ti ti-mail text-primary me-3"></i>
                            <div>
                                <small class="text-muted d-block">Email</small>
                                <span class="fw-medium">{{ $lead->email ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center p-2 bg-light rounded">
                            <i class="ti ti-book text-primary me-3"></i>
                            <div>
                                <small class="text-muted d-block">Course</small>
                                <span class="fw-medium">{{ $lead->course->title ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center p-2 bg-light rounded">
                            <i class="ti ti-user text-primary me-3"></i>
                            <div>
                                <small class="text-muted d-block">Telecaller</small>
                                <span class="fw-medium">{{ $lead->telecaller->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center p-2 bg-light rounded">
                            <i class="ti ti-calendar text-primary me-3"></i>
                            <div>
                                <small class="text-muted d-block">Created</small>
                                <span class="fw-medium">{{ $lead->created_at->format('M d, Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Registration Details Card -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-gradient-success text-white">
                <h5 class="card-title mb-0">
                    <i class="ti ti-file-text me-2"></i>Registration Details
                </h5>
            </div>
            <div class="card-body">
                <!-- Navigation Tabs -->
                <ul class="nav nav-tabs nav-fill mb-4" id="registrationTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab">
                            <i class="ti ti-user me-2"></i>Personal Info
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab">
                            <i class="ti ti-phone me-2"></i>Contact Info
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="address-tab" data-bs-toggle="tab" data-bs-target="#address" type="button" role="tab">
                            <i class="ti ti-map-pin me-2"></i>Address
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="course-tab" data-bs-toggle="tab" data-bs-target="#course" type="button" role="tab">
                            <i class="ti ti-book me-2"></i>Course Info
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab">
                            <i class="ti ti-file me-2"></i>Documents
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="registrationTabsContent">
                    <!-- Personal Information Tab -->
                    <div class="tab-pane fade show active" id="personal" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-user text-primary"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Student Name</label>
                                        <p class="info-value">{{ $studentDetail->student_name }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-user-check text-success"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Father Name</label>
                                        <p class="info-value">{{ $studentDetail->father_name }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-user-heart text-info"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Mother Name</label>
                                        <p class="info-value">{{ $studentDetail->mother_name }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-calendar text-warning"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Date of Birth</label>
                                        <p class="info-value">{{ $studentDetail->date_of_birth ? $studentDetail->date_of_birth->format('M d, Y') : 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information Tab -->
                    <div class="tab-pane fade" id="contact" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-mail text-primary"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Email</label>
                                        <p class="info-value">{{ $studentDetail->email }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-phone text-success"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Personal Phone</label>
                                        <p class="info-value">{{ \App\Helpers\PhoneNumberHelper::display($studentDetail->personal_code, $studentDetail->personal_number) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-phone-call text-info"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Parents Phone</label>
                                        <p class="info-value">{{ \App\Helpers\PhoneNumberHelper::display($studentDetail->parents_code, $studentDetail->parents_number) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-brand-whatsapp text-success"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">WhatsApp</label>
                                        <p class="info-value">{{ \App\Helpers\PhoneNumberHelper::display($studentDetail->whatsapp_code, $studentDetail->whatsapp_number) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Address Information Tab -->
                    <div class="tab-pane fade" id="address" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-map-pin text-primary"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Street Address</label>
                                        <p class="info-value">{{ $studentDetail->street }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-building text-success"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Locality</label>
                                        <p class="info-value">{{ $studentDetail->locality }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-mailbox text-info"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Post Office</label>
                                        <p class="info-value">{{ $studentDetail->post_office }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-map text-warning"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">District</label>
                                        <p class="info-value">{{ $studentDetail->district }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-flag text-danger"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">State</label>
                                        <p class="info-value">{{ $studentDetail->state }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-hash text-secondary"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Pin Code</label>
                                        <p class="info-value">{{ $studentDetail->pin_code }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Course Information Tab -->
                    <div class="tab-pane fade" id="course" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-book text-primary"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Course</label>
                                        <p class="info-value">{{ $studentDetail->course->title ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-bookmark text-success"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Subject</label>
                                        <p class="info-value">{{ $studentDetail->subject->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-users text-info"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">Batch</label>
                                        <p class="info-value">{{ $studentDetail->batch->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                            @if($studentDetail->ug_pg_selection)
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="ti ti-graduation-cap text-warning"></i>
                                    </div>
                                    <div class="info-content">
                                        <label class="info-label">UG/PG Selection</label>
                                        <p class="info-value">{{ ucfirst($studentDetail->ug_pg_selection) }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Documents Tab -->
                    <div class="tab-pane fade" id="documents" role="tabpanel">
                        <div class="row g-3">
                            @if($studentDetail->sslc_certificate)
                            <div class="col-md-6">
                                <div class="document-card">
                                    <div class="document-icon">
                                        <i class="ti ti-file-certificate text-primary"></i>
                                    </div>
                                    <div class="document-content">
                                        <div class="document-info">
                                            <label class="document-label">SSLC Certificate</label>
                                            <div class="verification-status">
                                                <span class="badge bg-{{ $studentDetail->sslc_verification_status === 'verified' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($studentDetail->sslc_verification_status ?? 'pending') }}
                                                </span>
                                                @if($studentDetail->sslc_verified_at)
                                                <small class="text-muted d-block">
                                                    Verified by: {{ $studentDetail->sslcVerifiedBy->name ?? 'Unknown' }}<br>
                                                    Date: {{ $studentDetail->sslc_verified_at->format('M d, Y H:i') }}
                                                </small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            <a href="{{ Storage::url($studentDetail->sslc_certificate) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="ti ti-eye me-1"></i>View
                                            </a>
                                            @if(is_telecaller()) {{-- Telecaller or post-sales --}}
                                            <button class="btn btn-sm btn-success" onclick="openVerificationModal('sslc_certificate', '{{ $studentDetail->sslc_verification_status }}')">
                                                <i class="ti ti-check me-1"></i>Verify
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            @if($studentDetail->plustwo_certificate || $studentDetail->plus_two_certificate)
                            <div class="col-md-6">
                                <div class="document-card">
                                    <div class="document-icon">
                                        <i class="ti ti-file-certificate text-success"></i>
                                    </div>
                                    <div class="document-content">
                                        <div class="document-info">
                                            <label class="document-label">Plus Two Certificate</label>
                                            <div class="verification-status">
                                                @php
                                                    $certificateField = $studentDetail->plustwo_certificate ? 'plustwo' : 'plus_two';
                                                    $verificationStatus = $studentDetail->{$certificateField . '_verification_status'} ?? 'pending';
                                                    $verifiedAt = $studentDetail->{$certificateField . '_verified_at'};
                                                    $verifiedBy = $studentDetail->{ucfirst($certificateField) . 'VerifiedBy'} ?? null;
                                                @endphp
                                                <span class="badge bg-{{ $verificationStatus === 'verified' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($verificationStatus) }}
                                                </span>
                                                @if($verifiedAt)
                                                <small class="text-muted d-block">
                                                    Verified by: {{ $verifiedBy->name ?? 'Unknown' }}<br>
                                                    Date: {{ $verifiedAt->format('M d, Y H:i') }}
                                                </small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            <a href="{{ Storage::url($studentDetail->plustwo_certificate ?? $studentDetail->plus_two_certificate) }}" target="_blank" class="btn btn-sm btn-outline-success">
                                                <i class="ti ti-eye me-1"></i>View
                                            </a>
                                            @if(is_telecaller()) {{-- Telecaller or post-sales --}}
                                            <button class="btn btn-sm btn-success" onclick="openVerificationModal('{{ $certificateField }}_certificate', '{{ $verificationStatus }}')">
                                                <i class="ti ti-check me-1"></i>Verify
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            @if($studentDetail->ug_certificate)
                            <div class="col-md-6">
                                <div class="document-card">
                                    <div class="document-icon">
                                        <i class="ti ti-file-certificate text-info"></i>
                                    </div>
                                    <div class="document-content">
                                        <div class="document-info">
                                            <label class="document-label">UG Certificate</label>
                                            <div class="verification-status">
                                                <span class="badge bg-{{ $studentDetail->ug_verification_status === 'verified' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($studentDetail->ug_verification_status ?? 'pending') }}
                                                </span>
                                                @if($studentDetail->ug_verified_at)
                                                <small class="text-muted d-block">
                                                    Verified by: {{ $studentDetail->ugVerifiedBy->name ?? 'Unknown' }}<br>
                                                    Date: {{ $studentDetail->ug_verified_at->format('M d, Y H:i') }}
                                                </small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            <a href="{{ Storage::url($studentDetail->ug_certificate) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                                <i class="ti ti-eye me-1"></i>View
                                            </a>
                                            @if(is_telecaller()) {{-- Telecaller or post-sales --}}
                                            <button class="btn btn-sm btn-success" onclick="openVerificationModal('ug_certificate', '{{ $studentDetail->ug_verification_status }}')">
                                                <i class="ti ti-check me-1"></i>Verify
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            @if($studentDetail->birth_certificate)
                            <div class="col-md-6">
                                <div class="document-card">
                                    <div class="document-icon">
                                        <i class="ti ti-file-certificate text-info"></i>
                                    </div>
                                    <div class="document-content">
                                        <div class="document-info">
                                            <label class="document-label">Birth Certificate</label>
                                            <div class="verification-status">
                                                @if(isset($studentDetail->birth_certificate_verification_status))
                                                <span class="badge bg-{{ $studentDetail->birth_certificate_verification_status === 'verified' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($studentDetail->birth_certificate_verification_status) }}
                                                </span>
                                                @if($studentDetail->birth_certificate_verified_at)
                                                <small class="text-muted d-block">
                                                    Verified by: {{ $studentDetail->birthCertificateVerifiedBy->name ?? 'Unknown' }}<br>
                                                    Date: {{ $studentDetail->birth_certificate_verified_at->format('M d, Y H:i') }}
                                                </small>
                                                @endif
                                                @else
                                                <span class="badge bg-secondary">Not Required</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            <a href="{{ Storage::url($studentDetail->birth_certificate) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                                <i class="ti ti-eye me-1"></i>View
                                            </a>
                                            @if(is_telecaller() && isset($studentDetail->birth_certificate_verification_status)) {{-- Telecaller or post-sales --}}
                                            <button class="btn btn-sm btn-success" onclick="openVerificationModal('birth_certificate', '{{ $studentDetail->birth_certificate_verification_status }}')">
                                                <i class="ti ti-check me-1"></i>Verify
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            <div class="col-md-6">
                                <div class="document-card">
                                    <div class="document-icon">
                                        <i class="ti ti-photo text-warning"></i>
                                    </div>
                                    <div class="document-content">
                                        <div class="document-info">
                                            <label class="document-label">Passport Photo</label>
                                            <div class="verification-status">
                                                <span class="badge bg-{{ $studentDetail->passport_photo_verification_status === 'verified' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($studentDetail->passport_photo_verification_status ?? 'pending') }}
                                                </span>
                                                @if($studentDetail->passport_photo_verified_at)
                                                <small class="text-muted d-block">
                                                    Verified by: {{ $studentDetail->passportPhotoVerifiedBy->name ?? 'Unknown' }}<br>
                                                    Date: {{ $studentDetail->passport_photo_verified_at->format('M d, Y H:i') }}
                                                </small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            <a href="{{ Storage::url($studentDetail->passport_photo) }}" target="_blank" class="btn btn-sm btn-outline-warning">
                                                <i class="ti ti-eye me-1"></i>View
                                            </a>
                                            @if(is_telecaller()) {{-- Telecaller or post-sales --}}
                                            <button class="btn btn-sm btn-success" onclick="openVerificationModal('passport_photo', '{{ $studentDetail->passport_photo_verification_status }}')">
                                                <i class="ti ti-check me-1"></i>Verify
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="document-card">
                                    <div class="document-icon">
                                        <i class="ti ti-id text-danger"></i>
                                    </div>
                                    <div class="document-content">
                                        <div class="document-info">
                                            <label class="document-label">Aadhar Front</label>
                                            <div class="verification-status">
                                                <span class="badge bg-{{ $studentDetail->adhar_front_verification_status === 'verified' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($studentDetail->adhar_front_verification_status ?? 'pending') }}
                                                </span>
                                                @if($studentDetail->adhar_front_verified_at)
                                                <small class="text-muted d-block">
                                                    Verified by: {{ $studentDetail->adharFrontVerifiedBy->name ?? 'Unknown' }}<br>
                                                    Date: {{ $studentDetail->adhar_front_verified_at->format('M d, Y H:i') }}
                                                </small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            <a href="{{ Storage::url($studentDetail->adhar_front) }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                                <i class="ti ti-eye me-1"></i>View
                                            </a>
                                            @if(is_telecaller()) {{-- Telecaller or post-sales --}}
                                            <button class="btn btn-sm btn-success" onclick="openVerificationModal('adhar_front', '{{ $studentDetail->adhar_front_verification_status }}')">
                                                <i class="ti ti-check me-1"></i>Verify
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="document-card">
                                    <div class="document-icon">
                                        <i class="ti ti-id text-secondary"></i>
                                    </div>
                                    <div class="document-content">
                                        <div class="document-info">
                                            <label class="document-label">Aadhar Back</label>
                                            <div class="verification-status">
                                                <span class="badge bg-{{ $studentDetail->adhar_back_verification_status === 'verified' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($studentDetail->adhar_back_verification_status ?? 'pending') }}
                                                </span>
                                                @if($studentDetail->adhar_back_verified_at)
                                                <small class="text-muted d-block">
                                                    Verified by: {{ $studentDetail->adharBackVerifiedBy->name ?? 'Unknown' }}<br>
                                                    Date: {{ $studentDetail->adhar_back_verified_at->format('M d, Y H:i') }}
                                                </small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            <a href="{{ Storage::url($studentDetail->adhar_back) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                <i class="ti ti-eye me-1"></i>View
                                            </a>
                                            @if(is_telecaller()) {{-- Telecaller or post-sales --}}
                                            <button class="btn btn-sm btn-success" onclick="openVerificationModal('adhar_back', '{{ $studentDetail->adhar_back_verification_status }}')">
                                                <i class="ti ti-check me-1"></i>Verify
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="document-card">
                                    <div class="document-icon">
                                        <i class="ti ti-signature text-dark"></i>
                                    </div>
                                    <div class="document-content">
                                        <div class="document-info">
                                            <label class="document-label">Signature</label>
                                            <div class="verification-status">
                                                <span class="badge bg-{{ $studentDetail->signature_verification_status === 'verified' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($studentDetail->signature_verification_status ?? 'pending') }}
                                                </span>
                                                @if($studentDetail->signature_verified_at)
                                                <small class="text-muted d-block">
                                                    Verified by: {{ $studentDetail->signatureVerifiedBy->name ?? 'Unknown' }}<br>
                                                    Date: {{ $studentDetail->signature_verified_at->format('M d, Y H:i') }}
                                                </small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            <a href="{{ Storage::url($studentDetail->signature) }}" target="_blank" class="btn btn-sm btn-outline-dark">
                                                <i class="ti ti-eye me-1"></i>View
                                            </a>
                                            @if(is_telecaller()) {{-- Telecaller or post-sales --}}
                                            <button class="btn btn-sm btn-success" onclick="openVerificationModal('signature', '{{ $studentDetail->signature_verification_status }}')">
                                                <i class="ti ti-check me-1"></i>Verify
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($studentDetail->message)
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="ti ti-message me-2"></i>Additional Message
                            </h6>
                            <p class="mb-0">{{ $studentDetail->message }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">Registration Status</h6>
                        <span class="badge bg-{{ $studentDetail->status === 'approved' ? 'success' : ($studentDetail->status === 'rejected' ? 'danger' : 'warning') }} fs-6">
                            {{ ucfirst($studentDetail->status ?? 'pending') }}
                        </span>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-arrow-left me-2"></i>Back to Leads
                        </a>
                        @if(!is_telecaller()) {{-- Only admin can approve/reject --}}
                            @if($studentDetail->status !== 'approved')
                            <button class="btn btn-success" onclick="updateStatus('approved')">
                                <i class="ti ti-check me-2"></i>Approve
                            </button>
                            @endif
                            @if($studentDetail->status !== 'rejected')
                            <button class="btn btn-danger" onclick="updateStatus('rejected')">
                                <i class="ti ti-x me-2"></i>Reject
                            </button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Document Verification Modal -->
<div class="modal fade" id="verificationModal" tabindex="-1" aria-labelledby="verificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verificationModalLabel">Document Verification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="verificationForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="lead_detail_id" name="lead_detail_id" value="{{ $studentDetail->id }}">
                    <input type="hidden" id="document_type" name="document_type">
                    
                    <div class="mb-3">
                        <label for="verification_status" class="form-label">Verification Status</label>
                        <select class="form-select" id="verification_status" name="verification_status" required>
                            <option value="pending">Pending</option>
                            <option value="verified">Verified</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="need_to_change_document" name="need_to_change_document">
                            <label class="form-check-label" for="need_to_change_document">
                                Need to change document
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3" id="file_upload_section" style="display: none;">
                        <label for="new_file" class="form-label">Upload New File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="new_file" name="new_file" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Upload a new file (Max 1MB)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Verification</button>
                </div>
            </form>
        </div>
    </div>
</div>

@else
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="ti ti-file-x f-48 text-muted mb-3"></i>
                <h5 class="text-muted">No Registration Details Found</h5>
                <p class="text-muted">This lead has not submitted any registration form yet.</p>
                <a href="{{ route('leads.index') }}" class="btn btn-primary">
                    <i class="ti ti-arrow-left me-2"></i>Back to Leads
                </a>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('styles')
<style>
    .info-card {
        display: flex;
        align-items: center;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 10px;
        border-left: 4px solid #007bff;
        transition: all 0.3s ease;
    }
    
    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .info-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: rgba(0,123,255,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        flex-shrink: 0;
    }
    
    .info-icon i {
        font-size: 1.5rem;
    }
    
    .info-content {
        flex-grow: 1;
    }
    
    .info-label {
        font-size: 0.875rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
        font-weight: 500;
    }
    
    .info-value {
        font-size: 1rem;
        color: #212529;
        margin-bottom: 0;
        font-weight: 600;
    }
    
    .document-card {
        display: flex;
        align-items: center;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 10px;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .document-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border-color: #007bff;
    }
    
    .document-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: rgba(0,123,255,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        flex-shrink: 0;
    }
    
    .document-icon i {
        font-size: 1.5rem;
    }
    
    .document-content {
        flex-grow: 1;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .document-info {
        flex-grow: 1;
    }
    
    .document-label {
        font-size: 0.875rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }
    
    .verification-status {
        margin-bottom: 0.5rem;
    }
    
    .document-actions {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        flex-shrink: 0;
    }
    
    .registration-details-container .nav-tabs .nav-link {
        border: none;
        border-radius: 10px 10px 0 0;
        margin-right: 0.25rem;
        background: #f8f9fa;
        color: #6c757d;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .registration-details-container .nav-tabs .nav-link:hover {
        background: #e9ecef;
        color: #495057;
    }
    
    .registration-details-container .nav-tabs .nav-link.active {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }
    
    .tab-content {
        background: white;
        border-radius: 0 10px 10px 10px;
        padding: 1.5rem;
        min-height: 400px;
    }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    }
    
    .bg-gradient-success {
        background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
    }
    
    .card {
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    @media (max-width: 768px) {
        .info-card, .document-card {
            flex-direction: column;
            text-align: center;
        }
        
        .info-icon, .document-icon {
            margin-right: 0;
            margin-bottom: 1rem;
        }
        
        .document-content {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .nav-tabs {
            flex-wrap: wrap;
        }
        
        .nav-tabs .nav-link {
            margin-bottom: 0.25rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
// Handle tab navigation on page load
document.addEventListener('DOMContentLoaded', function() {
    // Try URL parameter first, then localStorage
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab') || localStorage.getItem('activeTab');
    
    if (tabParam) {
        // Wait a bit for Bootstrap to be fully loaded
        setTimeout(() => {
            const tabElement = document.querySelector(`[href="#${tabParam}"]`);
            if (tabElement) {
                // Remove active class from all tabs
                document.querySelectorAll('.registration-details-container .nav-link').forEach(tab => {
                    tab.classList.remove('active');
                });
                document.querySelectorAll('.registration-details-container .tab-pane').forEach(pane => {
                    pane.classList.remove('active', 'show');
                });
                
                // Activate the target tab
                tabElement.classList.add('active');
                const targetPane = document.querySelector(tabElement.getAttribute('href'));
                if (targetPane) {
                    targetPane.classList.add('active', 'show');
                }
            }
        }, 100);
    }
    
    // Store active tab when user clicks on tabs
    document.querySelectorAll('.registration-details-container .nav-link[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('click', function() {
            const tabId = this.getAttribute('href').substring(1);
            localStorage.setItem('activeTab', tabId);
        });
    });
});

function updateStatus(status) {
    if (confirm(`Are you sure you want to ${status} this registration?`)) {
        // Add your status update logic here
        console.log('Updating status to:', status);
        // You can implement AJAX call to update the status
    }
}

function openVerificationModal(documentType, currentStatus) {
    document.getElementById('document_type').value = documentType;
    document.getElementById('verification_status').value = currentStatus || 'pending';
    document.getElementById('need_to_change_document').checked = false;
    document.getElementById('new_file').value = '';
    document.getElementById('file_upload_section').style.display = 'none';
    
    const modal = new bootstrap.Modal(document.getElementById('verificationModal'));
    modal.show();
}

// Handle checkbox change
document.getElementById('need_to_change_document').addEventListener('change', function() {
    const fileUploadSection = document.getElementById('file_upload_section');
    const newFileInput = document.getElementById('new_file');
    
    if (this.checked) {
        fileUploadSection.style.display = 'block';
        newFileInput.required = true;
    } else {
        fileUploadSection.style.display = 'none';
        newFileInput.required = false;
        newFileInput.value = '';
    }
});

// Handle verification form submission
document.getElementById('verificationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const needToChangeDocument = document.getElementById('need_to_change_document').checked;
    const newFile = document.getElementById('new_file');
    
    // Validate file upload requirement
    if (needToChangeDocument && !newFile.files.length) {
        showAlert('Please upload a new file when "Need to change document" is checked.', 'danger');
        return;
    }
    
    const formData = new FormData(this);
    
    fetch('{{ route("leads.update-document-verification") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showAlert(data.message, 'success');
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('verificationModal'));
            modal.hide();
            
            // Reload page to show updated verification status
            setTimeout(() => {
                // Get current active tab and store it
                const activeTab = document.querySelector('.registration-details-container .nav-link.active');
                const activeTabId = activeTab ? activeTab.getAttribute('href').substring(1) : 'personal-info';
                localStorage.setItem('activeTab', activeTabId);
                
                // Reload the page
                window.location.reload();
            }, 1500);
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while updating verification.', 'danger');
    });
});

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="ti ti-${type === 'success' ? 'check-circle' : 'alert-circle'} me-2"></i>
            <div class="flex-grow-1">
                <strong>${type === 'success' ? 'Success:' : 'Error:'}</strong> ${message}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Insert at the top of the page
    const container = document.querySelector('.page-header').parentNode;
    container.insertBefore(alertDiv, container.firstChild);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
@endpush