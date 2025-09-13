<?php
    if (isset($edit_data) && !empty($edit_data)){
        ?>
        <form method="post" action="<?=base_url('app/leads/convert_lead')?>" >
            <input type="hidden" name="lead_id" value="<?=$edit_data['id']?>">
            <div class="row g-3">
                <div class="col-lg-12">
                    <div>
                        <label for="name" class="form-label font-size-13 text-muted">Name<span class="required text-danger">*</span></label>
                        <input type="text" class="form-control select2" name="name" value="<?=$edit_data['title'] ?? ''?>" required>
                    </div>
                </div>
                <div class="col-12 form-group p-2">
                    <div>
                        <label for="phone" class="form-label">Phone<span class="required text-danger">*</span></label>
                        <div class="col-sm-12">
                            <div class="input-group">
                                <div class="col-sm-3">
                                    <select class="form-control select2" name="code" required>
                                        <?php
                                        foreach ($country_code as $code => $country) {
                                            $code_displayed = false;
                                            foreach ($country_code as $c => $ctry) {
                                                if ($c === $code) {
                                                    if (!$code_displayed) {
                                                        // Check if the current option matches the value from the database
                                                        $selected = ($edit_data['code'] == $code) ? 'selected' : '';
                                                        ?>
                                                        <option value="<?= $code ?>" <?= $selected ?>><?= $code ?> - <?= $country ?></option>
                                                        <?php
                                                        $code_displayed = true;
                                                    }
                                                    if ($ctry !== $country) {
                                                        // Check if the current option matches the value from the database
                                                        $selected = ($edit_data['code'] == $code) ? 'selected' : '';
                                                        ?>
                                                        <option value="<?= $code ?>" <?= $selected ?>><?= $code ?> - <?= $ctry ?></option>
                                                        <?php
                                                    }
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" name="phone" id="phone"  class="form-control" oninput="number_length(15, 'phone')" value="<?=$edit_data['phone'] ?? ''?>" placeholder="Enter phone no" required />
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="col-12 form-group p-2">
                    <label for="email" class="form-label">Email<span class="required text-danger">*</span></label>
                    <input type="email" class="form-control" name="email" id="email" value="<?= $edit_data['email'] ?? '' ?>" placeholder="Email" required>
                </div>
                
                <div class="col-lg-6">
                    <div>
                        <label for="board_id" class="form-label font-size-13 text-muted">Board<span class="required text-danger">*</span></label>
                        <select class="form-control select2" name="board_id" id="board_id" required>
                            <option value="">Select Board</option>
                            <?php if(isset($boards)){ 
                                foreach($boards as $board){
                            ?>
                                <option value="<?= $board['id'] ?>"><?= $board['title'] ?></option>
                            <?php } } ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div>
                        <label for="batch_id" class="form-label font-size-13 text-muted">Batch<span class="required text-danger">*</span></label>
                        <select class="form-control select2" name="batch_id" id="batch_id" required>
                            <option value="">Select Batch</option>
                            <?php if(isset($batches)){ 
                                foreach($batches as $batch){
                            ?>
                                <option value="<?= $batch['id'] ?>"><?= $batch['title'] ?></option>
                            <?php } } ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div>
                        <label for="course_id" class="form-label font-size-13 text-muted">Course<span class="required text-danger">*</span></label>
                        <select class="form-control select2" name="course_id" id="course_id" required>
                            <option value="">Select Course</option>
                            <?php if(isset($courses)){ 
                                foreach($courses as $course){
                            ?>
                                <option value="<?= $course['id'] ?>"><?= $course['title'] ?></option>
                            <?php } } ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div>
                        <label for="academic_assistant_id" class="form-label font-size-13 text-muted">Accademic Assistant<span class="required text-danger">*</span></label>
                        <select class="form-control select2" name="academic_assistant_id" id="academic_assistant_id" required>
                            <option value="">Select Accademic Assistant</option>
                            <?php if(isset($academic_assistants)){
                                foreach($academic_assistants as $board){
                            ?>
                                <option value="<?= $board['id'] ?>"><?= $board['name'] ?></option>
                            <?php } } ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div>
                        <label for="subject_id" class="form-label font-size-13 text-muted">Subject<span class="required text-danger">*</span></label>
                        <select class="form-control select2" name="subject_id" id="subject_id" required>
                            <option value="">Select Subject</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div>
                        <label for="remarks" class="form-label font-size-13 text-muted">Remarks</label>
                        <input type="text" class="form-control select2" name="remarks">
                    </div>
                </div>
                
                <div class="col-12 p-2">
                    <button class="btn btn-success float-end" id="submit_button" type="submit"><i class="ri-check-fill"></i>Save</button>
                </div>
            </div>
        </form>
        
        <script>
            $(document).on('change', '#course_id', function() {
                const course_id = $(this).val(); 
                $.post("<?php echo base_url('app/subjects/get_subject_by_course'); ?>", 
                    { course_id: course_id }, 
                    function(data) {
                        $('#subject_id').html(data);
                    }
                );
            });

        </script>
        
        <?php
    }
?>

  