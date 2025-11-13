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
    // Enable/Disable submit button based on telecaller selection and checkbox selection
    function toggleSubmitButton() {
        var telecallerSelected = $('#telecaller_id').val() !== '';
        // Find checkboxes - exclude the "check all" checkbox
        var checkboxes = $('#marketing_lead_table_body input[type="checkbox"]').not('#check_all');
        var anyChecked = checkboxes.filter(':checked').length > 0;
        
        var shouldEnable = telecallerSelected && anyChecked;
        $('#assign_btn').prop('disabled', !shouldEnable);
        
        // Update button visual state for better UX
        if (shouldEnable) {
            $('#assign_btn').removeClass('disabled');
        } else {
            $('#assign_btn').addClass('disabled');
        }
    }

    // Update "Check All" checkbox state based on individual checkboxes
    function updateCheckAllState() {
        var checkboxes = $('#marketing_lead_table_body input[type="checkbox"]');
        var totalCheckboxes = checkboxes.length;
        var checkedCount = checkboxes.filter(':checked').length;
        
        // Update "Check All" checkbox state
        if (totalCheckboxes > 0) {
            $('#check_all').prop('checked', totalCheckboxes === checkedCount);
            // Also set indeterminate state if some (but not all) are checked
            $('#check_all').prop('indeterminate', checkedCount > 0 && checkedCount < totalCheckboxes);
        } else {
            $('#check_all').prop('checked', false);
            $('#check_all').prop('indeterminate', false);
        }
    }

    // Handle "Check All" functionality
    $(document).on('change', '#check_all', function() {
        var isChecked = $(this).is(':checked');
        $('#marketing_lead_table_body input[type="checkbox"]').prop('checked', isChecked);
        // Clear indeterminate state when manually toggled
        $(this).prop('indeterminate', false);
        updateCheckAllState();
        toggleSubmitButton();
    });

    // Handle individual checkbox changes - use multiple event handlers for maximum compatibility
    $(document).on('change click', '#marketing_lead_table_body input[type="checkbox"]', function(e) {
        // Prevent double triggering
        if (e.type === 'click' && $(this).is(':checkbox')) {
            // For click events on checkboxes, check if it's actually changing state
            var wasChecked = $(this).data('was-checked');
            var isNowChecked = $(this).is(':checked');
            if (wasChecked === isNowChecked) {
                return; // State didn't change, ignore
            }
            $(this).data('was-checked', isNowChecked);
        }
        
        // Small delay to ensure DOM is updated
        setTimeout(function() {
            updateCheckAllState();
            toggleSubmitButton();
        }, 10);
    });

    // Also use MutationObserver as a fallback to catch any checkbox changes
    if (typeof MutationObserver !== 'undefined') {
        var observer = new MutationObserver(function(mutations) {
            var shouldUpdate = false;
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'checked') {
                    shouldUpdate = true;
                }
                if (mutation.type === 'childList') {
                    shouldUpdate = true;
                }
            });
            if (shouldUpdate) {
                setTimeout(function() {
                    updateCheckAllState();
                    toggleSubmitButton();
                }, 50);
            }
        });

        // Observe the tbody for changes
        var tbody = document.getElementById('marketing_lead_table_body');
        if (tbody) {
            observer.observe(tbody, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ['checked']
            });
        }
    }

    // Handle telecaller selection change
    $(document).on('change', '#telecaller_id', function() {
        toggleSubmitButton();
    });

    // Select checkboxes based on entered number
    $('#select_count').on('input', function() {
        var count = parseInt($(this).val()) || 0;
        var checkboxes = $('#marketing_lead_table_body input[type="checkbox"]');

        // Uncheck all first
        checkboxes.prop('checked', false);

        // Check only the specified number of checkboxes
        checkboxes.slice(0, count).prop('checked', true);

        updateCheckAllState();
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
                    $('#check_all').prop('indeterminate', false);
                    // Reset checkbox state tracking
                    $('#marketing_lead_table_body input[type="checkbox"]').each(function() {
                        $(this).data('was-checked', $(this).is(':checked'));
                    });
                    // Update check all state and button state after loading new data
                    setTimeout(function() {
                        updateCheckAllState();
                        toggleSubmitButton();
                    }, 100);
                },
                error: function(xhr) {
                    console.log('Error fetching marketing leads:', xhr.responseText);
                    $('#marketing_lead_table_body').html('<tr><td colspan="8" class="text-center">Error loading marketing leads. Please try again.</td></tr>');
                    toggleSubmitButton();
                }
            });
        } else {
            $('#marketing_lead_table_body').html('');
            toggleSubmitButton();
        }
    });
    
    // Periodic check as ultimate fallback (runs every 500ms)
    var lastButtonState = null;
    setInterval(function() {
        var checkboxes = $('#marketing_lead_table_body input[type="checkbox"]').not('#check_all');
        var anyChecked = checkboxes.filter(':checked').length > 0;
        var telecallerSelected = $('#telecaller_id').val() !== '';
        var shouldEnable = telecallerSelected && anyChecked;
        var currentState = shouldEnable ? 'enabled' : 'disabled';
        
        // Only update if state has changed to avoid unnecessary DOM manipulation
        if (currentState !== lastButtonState) {
            toggleSubmitButton();
            updateCheckAllState();
            lastButtonState = currentState;
        }
    }, 500);

    // Also trigger toggle when page loads in case there are pre-selected checkboxes
    setTimeout(function() {
        updateCheckAllState();
        toggleSubmitButton();
    }, 200);
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
