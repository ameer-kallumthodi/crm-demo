<div class="container p-2">
    <form action="{{ route('leads.submit') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="title">Name</label>
                    <input type="text" name="title" class="form-control" id="title" placeholder="Enter Name" value="{{ old('title') }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label" for="code">Country Code</label>
                        <select class="form-select" id="code" name="code">
                            <option value="">Select Country</option>
                            @foreach($country_codes as $code => $country)
                                <option value="{{ $code }}" {{ old('code') == $code ? 'selected' : '' }}>{{ $code }} - {{ $country }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label" for="phone">Phone <span class="text-danger">*</span></label>
                        <input type="number" name="phone" class="form-control" id="phone" placeholder="Enter Phone" value="{{ old('phone') }}" required maxlength="15" />
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="gender">Gender</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="gender" id="gender-male" value="male" {{ old('gender') == 'male' ? 'checked' : '' }}>
                            <label class="form-check-label" for="gender-male">Male</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="gender" id="gender-female" value="female" {{ old('gender') == 'female' ? 'checked' : '' }}>
                            <label class="form-check-label" for="gender-female">Female</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="age">Age</label>
                    <input type="number" class="form-control" id="age" name="age" placeholder="Enter Age" value="{{ old('age') }}" max="999" />
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="place">Place</label>
                    <input type="text" class="form-control" id="place" name="place" placeholder="Enter Place" value="{{ old('place') }}" />
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label" for="whatsapp_code">WhatsApp Code</label>
                        <select class="form-select" id="whatsapp_code" name="whatsapp_code">
                            <option value="">Select Country</option>
                            @foreach($country_codes as $code => $country)
                                <option value="{{ $code }}" {{ old('whatsapp_code') == $code ? 'selected' : '' }}>{{ $code }} - {{ $country }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label" for="whatsapp">WhatsApp Number</label>
                        <input type="number" name="whatsapp" class="form-control" id="whatsapp" placeholder="Enter WhatsApp Number" value="{{ old('whatsapp') }}" maxlength="15" />
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="email">Email ID</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email" value="{{ old('email') }}" />
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="qualification">Qualification</label>
                    <input type="text" class="form-control" id="qualification" name="qualification" placeholder="Enter Qualification" value="{{ old('qualification') }}" />
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="interest_status">Interest Status</label>
                    <select class="form-select" name="interest_status" id="interest_status">
                        <option value="">Select Status</option>
                        <option value="1" {{ old('interest_status') == '1' ? 'selected' : '' }}>Hot</option>
                        <option value="2" {{ old('interest_status') == '2' ? 'selected' : '' }}>Warm</option>
                        <option value="3" {{ old('interest_status') == '3' ? 'selected' : '' }}>Cold</option>
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="lead_status_id">Lead Status</label>
                    <select class="form-select" name="lead_status_id" id="lead_status_id">
                        @foreach($leadStatuses as $status)
                            <option value="{{ $status->id }}" {{ old('lead_status_id', 1) == $status->id ? 'selected' : '' }}>{{ $status->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="lead_source_id">Lead Source</label>
                    <select class="form-select" name="lead_source_id" id="lead_source_id">
                        <option value="">Select Source</option>
                        @foreach($leadSources as $source)
                            <option value="{{ $source->id }}" {{ old('lead_source_id') == $source->id ? 'selected' : '' }}>{{ $source->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="country_id">Country</label>
                    <select class="form-select" name="country_id" id="country_id">
                        <option value="">Select Country</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>{{ $country->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="course_id">Course Interested</label>
                    <select class="form-select" name="course_id" id="course_id">
                        <option value="">Select Course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="team_id">Team</label>
                    <select class="form-select" name="team_id" id="team_id">
                        <option value="">Select Team</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}" {{ old('team_id') == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="telecaller_id">Telecaller</label>
                    <select class="form-select" name="telecaller_id" id="telecaller_id">
                        <option value="">Select Telecaller</option>
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="address">Address</label>
                    <input type="text" class="form-control" name="address" id="address" placeholder="Enter Address" value="{{ old('address') }}" />
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="add_date">Date</label>
                    <input type="date" class="form-control" id="add_date" name="add_date" value="" />
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="add_time">Add Time</label>
                    <input type="time" class="form-control" id="add_time" name="add_time" value="" />
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="remarks">Remarks</label>
                    <textarea class="form-control" name="remarks" id="remarks" placeholder="Enter Remarks" rows="3">{{ old('remarks') }}</textarea>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success float-end">Submit</button>
    </form>
</div>

<script>
$(document).ready(function() {
    // Handle team selection change
    $('#team_id').on('change', function() {
        const teamId = $(this).val();
        const telecallerSelect = $('#telecaller_id');
        
        // Clear existing options
        telecallerSelect.html('<option value="">Loading telecallers...</option>');
        
        if (teamId) {
            // Fetch telecallers for selected team
            $.ajax({
                url: '{{ route("leads.telecallers-by-team") }}',
                type: 'GET',
                data: { team_id: teamId },
                success: function(response) {
                    telecallerSelect.html('<option value="">Select Telecaller</option>');
                    
                    if (response.telecallers && response.telecallers.length > 0) {
                        $.each(response.telecallers, function(index, telecaller) {
                            telecallerSelect.append(
                                '<option value="' + telecaller.id + '">' + telecaller.name + '</option>'
                            );
                        });
                    } else {
                        telecallerSelect.append('<option value="">No telecallers found in this team</option>');
                    }
                },
                error: function() {
                    telecallerSelect.html('<option value="">Error loading telecallers</option>');
                }
            });
        } else {
            telecallerSelect.html('<option value="">Select Team First</option>');
        }
    });
    
    // If there's an old team_id value, trigger the change event
    @if(old('team_id'))
        $('#team_id').trigger('change');
    @endif
});
</script>
