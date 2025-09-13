<h5 class="mb-4">
    <i class="ti ti-users me-2"></i>Team Members - {{ $team->name }}
</h5>

<!-- Team Lead Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">
                    <i class="ti ti-crown me-2"></i>Team Lead
                </h6>
            </div>
            <div class="card-body">
                @if($team->teamLead)
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="ti ti-crown"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold">{{ $team->teamLead->name }}</h6>
                            <p class="text-muted mb-1">{{ $team->teamLead->email }}</p>
                            <small class="text-muted">Phone: {{ $team->teamLead->phone ?? 'N/A' }}</small>
                        </div>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="ti ti-user-x display-4 mb-3 text-muted"></i>
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
        <div class="card">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">
                            <i class="ti ti-users me-2"></i>Team Members
                        </h6>
                        <small class="text-muted">{{ $allTeamMembers->count() }} member{{ $allTeamMembers->count() != 1 ? 's' : '' }}</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" onclick="openAddMemberModal()">
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
                                    <th class="ps-4 py-3" style="width: 60px;">
                                        <i class="ti ti-hash me-2"></i>#
                                    </th>
                                    <th class="py-3">
                                        <i class="ti ti-user me-2"></i>Member
                                    </th>
                                    <th class="py-3">
                                        <i class="ti ti-mail me-2"></i>Email
                                    </th>
                                    <th class="py-3">
                                        <i class="ti ti-phone me-2"></i>Phone
                                    </th>
                                    <th class="py-3">
                                        <i class="ti ti-circle-check me-2"></i>Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allTeamMembers as $index => $member)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <span class="badge bg-light text-dark fw-bold">{{ $index + 1 }}</span>
                                        </td>
                                        <td class="py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <div class="{{ $member->id == $team->team_lead_id ? 'bg-warning text-dark' : 'bg-light text-primary' }} rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        @if($member->id == $team->team_lead_id)
                                                            <i class="ti ti-crown"></i>
                                                        @else
                                                            <i class="ti ti-phone"></i>
                                                        @endif
                                                    </div>
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
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="bg-light text-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                            <i class="ti ti-users display-6"></i>
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
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="ti ti-user-plus me-2"></i>Available Telecallers
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($availableTelecallers as $index => $telecaller)
                                    <tr>
                                        <td>
                                            <span class="badge bg-light text-dark fw-bold">{{ $index + 1 }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light text-secondary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
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
                                                <i class="ti ti-user-plus me-1"></i> Add
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

<style>
.swal2-popup-high-z {
    z-index: 9999 !important;
}
</style>
<script>

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
                        Swal.fire('Added!', response.message, 'success').then(() => {
                            // Close current modal after SweetAlert is dismissed
                            $('.modal').modal('hide');
                            // Reload the modal content after a short delay
                            setTimeout(function() {
                                show_ajax_modal('{{ route("admin.teams.members", $team->id) }}', 'Team Members - {{ $team->name }}');
                            }, 300);
                        });
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

function openAddMemberModal() {
    // Close current modal first
    $('.modal').modal('hide');
    // Wait for modal to close, then open the add member modal
    setTimeout(function() {
        show_small_modal('{{ route('admin.telecallers.add') }}?team_id={{ $team->id }}', 'Add Team Member');
    }, 300);
}
</script>
