<div class="modal-header">
    <h5 class="modal-title">
        <i class="ti ti-users me-2"></i>Team Members - {{ $team->name }}
    </h5>

    <!-- Team Lead Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-light-primary">
                    <h6 class="mb-0">
                        <i class="ti ti-crown me-2"></i>Team Lead
                    </h6>
                </div>
                <div class="card-body">
                    @if($team->teamLead)
                        <div class="d-flex align-items-center">
                            <div class="avtar avtar-s rounded-circle bg-primary text-white me-3">
                                <i class="ti ti-user"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">{{ $team->teamLead->name }}</h6>
                                <p class="text-muted mb-0">{{ $team->teamLead->email }}</p>
                                <small class="text-muted">Phone: {{ $team->teamLead->phone ?? 'N/A' }}</small>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="ti ti-user-x f-48 mb-2"></i>
                            <p class="mb-0">No team lead assigned</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Team Members Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="avtar avtar-s rounded-circle bg-white text-primary me-3">
                                <i class="ti ti-users"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-white">Team Members</h6>
                                <small class="text-white-50">{{ $allTeamMembers->count() }} member{{ $allTeamMembers->count() != 1 ? 's' : '' }}</small>
                            </div>
                        </div>
                        <button type="button" class="btn btn-light btn-sm" onclick="show_small_modal('{{ route('admin.telecallers.add') }}?team_id={{ $team->id }}', 'Add Team Member')">
                            <i class="ti ti-plus me-1"></i> Add Member
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($allTeamMembers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0 ps-4 py-3" style="width: 30%;">
                                            <i class="ti ti-user me-2"></i>Member
                                        </th>
                                        <th class="border-0 py-3" style="width: 25%;">
                                            <i class="ti ti-mail me-2"></i>Email
                                        </th>
                                        <th class="border-0 py-3" style="width: 20%;">
                                            <i class="ti ti-phone me-2"></i>Phone
                                        </th>
                                        <th class="border-0 py-3" style="width: 15%;">
                                            <i class="ti ti-circle-check me-2"></i>Status
                                        </th>
                                        <th class="border-0 text-end pe-4 py-3" style="width: 10%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allTeamMembers as $member)
                                        <tr class="border-bottom">
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="avtar avtar-m rounded-circle me-3 {{ $member->id == $team->team_lead_id ? 'bg-warning text-white' : 'bg-light-primary text-primary' }}">
                                                        @if($member->id == $team->team_lead_id)
                                                            <i class="ti ti-crown f-18"></i>
                                                        @else
                                                            <i class="ti ti-phone f-18"></i>
                                                        @endif
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex align-items-center mb-1">
                                                            <h6 class="mb-0 fw-semibold me-2">{{ $member->name }}</h6>
                                                            @if($member->id == $team->team_lead_id)
                                                                <span class="badge bg-warning text-dark">
                                                                    <i class="ti ti-crown me-1"></i>Team Lead
                                                                </span>
                                                            @endif
                                                        </div>
                                                        @if($member->id != $team->team_lead_id)
                                                            <small class="text-muted">Team Member</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="ti ti-mail text-muted me-2"></i>
                                                    <span class="text-truncate" style="max-width: 180px;" title="{{ $member->email }}">
                                                        {{ $member->email }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="py-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="ti ti-phone text-muted me-2"></i>
                                                    <span>{{ $member->phone ?? 'N/A' }}</span>
                                                </div>
                                            </td>
                                            <td class="py-3">
                                                <span class="badge bg-{{ $member->is_active ? 'success' : 'danger' }} rounded-pill">
                                                    <i class="ti ti-{{ $member->is_active ? 'check' : 'x' }} me-1"></i>
                                                    {{ $member->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td class="text-end pe-4 py-3">
                                                @if($member->id != $team->team_lead_id)
                                                    <button type="button" class="btn btn-outline-danger btn-sm" 
                                                            onclick="removeTeamMember({{ $member->id }}, '{{ $member->name }}')"
                                                            title="Remove from Team">
                                                        <i class="ti ti-user-minus me-1"></i> Remove
                                                    </button>
                                                @else
                                                    <span class="badge bg-light text-muted">
                                                        <i class="ti ti-crown me-1"></i>Lead
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="avtar avtar-xl rounded-circle bg-light-secondary text-secondary mx-auto mb-3">
                                <i class="ti ti-users f-32"></i>
                            </div>
                            <h6 class="text-muted mb-2">No team members assigned</h6>
                            <p class="text-muted mb-3">Click "Add Member" to assign telecallers to this team</p>
                            <button type="button" class="btn btn-primary" onclick="show_small_modal('{{ route('admin.telecallers.add') }}?team_id={{ $team->id }}', 'Add Team Member')">
                                <i class="ti ti-plus me-1"></i> Add First Member
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Available Telecallers Section -->
    @if($availableTelecallers->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="ti ti-user-plus me-2"></i>Available Telecallers
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($availableTelecallers as $telecaller)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avtar avtar-s rounded-circle bg-light-secondary text-secondary me-2">
                                                        <i class="ti ti-user"></i>
                                                    </div>
                                                    <span>{{ $telecaller->name }}</span>
                                                </div>
                                            </td>
                                            <td>{{ $telecaller->email }}</td>
                                            <td>{{ $telecaller->phone ?? 'N/A' }}</td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-success btn-sm" 
                                                        onclick="addTeamMember({{ $telecaller->id }}, {{ $team->id }}, '{{ $telecaller->name }}')"
                                                        title="Add to Team">
                                                    <i class="ti ti-user-plus"></i> Add
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.swal2-popup-high-z {
    z-index: 9999 !important;
}
</style>

<script>
function removeTeamMember(userId, userName) {
    Swal.fire({
        title: 'Remove Team Member',
        text: `Are you sure you want to remove ${userName} from this team?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, remove!',
        cancelButtonText: 'Cancel',
        customClass: {
            popup: 'swal2-popup-high-z'
        },
        didOpen: () => {
            // Prevent the parent modal from closing
            $('.modal.show').modal('show');
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("admin.teams.remove-member") }}',
                type: 'POST',
                data: {
                    user_id: userId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Removed!', response.message, 'success');
                        // Reload the modal content
                        setTimeout(function() {
                            show_ajax_modal('{{ route("admin.teams.members", $team->id) }}', 'Team Members - {{ $team->name }}');
                        }, 1000);
                    } else {
                        Swal.fire('Error!', response.message || 'Something went wrong!', 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Something went wrong!', 'error');
                }
            });
        }
    });
}

function addTeamMember(telecallerId, teamId, telecallerName) {
    Swal.fire({
        title: 'Add Team Member',
        text: `Are you sure you want to add ${telecallerName} to this team?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, add!',
        cancelButtonText: 'Cancel',
        customClass: {
            popup: 'swal2-popup-high-z'
        },
        didOpen: () => {
            // Prevent the parent modal from closing
            $('.modal.show').modal('show');
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("admin.teams.add-member") }}',
                type: 'POST',
                data: {
                    user_id: telecallerId,
                    team_id: teamId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Added!', response.message, 'success');
                        // Reload the modal content
                        setTimeout(function() {
                            show_ajax_modal('{{ route("admin.teams.members", $team->id) }}', 'Team Members - {{ $team->name }}');
                        }, 1000);
                    } else {
                        Swal.fire('Error!', response.message || 'Something went wrong!', 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Something went wrong!', 'error');
                }
            });
        }
    });
}
</script>
