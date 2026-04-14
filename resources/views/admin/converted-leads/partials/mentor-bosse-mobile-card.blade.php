                    <div class="card mb-3 {{ $convertedLead->is_cancelled ? 'cancelled-card' : '' }}">
                        <div class="card-body">
                            <!-- Lead Header -->
                            <div class="d-flex align-items-center mb-3">
                                <div class="avtar avtar-s rounded-circle bg-light-primary me-3 d-flex align-items-center justify-content-center">
                                    <span class="f-16 fw-bold text-primary">{{ strtoupper(substr($convertedLead->name, 0, 1)) }}</span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">{{ $convertedLead->name }}</h6>
                                    <small class="text-muted">ID: {{ $convertedLead->lead_id }}</small>
                                    @if($convertedLead->is_cancelled)
                                    <span class="badge bg-danger ms-2">Cancelled</span>
                                    @endif
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.converted-leads.show', $convertedLead->id) }}">
                                                <i class="ti ti-eye me-2"></i>View Details
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.invoices.index', $convertedLead->id) }}">
                                                <i class="ti ti-receipt me-2"></i>View Invoice
                                            </a>
                                        </li>
                                        @php
                                        $canManageCancelFlag = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor();
                                        @endphp
                                        @if($canManageCancelFlag)
                                        <li>
                                            <button type="button" class="dropdown-item js-cancel-flag" 
                                                data-cancel-url="{{ route('admin.converted-leads.cancel-flag', $convertedLead->id) }}"
                                                data-modal-title="Cancellation Confirmation">
                                                <i class="ti ti-ban me-2"></i>{{ $convertedLead->is_cancelled ? 'Update Cancellation' : 'Cancel' }}
                                            </button>
                                        </li>
                                        @endif
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_support_team())
                                        <li>
                                            <button type="button" class="dropdown-item update-register-btn"
                                                data-url="{{ route('admin.converted-leads.update-register-number-modal', $convertedLead->id) }}"
                                                data-title="Update Register Number">
                                                <i class="ti ti-edit me-2"></i>Update Register
                                            </button>
                                        </li>
                                        @php $courseChanged = (bool) ($convertedLead->is_course_changed ?? false); @endphp
                                        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                        <li>
                                            <button type="button" class="dropdown-item js-change-course-modal"
                                                data-modal-url="{{ route('admin.converted-leads.change-course-modal', $convertedLead->id) }}"
                                                data-modal-title="Change Course">
                                                <i class="ti ti-exchange me-2"></i>Change Course
                                            </button>
                                        </li>
                                        @endif
                                        @if($convertedLead->register_number)
                                        @php
                                        $idCard = \App\Models\ConvertedLeadIdCard::where('converted_lead_id', $convertedLead->id)->first();
                                        @endphp
                                        @if($idCard)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.converted-leads.id-card-view', $convertedLead->id) }}" target="_blank">
                                                <i class="ti ti-id me-2"></i>View ID Card
                                            </a>
                                        </li>
                                        @else
                                        <li>
                                            <form action="{{ route('admin.converted-leads.id-card-generate', $convertedLead->id) }}" method="post" style="display:inline-block" class="id-card-generate-form">
                                                @csrf
                                                <button type="submit" class="dropdown-item" data-loading-text="Generating...">
                                                    <i class="ti ti-id me-2"></i>Generate ID Card
                                                </button>
                                            </form>
                                        </li>
                                        @endif
                                        @endif
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            @php
                            $academicVerifiedAtMobile = $convertedLead->academic_verified_at
                            ? $convertedLead->academic_verified_at->copy()->timezone($appTimezone)->format('d-m-Y h:i A')
                            : null;
                            $supportVerifiedAtMobile = $convertedLead->support_verified_at
                            ? $convertedLead->support_verified_at->copy()->timezone($appTimezone)->format('d-m-Y h:i A')
                            : null;
                            @endphp

                            <!-- Lead Details -->
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <small class="text-muted d-block">Phone</small>
                                    <span class="fw-medium">{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">WhatsApp</small>
                                    <span class="fw-medium">
                                        @if($convertedLead->leadDetail && $convertedLead->leadDetail->whatsapp_number)
                                            {{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->whatsapp_code, $convertedLead->leadDetail->whatsapp_number) }}
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </div>
                                @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                                <div class="col-6">
                                    <small class="text-muted d-block">Parent Phone</small>
                                    <span class="fw-medium">
                                        @if($convertedLead->leadDetail && $convertedLead->leadDetail->parents_number)
                                            {{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->parents_code, $convertedLead->leadDetail->parents_number) }}
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </div>
                                @endif
                                <div class="col-6">
                                    <small class="text-muted d-block">Application Number</small>
                                    <span class="fw-medium">{{ $convertedLead->studentDetails?->application_number ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Registration Status</small>
                                    <span class="fw-medium">{{ $convertedLead->mentorDetails?->registration_status ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Student Status</small>
                                    <span class="fw-medium">{{ $convertedLead->mentorDetails?->student_status ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Converted Date</small>
                                    <span class="fw-medium">{{ $convertedLead->created_at->format('d-m-Y') }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Academic Verified At</small>
                                    <span class="fw-medium">{{ $academicVerifiedAtMobile ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Support Verified At</small>
                                    <span class="fw-medium">{{ $supportVerifiedAtMobile ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Subject</small>
                                    <span class="fw-medium">{{ $convertedLead->mentorDetails?->subject?->title ?? $convertedLead->subject?->title ?? 'N/A' }}</span>
                                </div>
                            </div>

                        </div>
                    </div>
