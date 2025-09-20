<div class="p-3">
    <form id="leadEditForm" action="{{ route('leads.update', $lead->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="title">Name <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" id="title" placeholder="Enter Name" value="{{ old('title', $lead->title) }}" required>
                    <div class="invalid-feedback" id="title-error"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="gender">Gender</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="gender" id="gender-male" value="male" {{ old('gender', $lead->gender) == 'male' ? 'checked' : '' }}>
                            <label class="form-check-label" for="gender-male">Male</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="gender" id="gender-female" value="female" {{ old('gender', $lead->gender) == 'female' ? 'checked' : '' }}>
                            <label class="form-check-label" for="gender-female">Female</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="age">Age</label>
                    <input type="number" class="form-control" id="age" name="age" placeholder="Enter Age" value="{{ old('age', $lead->age) }}" max="999" />
                    <div class="invalid-feedback" id="age-error"></div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="place">Place</label>
                    <input type="text" class="form-control" id="place" name="place" placeholder="Enter Place" value="{{ old('place', $lead->place) }}" />
                    <div class="invalid-feedback" id="place-error"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label" for="code">Country Code <span class="text-danger">*</span></label>
                        <select class="form-select" id="code" name="code" required>
                            <option value="">Select Country</option>
                            @foreach($country_codes as $code => $country)
                                <option value="{{ $code }}" {{ old('code', $lead->code) == $code ? 'selected' : '' }}>{{ $code }} - {{ $country }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" id="code-error"></div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label" for="phone">Phone <span class="text-danger">*</span></label>
                        <input type="number" name="phone" class="form-control" id="phone" placeholder="Enter Phone" value="{{ old('phone', $lead->phone) }}" required maxlength="15" />
                        <div class="invalid-feedback" id="phone-error"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label" for="whatsapp_code">WhatsApp Code</label>
                        <select class="form-select" id="whatsapp_code" name="whatsapp_code">
                            <option value="">Select Country</option>
                            @foreach($country_codes as $code => $country)
                                <option value="{{ $code }}" {{ old('whatsapp_code', $lead->whatsapp_code) == $code ? 'selected' : '' }}>{{ $code }} - {{ $country }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" id="whatsapp_code-error"></div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label" for="whatsapp">WhatsApp Number</label>
                        <input type="number" name="whatsapp" class="form-control" id="whatsapp" placeholder="Enter WhatsApp Number" value="{{ old('whatsapp', $lead->whatsapp) }}" maxlength="15" />
                        <div class="invalid-feedback" id="whatsapp-error"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="email">Email ID</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email" value="{{ old('email', $lead->email) }}" />
                    <div class="invalid-feedback" id="email-error"></div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="qualification">Qualification</label>
                    <input type="text" class="form-control" id="qualification" name="qualification" placeholder="Enter Qualification" value="{{ old('qualification', $lead->qualification) }}" />
                    <div class="invalid-feedback" id="qualification-error"></div>
                </div>
            </div>


            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="lead_status_id">Lead Status <span class="text-danger">*</span></label>
                    <select class="form-select" name="lead_status_id" id="lead_status_id" required>
                        <option value="">Select Lead Status</option>
                        @foreach($leadStatuses as $status)
                            <option value="{{ $status->id }}" {{ old('lead_status_id', $lead->lead_status_id) == $status->id ? 'selected' : '' }}>
                                {{ $status->title }}
                            </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="lead_status_id-error"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="lead_source_id">Lead Source <span class="text-danger">*</span></label>
                    <select class="form-select" name="lead_source_id" id="lead_source_id" required>
                        <option value="">Select Source</option>
                        @foreach($leadSources as $source)
                            <option value="{{ $source->id }}" {{ old('lead_source_id', $lead->lead_source_id) == $source->id ? 'selected' : '' }}>
                                {{ $source->title }}
                            </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="lead_source_id-error"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="country_id">Country <span class="text-danger">*</span></label>
                    <select class="form-select" name="country_id" id="country_id" required>
                        <option value="">Select Country</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" {{ old('country_id', $lead->country_id) == $country->id ? 'selected' : '' }}>
                                {{ $country->title }}
                            </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="country_id-error"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="course_id">Course Interested <span class="text-danger">*</span></label>
                    <select class="form-select" name="course_id" id="course_id" required>
                        <option value="">Select Course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id', $lead->course_id) == $course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="course_id-error"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="team_id">Team</label>
                    <select class="form-select" name="team_id" id="team_id">
                        <option value="">Select Team</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}" {{ old('team_id', $lead->team_id) == $team->id ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="team_id-error"></div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="address">Address</label>
                    <input type="text" class="form-control" name="address" id="address" placeholder="Enter Address" value="{{ old('address', $lead->address) }}" />
                    <div class="invalid-feedback" id="address-error"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="telecaller_id">Academic Counselor</label>
                    <select class="form-select" name="telecaller_id" id="telecaller_id">
                        <option value="">Select Team First</option>
                        @foreach($telecallers as $telecaller)
                            <option value="{{ $telecaller->id }}" {{ old('telecaller_id', $lead->telecaller_id) == $telecaller->id ? 'selected' : '' }}>
                                {{ $telecaller->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="telecaller_id-error"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="followup_date">Follow Up Date</label>
                    <input type="date" class="form-control" id="followup_date" name="followup_date" value="{{ old('followup_date', $lead->followup_date) }}" />
                    <div class="invalid-feedback" id="followup_date-error"></div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="remarks">Remarks</label>
                    <textarea class="form-control" name="remarks" id="remarks" placeholder="Enter Remarks" rows="3">{{ old('remarks', $lead->remarks) }}</textarea>
                    <div class="invalid-feedback" id="remarks-error"></div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success">Update Lead</button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    // Handle team selection to load telecallers
    $('#team_id').on('change', function() {
        const teamId = $(this).val();
        const telecallerSelect = $('#telecaller_id');
        
        if (teamId) {
            $.get('{{ route("leads.telecallers-by-team") }}', { team_id: teamId })
                .done(function(data) {
                    telecallerSelect.empty();
                    telecallerSelect.append('<option value="">Select Telecaller</option>');
                    $.each(data.telecallers, function(index, telecaller) {
                        telecallerSelect.append(
                            $('<option></option>').val(telecaller.id).text(telecaller.name)
                        );
                    });
                })
                .fail(function() {
                    console.error('Failed to load telecallers');
                });
        } else {
            telecallerSelect.empty();
            telecallerSelect.append('<option value="">Select Telecaller</option>');
        }
    });

    // Form submission with AJAX
    $('#leadEditForm').on('submit', function(e) {
        e.preventDefault();
        
        $('.form-control, .form-select').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        const form = $(this);
        const formData = new FormData(this);
        
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="ti ti-loader-2"></i> Updating...');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toast_success(response.message);
                    $('#ajax_modal').modal('hide');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(field, messages) {
                        var input = $('[name="' + field + '"]');
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(messages[0]);
                    });
                } else {
                    toast_danger('An error occurred while updating the lead. Please try again.');
                }
                
                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
            }
        });
    });
});
</script>
