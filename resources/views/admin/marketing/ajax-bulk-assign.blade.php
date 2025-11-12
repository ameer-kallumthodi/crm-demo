<form action="{{ route('admin.marketing.bulk-assign.submit') }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="row g-3">
        <div class="col-lg-3">
            <div class="p-1">
                <label for="telecaller_id" class="form-label">Assign To</label>
                <select class="form-control" name="telecaller_id" id="telecaller_id" required>
                    <option value="">Select Telecaller</option>
                    @foreach ($telecallers as $telecaller)
                    <option value="{{ $telecaller->id }}">{{ $telecaller->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="p-1">
                <label for="bde_id" class="form-label">BDE Name</label>
                <select class="form-control" name="bde_id" id="bde_id">
                    <option value="">Select BDEs</option>
                    @foreach ($marketingUsers as $bde)
                    <option value="{{ $bde->id }}">{{ $bde->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="p-1">
                <label for="date_from" class="form-label">From Date (Date of Visit)</label>
                <input type="date" id="date_from" name="date_from" class="form-control" required>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="p-1">
                <label for="date_to" class="form-label">To Date (Date of Visit)</label>
                <input type="date" id="date_to" name="date_to" class="form-control" required>
            </div>
        </div>

        <div class="col-lg-12 pt-2">
            <label for="assign_all_counselor">Select Marketing Leads to be Assigned.</label>
            <!-- New Number Input for Selecting Top Leads -->
            <div class="d-flex justify-content-end">
                <div class="col-lg-2">
                    <div class="p-1">
                        <label for="select_count" class="form-label">Count</label>
                        <input type="number" id="select_count" class="form-control" min="1" placeholder="Enter count">
                    </div>
                </div>
            </div>
            <div id="marketing_leads_list">
                <hr>
                <div class="table-responsive bulk-operations-table">
                    <table class="table table-striped table-bordered bulk-table">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#</th>
                                <th style="width: 60%;">Lead Name</th>
                                <th style="width: 60%;">Phone</th>
                                <th style="width: 60%;">Location</th>
                                <th style="width: 60%;">Interested Courses</th>
                                <th style="width: 60%;">Remarks</th>
                                <th style="width: 60%; white-space: nowrap;">Date of Visit</th>
                                <th style="width: 30%;">Action <input type="checkbox" id="check_all" class="bulk-checkbox"></th>
                            </tr>
                        </thead>
                        <tbody id="marketing_lead_table_body">
                            <!-- Content dynamically populated by AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 p-2">
            <button class="btn btn-success float-end" type="submit" id="assign_btn" disabled>Assign</button>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {
    // Handle "Check All" functionality
    $('#check_all').on('change', function() {
        var isChecked = $(this).is(':checked');
        $('#marketing_lead_table_body input[type="checkbox"]').prop('checked', isChecked);
        toggleSubmitButton();
    });

    // Handle individual checkbox changes
    $('#marketing_lead_table_body').on('change', 'input[type="checkbox"]', function() {
        toggleSubmitButton();
    });

    // Handle telecaller selection change
    $('#telecaller_id').on('change', function() {
        toggleSubmitButton();
    });

    // Enable/Disable submit button based on telecaller selection and checkbox selection
    function toggleSubmitButton() {
        var telecallerSelected = $('#telecaller_id').val() !== '';
        var anyChecked = $('#marketing_lead_table_body input[type="checkbox"]:checked').length > 0;
        $('#assign_btn').prop('disabled', !(telecallerSelected && anyChecked));
    }

    // Select checkboxes based on entered number
    $('#select_count').on('input', function() {
        var count = parseInt($(this).val()) || 0;
        var checkboxes = $('#marketing_lead_table_body input[type="checkbox"]');

        // Uncheck all first
        checkboxes.prop('checked', false);

        // Check only the specified number of checkboxes
        checkboxes.slice(0, count).prop('checked', true);

        toggleSubmitButton();
    });

    // AJAX to fetch marketing leads
    $('#bde_id, #date_from, #date_to').on('change', function() {
        var bdeId = $('#bde_id').val();
        var dateFrom = $('#date_from').val();
        var dateTo = $('#date_to').val();

        if (dateFrom && dateTo) {
            $.ajax({
                url: '{{ route("admin.marketing.get-by-filters-assign") }}',
                type: 'POST',
                data: { 
                    bde_id: bdeId, 
                    date_from: dateFrom, 
                    date_to: dateTo 
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#marketing_lead_table_body').html(response);
                    $('#check_all').prop('checked', false);
                    toggleSubmitButton();
                },
                error: function(xhr) {
                    console.log('Error fetching marketing leads:', xhr.responseText);
                    $('#marketing_lead_table_body').html('<tr><td colspan="8" class="text-center">Error loading marketing leads. Please try again.</td></tr>');
                }
            });
        } else {
            $('#marketing_lead_table_body').html('');
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
