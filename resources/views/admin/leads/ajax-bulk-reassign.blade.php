<form action="{{ route('admin.leads.bulk-reassign.submit') }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="row g-3">
        <div class="col-lg-2">
            <div class="p-1">
                <label for="telecaller_id" class="form-label">Re-assign To</label>
                <select class="form-control" name="telecaller_id" id="telecaller_id" required>
                    <option value="">Select Telecaller</option>
                    @foreach ($telecallers as $telecaller)
                    <option value="{{ $telecaller->id }}">{{ $telecaller->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-2">
            <div class="p-1">
                <label for="lead_source_id" class="form-label">Lead Source</label>
                <select class="form-control" name="lead_source_id" id="lead_source_id" required>
                    <option value="">Select Source</option>
                    @foreach ($leadSources as $source)
                    <option value="{{ $source->id }}">{{ $source->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-2">
            <div class="p-1">
                <label for="lead_status_id" class="form-label">Lead Status</label>
                <select class="form-control" name="lead_status_id" id="lead_status_id" required>
                    <option value="">Select Status</option>
                    @foreach ($leadStatuses as $status)
                    <option value="{{ $status->id }}">{{ $status->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-2">
            <div class="p-1">
                <label for="from_telecaller_id" class="form-label">Re-assign From</label>
                <select class="form-control" name="from_telecaller_id" id="from_telecaller_id" required>
                    <option value="">Select Telecaller</option>
                    @foreach ($telecallers as $telecaller)
                        <option value="{{ $telecaller->id }}">{{ $telecaller->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-2">
            <div class="p-1">
                <label for="lead_from_date" class="form-label">From Date</label>
                <input type="date" id="lead_from_date" name="lead_from_date" class="form-control" required>
            </div>
        </div>

        <div class="col-lg-2">
            <div class="p-1">
                <label for="lead_to_date" class="form-label">To Date</label>
                <input type="date" id="lead_to_date" name="lead_to_date" class="form-control" required>
            </div>
        </div>

        <div class="col-lg-12 pt-2">
            <label for="assign_all_counselor">Select Leads to be Re-assigned.</label>
            <!-- New Number Input for Selecting Top Leads -->
            <div class="d-flex justify-content-end">
                <div class="col-lg-2">
                    <div class="p-1">
                        <label for="select_count" class="form-label">Count</label>
                        <input type="number" id="select_count" class="form-control" min="1" placeholder="Enter count">
                    </div>
                </div>
            </div>
            <div id="telecaller_list">
                <hr>
                <div class="table-responsive bulk-operations-table">
                    <table class="table table-striped table-bordered bulk-table">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#</th>
                                <th style="width: 60%;">Lead</th>
                                <th style="width: 60%;">Lead Status</th>
                                <th style="width: 60%;">Course Interested</th>
                                <th style="width: 60%;">Remarks</th>
                                <th style="width: 60%; white-space: nowrap;">Date</th>
                                <th style="width: 30%;">Action <input type="checkbox" id="check_all" class="bulk-checkbox"></th>
                            </tr>
                        </thead>
                        <tbody id="lead_table_body">
                            <!-- Content dynamically populated by AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 p-2">
            <button class="btn btn-success float-end" type="submit" id="reassign_btn" disabled>Re-Assign</button>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {
    // Handle "Check All" functionality
    $('#check_all').on('change', function() {
        var isChecked = $(this).is(':checked');
        $('#lead_table_body input[type="checkbox"]').prop('checked', isChecked);
        toggleSubmitButton();
    });

    // Handle individual checkbox changes
    $('#lead_table_body').on('change', 'input[type="checkbox"]', function() {
        toggleSubmitButton();
    });

    // Enable/Disable submit button based on checkbox selection
    function toggleSubmitButton() {
        var anyChecked = $('#lead_table_body input[type="checkbox"]:checked').length > 0;
        $('#reassign_btn').prop('disabled', !anyChecked);
    }

    // Select checkboxes based on entered number
    $('#select_count').on('input', function() {
        var count = parseInt($(this).val()) || 0;
        var checkboxes = $('#lead_table_body input[type="checkbox"]');

        // Uncheck all first
        checkboxes.prop('checked', false);

        // Check only the specified number of checkboxes
        checkboxes.slice(0, count).prop('checked', true);

        toggleSubmitButton();
    });

    // Team selection removed - senior managers can see all telecallers directly
    
    // AJAX to fetch leads
    $('#from_telecaller_id, #lead_source_id, #lead_from_date, #lead_to_date, #lead_status_id').on('change', function() {
        var leadSourceId = $('#lead_source_id').val();
        var leadStatusId = $('#lead_status_id').val();
        var teleCallerId = $('#from_telecaller_id').val();
        var leadFromDate = $('#lead_from_date').val();
        var leadToDate = $('#lead_to_date').val();

        if (leadSourceId && teleCallerId && leadFromDate && leadToDate && leadStatusId) {
            $.ajax({
                url: '{{ route("admin.leads.get-by-source-reassign") }}',
                type: 'POST',
                data: { 
                    lead_source_id: leadSourceId, 
                    tele_caller_id: teleCallerId, 
                    from_date: leadFromDate, 
                    to_date: leadToDate, 
                    lead_status_id: leadStatusId 
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#lead_table_body').html(response);
                    $('#check_all').prop('checked', false);
                    toggleSubmitButton();
                },
                error: function(xhr) {
                    console.log('Error fetching leads:', xhr.responseText);
                }
            });
        } else {
            $('#lead_table_body').html('');
            toggleSubmitButton();
        }
    });
});
</script>

<style>
.bulk-operations-table {
    max-height: 300px;
    overflow-y: auto;
}

.bulk-table thead th {
    background-color: #fff;
    position: sticky;
    top: 0;
    border: 1px solid #ddd;
}

.bulk-checkbox {
    width: 22px;
    height: 22px;
}
</style>
