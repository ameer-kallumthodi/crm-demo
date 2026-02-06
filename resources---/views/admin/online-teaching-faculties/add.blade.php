<div class="container p-2">
    <form action="{{ route('admin.online-teaching-faculties.submit') }}" method="post" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-12">
                <h6 class="mb-2">A. Personal Details</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="full_name">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="{{ old('full_name') }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label" for="date_of_birth">Date of Birth</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label" for="gender">Gender</label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="">Select</option>
                                <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="primary_mobile_number">Primary Mobile Number</label>
                            <input type="text" class="form-control" id="primary_mobile_number" name="primary_mobile_number" value="{{ old('primary_mobile_number') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="alternate_contact_number">Alternate Contact Number</label>
                            <input type="text" class="form-control" id="alternate_contact_number" name="alternate_contact_number" value="{{ old('alternate_contact_number') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="official_email_address">Official Email Address</label>
                            <input type="email" class="form-control" id="official_email_address" name="official_email_address" value="{{ old('official_email_address') }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="father_name">Father’s Name</label>
                            <input type="text" class="form-control" id="father_name" name="father_name" value="{{ old('father_name') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="mother_name">Mother’s Name</label>
                            <input type="text" class="form-control" id="mother_name" name="mother_name" value="{{ old('mother_name') }}">
                        </div>
                    </div>

                    <div class="col-12">
                        <h6 class="mt-2 mb-2">Residential Address</h6>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="address_house_name_flat_no">House Name / Flat No.</label>
                            <input type="text" class="form-control" id="address_house_name_flat_no" name="address_house_name_flat_no" value="{{ old('address_house_name_flat_no') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="address_area_locality">Area / Locality</label>
                            <input type="text" class="form-control" id="address_area_locality" name="address_area_locality" value="{{ old('address_area_locality') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="address_city">Village / Town / City</label>
                            <input type="text" class="form-control" id="address_city" name="address_city" value="{{ old('address_city') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="address_district">District</label>
                            <input type="text" class="form-control" id="address_district" name="address_district" value="{{ old('address_district') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="address_state">State</label>
                            <input type="text" class="form-control" id="address_state" name="address_state" value="{{ old('address_state') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="address_pin_code">PIN Code</label>
                            <input type="text" class="form-control" id="address_pin_code" name="address_pin_code" value="{{ old('address_pin_code') }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="highest_educational_qualification">Highest Educational Qualification</label>
                            <input type="text" class="form-control" id="highest_educational_qualification" name="highest_educational_qualification" value="{{ old('highest_educational_qualification') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="additional_certifications">Additional Certifications / Professional Credentials</label>
                            <input type="text" class="form-control" id="additional_certifications" name="additional_certifications" value="{{ old('additional_certifications') }}">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="teaching_experience">Teaching Experience</label>
                            <select class="form-select" id="teaching_experience" name="teaching_experience">
                                <option value="">Select</option>
                                <option value="Yes" {{ old('teaching_experience') === 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ old('teaching_experience') === 'No' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label" for="department_name">Department Name</label>
                            <select class="form-select" id="department_name" name="department_name">
                                <option value="">Select</option>
                                <option value="E-School" {{ old('department_name') === 'E-School' ? 'selected' : '' }}>E-School</option>
                                <option value="EduThanzeel" {{ old('department_name') === 'EduThanzeel' ? 'selected' : '' }}>EduThanzeel</option>
                                <option value="Graphic Designing" {{ old('department_name') === 'Graphic Designing' ? 'selected' : '' }}>Graphic Designing</option>
                                <option value="Digital Marketing" {{ old('department_name') === 'Digital Marketing' ? 'selected' : '' }}>Digital Marketing</option>
                                <option value="Data Science" {{ old('department_name') === 'Data Science' ? 'selected' : '' }}>Data Science</option>
                                <option value="Machine Learning" {{ old('department_name') === 'Machine Learning' ? 'selected' : '' }}>Machine Learning</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <hr>
                <h6 class="mb-2">B. Document Submission (Uploads)</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="document_resume_cv">Updated Resume / CV</label>
                            <input type="file" class="form-control" id="document_resume_cv" name="document_resume_cv">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="document_10th_certificate">10th certificate</label>
                            <input type="file" class="form-control" id="document_10th_certificate" name="document_10th_certificate">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="document_educational_qualification_certificates">Educational Qualification Certificates</label>
                            <input type="file" class="form-control" id="document_educational_qualification_certificates" name="document_educational_qualification_certificates">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="document_aadhaar_front">Aadhaar Card (Front Side)</label>
                            <input type="file" class="form-control" id="document_aadhaar_front" name="document_aadhaar_front">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="document_aadhaar_back">Aadhaar Card (Back Side)</label>
                            <input type="file" class="form-control" id="document_aadhaar_back" name="document_aadhaar_back">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="document_other_1">Other Supporting Document – 1</label>
                            <input type="file" class="form-control" id="document_other_1" name="document_other_1">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="document_other_2">Other Supporting Document – 2</label>
                            <input type="file" class="form-control" id="document_other_2" name="document_other_2">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success float-end">Submit</button>
    </form>
</div>

