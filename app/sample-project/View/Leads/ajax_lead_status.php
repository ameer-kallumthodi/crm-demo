<?php
    if (isset($lead_id)){
        ?>
        <form method="post" action="<?=base_url('app/leads/update_lead_status')?>" >
            <input type="hidden" name="lead_id" value="<?=$lead_id?>">
            <div class="row g-3">
                
                <div class="col-lg-12">
                    <div>
                        <label for="lead_status_id" class="form-label font-size-13 text-muted">Lead Status <span class="text-danger">*</span></label>
                        <select class="form-control select2" name="lead_status_id" id="lead_status_id" required>
                            <option value="">Select Status</option>
                            <?php
                            if (isset($lead_status)){
                                foreach ($lead_status as $status){
                                    $selected_status = $status['id'] == $edit_data['lead_status_id'] ? 'selected':'';
                                    echo "<option value=\"{$status['id']}\" {$selected_status}>{$status['title']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-lg-12">
                    <div>
                        <label for="remarks" class="form-label font-size-13 text-muted">Remarks <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="remarks" value="<?=$edit_data['remarks']?>" required>
                    </div>
                </div>
                
                <div class="col-lg-12">
                    <div>
                        <label for="reason" class="form-label font-size-13 text-muted">Reason <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="reason" value="<?=$edit_data['reason']?>" required>
                    </div>
                </div>
                
                <div class="col-lg-12">
                    <div>
                        <label for="date" class="form-label font-size-13 text-muted">Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="date" name="date" value="<?=$edit_data['date']?>" required />
                    </div>
                </div>
                
                <div class="col-lg-12">
                    <div>
                        <label for="time" class="form-label font-size-13 text-muted">Time <span class="text-danger">*</span></label>
                        <input type="time" class="form-control" id="time" name="time" value="<?=$edit_data['time']?>" required />
                    </div>
                </div>
                <div class="col-lg-12" id="form_container">
                    <div>
                        <a href="https://docs.google.com/forms/d/e/1FAIpQLScA4_7YZOiD1sKQDHXJlUYY2mKYJoz1m-aoEgb9ZBgxjsB5GA/viewform?usp=sf_link" target="_blank" class="btn btn-primary mt-2" id="form_url">Open Google Form</a>
                    </div>
                </div>
                
                <div class="col-lg-12">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="send_mail" name="send_mail" value="1">
                        <label class="form-check-label font-size-13 text-muted" for="send_mail">Send mail</label>
                    </div>
                </div>

                <div class="col-12 p-2">
                    <button class="btn btn-success float-end" id="submit_button" type="submit"><i class="ri-check-fill"></i>Save</button>
                </div>
            </div>
        </form>
        
        <script>
            
            $(document).ready(function() {
                // Function to toggle the meeting_url input field based on lead status
                function toggleMeetingUrl() {
                    var leadStatus = $('#lead_status_id').val();
                    if (leadStatus == 19) {
                        $('#form_container').show();
                        $('#submit_button').prop('disabled', true);
                    } else {
                        $('#form_container').hide();
                        $('#submit_button').prop('disabled', false);
                    }
                }
            
                // Run the function on page load to check initial value
                toggleMeetingUrl();
            
                // Trigger the function when the dropdown value changes
                $('#lead_status_id').on('change', function() {
                    toggleMeetingUrl();
                });
            });
            
            $(document).ready(function () {
                $('#form_url').on('click', function () {
                    // Delay to simulate user opening the form in a new tab
                    setTimeout(function () {
                        $('#submit_button').prop('disabled', false);
                    }, 1000); // Adjust delay as needed
                });
            });
            </script>
        <?php
    }
?>

  