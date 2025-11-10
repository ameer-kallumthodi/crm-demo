@extends('layouts.mantis')

@section('title', 'D2D SKILL PARK - Marketing Form')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">D2D SKILL PARK</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.marketing.index') }}">Marketing</a></li>
                    <li class="breadcrumb-item">D2D Form</li>
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
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">D2D SKILL PARK</h5>
                    <a href="{{ route('admin.marketing.marketing-leads') }}" class="btn btn-secondary btn-sm">
                        <i class="ti ti-arrow-left"></i> Back to Marketing Leads
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <p class="text-muted">
                        A door-to-door marketing campaign for Skill Park is a community-based outreach initiative designed to promote the institute's training programs and skill development courses directly to potential students and families. In this campaign, Skill Park representatives visit homes, schools, and local areas to create awareness about various courses, government-certified programs, and career opportunities offered by the institution. The team explains the benefits of skill-based education, distributes brochures, collects lead information, and answers queries face-to-face. This personal interaction helps build trust within the community and ensures that even those with limited digital access learn about Skill Park's offerings. The campaign aims to increase enrollments, strengthen community relationships, and spread the message of empowering youth through practical skills and career-oriented training.
                    </p>
                </div>

                <form action="{{ route('admin.marketing.d2d-submit') }}" method="post">
                    @csrf
                    <div class="row">
                        <!-- BDE Name - Only show if user is not marketing -->
                        @if(!$isMarketing)
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="bde_id" class="form-label">BDE Name <span class="text-danger">*</span></label>
                                <select class="form-select @error('bde_id') is-invalid @enderror" name="bde_id" id="bde_id" required>
                                    <option value="">Select BDE</option>
                                    @foreach($marketingUsers as $user)
                                        <option value="{{ $user->id }}" {{ old('bde_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('bde_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @else
                        <!-- Hidden field to store marketing user ID -->
                        <input type="hidden" name="marketing_bde_id" value="{{ $currentUser->id }}">
                        @endif

                        <!-- Date Of Visit -->
                        <div class="{{ $isMarketing ? 'col-md-12' : 'col-md-6' }}">
                            <div class="form-group mb-3">
                                <label for="date_of_visit" class="form-label">Date Of Visit <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date_of_visit') is-invalid @enderror" id="date_of_visit" name="date_of_visit" value="{{ old('date_of_visit', date('Y-m-d')) }}" required />
                                @error('date_of_visit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Location / Area Covered -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="location" class="form-label">Location / Area Covered <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" placeholder="Enter Location / Area Covered" value="{{ old('location') }}" required />
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- House Number -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="house_number" class="form-label">House Number</label>
                                <input type="text" class="form-control @error('house_number') is-invalid @enderror" id="house_number" name="house_number" placeholder="Enter House Number" value="{{ old('house_number') }}" />
                                @error('house_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- LEAD INFORMATION Section -->
                        <div class="col-12">
                            <hr class="my-4">
                            <h6 class="mb-3">LEAD INFORMATION</h6>
                            <div class="alert alert-info">
                                <p class="mb-0">
                                    <strong>Lead information</strong> refers to the collection of details about a potential customer who has shown interest in a product or service. It typically includes the lead's name, contact number, email address, and location, along with the source from which the lead was generated, such as social media, referrals, or door-to-door campaigns. Additionally, it records the lead's specific interest or inquiry, current status (like new, contacted, or converted), and any planned follow-up dates. Notes or remarks may also be added to capture extra details from conversations or interactions. This information helps businesses track, manage, and nurture potential customers effectively, ensuring that each lead is followed up and guided smoothly through the sales process.
                                </p>
                            </div>
                        </div>

                        <!-- Lead Name -->
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="lead_name" class="form-label">Lead Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('lead_name') is-invalid @enderror" id="lead_name" name="lead_name" placeholder="Enter Lead Name" value="{{ old('lead_name') }}" required />
                                @error('lead_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Code and Phone -->
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="code" class="form-label">Country Code <span class="text-danger">*</span></label>
                                <select class="form-select @error('code') is-invalid @enderror" id="code" name="code" required>
                                    <option value="">Select Country</option>
                                    @foreach($country_codes as $code => $country)
                                        <option value="{{ $code }}" {{ old('code') == $code ? 'selected' : '' }}>{{ $code }} - {{ $country }}</option>
                                    @endforeach
                                </select>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group mb-3">
                                <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="number" name="phone" class="form-control @error('phone') is-invalid @enderror" id="phone" placeholder="Enter Phone" value="{{ old('phone') }}" required maxlength="15" />
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Whatsapp Code and Number -->
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="whatsapp_code" class="form-label">WhatsApp Country Code</label>
                                <select class="form-select @error('whatsapp_code') is-invalid @enderror" id="whatsapp_code" name="whatsapp_code">
                                    <option value="">Select Country</option>
                                    @foreach($country_codes as $code => $country)
                                        <option value="{{ $code }}" {{ old('whatsapp_code') == $code ? 'selected' : '' }}>{{ $code }} - {{ $country }}</option>
                                    @endforeach
                                </select>
                                @error('whatsapp_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group mb-3">
                                <label for="whatsapp" class="form-label">WhatsApp Number</label>
                                <input type="number" name="whatsapp" class="form-control @error('whatsapp') is-invalid @enderror" id="whatsapp" placeholder="Enter WhatsApp Number" value="{{ old('whatsapp') }}" maxlength="15" />
                                @error('whatsapp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" name="address" id="address" placeholder="Enter Address" value="{{ old('address') }}" />
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Lead Category -->
                        <div class="col-12">
                            <hr class="my-4">
                            <h6 class="mb-3">Lead Category</h6>
                            <div class="alert alert-info">
                                <p class="mb-0">
                                    <strong>Lead category</strong> refers to the classification of potential customers based on their level of interest and likelihood of conversion. This helps businesses or marketing teams prioritize their efforts and plan suitable follow-up actions. For Skill Park, leads can be categorized as hot, warm, cold, or not interested. Hot leads are those who are highly interested and ready to enroll, while warm leads show potential but may need more information or time to decide. Cold leads have been contacted but currently show little interest, and not interested leads are those who have declined or are not suitable for the offered programs. Categorizing leads in this way enables Skill Park to manage outreach efficiently, focus on the most promising prospects, and improve overall conversion rates.
                                </p>
                            </div>
                        </div>

                        <!-- Lead Type -->
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="lead_type" class="form-label">Lead Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('lead_type') is-invalid @enderror" name="lead_type" id="lead_type" required>
                                    <option value="">Select Lead Type</option>
                                    <option value="Student" {{ old('lead_type') == 'Student' ? 'selected' : '' }}>Student</option>
                                    <option value="Parent" {{ old('lead_type') == 'Parent' ? 'selected' : '' }}>Parent</option>
                                    <option value="Working Professional" {{ old('lead_type') == 'Working Professional' ? 'selected' : '' }}>Working Professional</option>
                                    <option value="Institution Representative" {{ old('lead_type') == 'Institution Representative' ? 'selected' : '' }}>Institution Representative</option>
                                    <option value="Others" {{ old('lead_type') == 'Others' ? 'selected' : '' }}>Others</option>
                                </select>
                                @error('lead_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Interested Courses -->
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label class="form-label">Interested Courses</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="interested_courses[]" value="SSLC" id="course_sslc" {{ in_array('SSLC', old('interested_courses', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="course_sslc">SSLC</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="interested_courses[]" value="Plus one Plus Two" id="course_plus" {{ in_array('Plus one Plus Two', old('interested_courses', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="course_plus">Plus one Plus Two</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="interested_courses[]" value="Degree" id="course_degree" {{ in_array('Degree', old('interested_courses', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="course_degree">Degree</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="interested_courses[]" value="Ai Python" id="course_ai_python" {{ in_array('Ai Python', old('interested_courses', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="course_ai_python">Ai Python</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="interested_courses[]" value="Digital Marketing" id="course_digital_marketing" {{ in_array('Digital Marketing', old('interested_courses', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="course_digital_marketing">Digital Marketing</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="interested_courses[]" value="Graphic Designing" id="course_graphic_designing" {{ in_array('Graphic Designing', old('interested_courses', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="course_graphic_designing">Graphic Designing</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="interested_courses[]" value="Medical Coding" id="course_medical_coding" {{ in_array('Medical Coding', old('interested_courses', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="course_medical_coding">Medical Coding</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="interested_courses[]" value="Hospital Administration" id="course_hospital_admin" {{ in_array('Hospital Administration', old('interested_courses', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="course_hospital_admin">Hospital Administration</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="interested_courses[]" value="Hotel Management" id="course_hotel_management" {{ in_array('Hotel Management', old('interested_courses', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="course_hotel_management">Hotel Management</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Remarks / Notes -->
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="remarks" class="form-label">Remarks / Notes</label>
                                <textarea class="form-control @error('remarks') is-invalid @enderror" name="remarks" id="remarks" placeholder="Enter Remarks / Notes" rows="3">{{ old('remarks') }}</textarea>
                                @error('remarks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-12">
                            <div class="form-group text-end">
                                <button class="btn btn-primary" type="submit">
                                    <i class="ti ti-device-floppy"></i> Submit
                                </button>
                                <a href="{{ route('admin.marketing.index') }}" class="btn btn-secondary ms-2">
                                    <i class="ti ti-x"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
@endsection

