<form action="<?= base_url('app/leads/bulk_delete') ?>" method="post" enctype="multipart/form-data">
    <div class="row g-3">
        <div class="col-lg-4">
            <div class="p-1">
                <label for="tele_caller_id" class="form-label">Telecaller</label>
                <select class="form-control" name="tele_caller_id" id="tele_caller_id" required>
                    <option value="">Select Tele Caller</option>
                    <?php foreach ($tele_callers as $tele_caller) { ?>
                        <option value="<?= $tele_caller['id'] ?>"><?= $tele_caller['name'] ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="p-1">
                <label for="lead_date" class="form-label font-size-13 text-muted">Date</label>
                <input type="date" id="lead_date" name="lead_date" class="form-control" required value="<?= !empty($_GET['lead_date']) ? $_GET['lead_date'] : ''?>" >
            </div>
        </div>

        <div class="col-lg-4">
            <div class="p-1">
                <label for="lead_source_id" class="form-label font-size-13 text-muted">Lead Source</label>
                <select class="form-control" name="lead_source_id" id="lead_source_id" required>
                    <option value="">Select Source</option>
                    <?php foreach ($lead_source as $source) { ?>
                    <option value="<?= $source['id'] ?>"><?= $source['title'] ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="col-lg-12 pt-2">
            <label for="assign_all_counselor">Select Leads to be Deleted.</label>
            <div id="telecaller_list">
                <hr>
                <div class="table-responsive reassign-table" style="max-height: 300px; overflow-y: auto;"> <!-- Fixed height for the table body -->
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#</th>
                                <th style="width: 60%;">Lead</th>
                                <th style="width: 60%;">Date</th>
                                <th style="width: 30%;">Action <input type="checkbox" id="check_all"></th>
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
            <!-- Submit button initially disabled -->
            <button class="btn btn-danger float-end" type="submit" id="reassign_btn" disabled>Delete</button>
        </div>
    </div>
</form>


<script>
    $(document).ready(function() {
    // Handle "Check All" functionality
    $('#check_all').on('change', function() {
        var isChecked = $(this).is(':checked');
        $('#lead_table_body input[type="checkbox"]').prop('checked', isChecked);
        toggleSubmitButton(); // Check if the submit button should be enabled/disabled
    });

    // Handle individual checkbox changes
    $('#lead_table_body').on('change', 'input[type="checkbox"]', function() {
        toggleSubmitButton();
    });

    // Function to enable/disable the submit button
    function toggleSubmitButton() {
        var anyChecked = $('#lead_table_body input[type="checkbox"]:checked').length > 0;
        $('#reassign_btn').prop('disabled', !anyChecked); // Enable button if any checkbox is checked
    }

    // AJAX to fetch leads based on lead source
    // Bind the change event to both tele_caller_id and lead_source_id
    $('#tele_caller_id, #lead_source_id, #lead_date').on('change', function() {
        var leadSourceId = $('#lead_source_id').val();
        var teleCallerId = $('#tele_caller_id').val();
        var leadDate     = $('#lead_date').val();
    
        // Ensure both fields have values before making the AJAX request 
        if (leadSourceId && teleCallerId) {
            $.ajax({
                url: '<?= base_url('app/leads/get_leads_by_source') ?>',
                type: 'POST', 
                data: { lead_source_id: leadSourceId, tele_caller_id: teleCallerId,created_at: leadDate },
                success: function(response) { 
                    $('#lead_table_body').html(response);  
                    $('#check_all').prop('checked', false); // Uncheck "Check All" when the list is refreshed
                    toggleSubmitButton(); // Disable submit button after table refresh
                }, 
                error: function(xhr, status, error) { 
                    console.log('Error fetching leads. Please try again.');
                    console.log(xhr.responseText); // Log the error
                }
            });
        } else {
            $('#lead_table_body').html(''); // Clear table if one of the fields is empty
            toggleSubmitButton(); // Disable submit button
        }
    });

});

</script>

<style>
    .reassign-table {
        max-height: 300px; /* Set the desired max height for the scrollable table */
        overflow-y: auto;
    }

    /* Ensures the header stays aligned with the body */
    table thead th {
        background-color: #fff;
    }

    table thead tr {
        position: sticky;
        top: 0;
        background-color: #fff;
        border: 1px solid #ddd;
    }

    /* Checkbox size */
    input[type="checkbox"] {
        width: 22px;
        height: 22px;
    }
</style>
