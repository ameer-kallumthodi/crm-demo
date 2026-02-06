<div class="container p-2">
    <form action="{{ route('admin.marketing.submit') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" id="name" placeholder="Enter Name" value="{{ old('name') }}" required>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" id="email" placeholder="Enter Email" value="{{ old('email') }}" required>
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
                        <label class="form-label" for="phone">Phone</label>
                        <input type="text" name="phone" class="form-control" id="phone" placeholder="Enter Phone" value="{{ old('phone') }}">
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="password">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="Enter Password" required>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="joining_date">Joining Date</label>
                    <input type="date" name="joining_date" class="form-control" id="joining_date" value="{{ old('joining_date') }}">
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="team_id">Team <span class="text-muted">(Marketing Teams Only)</span></label>
                    <select class="form-select" id="team_id" name="team_id">
                        <option value="">Select Team</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}" {{ (old('team_id') == $team->id || (isset($selectedTeamId) && $selectedTeamId == $team->id)) ? 'selected' : '' }}>{{ $team->name }}</option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Only marketing teams are available for selection</small>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_team_lead" id="is_team_lead" value="1" {{ old('is_team_lead') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_team_lead">
                            <i class="ti ti-crown me-1"></i>Is Team Lead
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success float-end">Submit</button>
    </form>
</div>

