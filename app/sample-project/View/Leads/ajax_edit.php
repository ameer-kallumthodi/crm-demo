<?php
    if (isset($edit_data)){
        ?>
        <form action="<?=base_url('app/leads/edit/'.$edit_data['id'])?>" method="post">
            <div class="row g-3">
                <div class="col-lg-12">
                    <div>
                        <label for="title" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" id="customername-field" class="form-control" id="title" name="title" value="<?=$edit_data['title']?>" placeholder="Enter name" required />
                    </div>
                </div>
                
                <div class="col-lg-3">
                    <div>
                        <label for="gender" class="form-label">Gender</label>
                        
                        <div class="hstack gap-2 flex-wrap">
                            <input type="radio" class="btn-check" name="gender" id="gender-male" value="male"  <?= ($edit_data['gender'] === 'male') ? 'checked' : '' ?>>
                            <label class="btn btn-outline-primary" for="gender-male">Male</label>
                        
                            <input type="radio" class="btn-check" name="gender" id="gender-female" value="female" <?= ($edit_data['gender'] === 'female') ? 'checked' : '' ?>>
                            <label class="btn btn-outline-primary" for="gender-female">Female</label>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div>
                        <label for="age" class="form-label">Age </label>
                        <input type="number" class="form-control" placeholder="Enter Age" id="age" name="age" value="<?=$edit_data['age']?>" oninput="number_length(3, 'age')" />
                    </div>
                </div>
                <div class="col-lg-6">
                    <div>
                        <label for="place" class="form-label">Place</label>
                        <input type="text" class="form-control" placeholder="Enter Place" id="place" name="place" value="<?=$edit_data['place']?>" />
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div>
                        <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
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
                                    <input type="number" name="phone" id="phone"  class="form-control" oninput="number_length(15, 'phone')" placeholder="Enter phone no" value="<?=$edit_data['phone']?>" required />
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="col-lg-6">
                    <div>
                        <label for="whatsapp" class="form-label">Whatsapp number</label>
                        <div class="col-sm-12">
                            <div class="input-group">
                                <div class="col-sm-3">
                                    <select class="form-control select2" name="whatsapp_code">
                                        <?php
                                        foreach ($country_code as $code => $country) {
                                            $code_displayed = false;
                                            foreach ($country_code as $c => $ctry) {
                                                if ($c === $code) {
                                                    if (!$code_displayed) {
                                                        // Check if the current option matches the value from the database
                                                        $selected = ($edit_data['whatsapp_code'] == $code) ? 'selected' : '';
                                                        ?>
                                                        <option value="<?= $code ?>" <?= $selected ?>><?= $code ?> - <?= $country ?></option>
                                                        <?php
                                                        $code_displayed = true;
                                                    }
                                                    if ($ctry !== $country) {
                                                        // Check if the current option matches the value from the database
                                                        $selected = ($edit_data['whatsapp_code'] == $code) ? 'selected' : '';
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
                                    <input type="number" name="whatsapp" id="whatsapp"  class="form-control" oninput="number_length(15, 'whatsapp')" placeholder="Enter Whatsapp number" value="<?=$edit_data['whatsapp']?>" required />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-12">
                    <div>
                        <label for="email_id-field" class="form-label">Email ID <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" value="<?=$edit_data['email']?>" required />
                    </div>
                </div>
                <div class="col-lg-12">
                    <div>
                        <label for="qualification" class="form-label">Qualification</label>
                        <input type="text" class="form-control" id="qualification" name="qualification" placeholder="Enter Qualification" value="<?=$edit_data['qualification']?>" />
                    </div>
                </div>
                <div class="col-lg-12">
                    <div>
                        <label for="country_id" class="form-label font-size-13 text-muted">Which country you wish to migrate</label>
                        <select class="form-control select2" name="country_id" id="country_id">
                            <option value="">Select Country</option>
                            <?php foreach($country_list as $list) { 
                                $selected_country = $list['id'] == $edit_data['country_id'] ? 'selected':'';
                                echo "<option value=\"{$list['id']}\" {$selected_country}>{$list['title']}</option>";
                            }
                            ?>
                            
                        
                        </select>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div>
                        <label for="interest_status" class="form-label font-size-13 text-muted">Interest status</label>
                        <select class="form-control select2" name="interest_status" id="interest_status">
                            <option value="">Select Status</option>
                            <option value="1" <?= ($edit_data['interest_status'] == 1)? 'selected' : '';?>>Hot</option>
                            <option value="2" <?= ($edit_data['interest_status']  == 2)? 'selected' : '';?>>Warm</option>
                            <option value="3" <?= ($edit_data['interest_status']  == 3)? 'selected' : '';?>>Cold</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div>
                        <label for="lead_status_id" class="form-label font-size-13 text-muted">Lead status</label>
                        <select class="form-control select2" name="lead_status_id" id="lead_status_id">
                            <option value="">Select Lead Status</option>
                            <?php foreach($lead_status as $status) { 
                                $selected_lead_status = $status['id'] == $edit_data['lead_status_id'] ? 'selected':'';
                                echo "<option value=\"{$status['id']}\" {$selected_lead_status}>{$status['title']}</option>";
                            }
                            ?>
             
                        </select>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div>
                        <label for="lead_source_id" class="form-label font-size-13 text-muted">Lead Source</label>
                        <select class="form-control select2" name="lead_source_id" id="lead_source_id" required>
                            <option value="">Select Source</option>
                            <?php foreach($lead_source as $source) {
                                $selected_lead_Source = $source['id'] == $edit_data['lead_source_id'] ? 'selected' : '';
                                echo "<option value = \" {$source['id']}\" {$selected_lead_Source} > {$source['title']}</option>";
                            }
                            ?>
                     
                        </select>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div>
                        <label for="lead_stage_id" class="form-label font-size-13 text-muted">Lead Stage</label>
                        <select class="form-control select2" name="lead_stage_id" id="lead_stage_id">
                            <option value="">Select Stage</option>
                            <?php foreach($lead_stages as $stage) {
                                $selected_lead_Stage = $stage['id'] == $edit_data['lead_stage_id'] ? 'selected' : '';
                                echo "<option value = \" {$stage['id']}\" {$selected_lead_Stage} > {$stage['title']}</option>";
                            }
                            ?>
                     
                        </select>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div>
                        <label for="university_id" class="form-label">University </label>
                        <select class="form-control select2" name="university_id" id="university_id" >
                            <option value="">Select University</option>
                            <?php foreach($university_list as $university) { 
                                $selected_university = $university['id'] == $edit_data['university_id'] ? 'selected' : '';
                                echo "<option value = \" {$university['id']}\" {$selected_university} > {$university['title']}</option>";
                             
                                }
                            ?>
                        </select>
                    </div>
                </div>
                
                 <div class="col-lg-4">
                    <div>
                        <label for="course_id" class="form-label">Course Interested </label>
                        <select class="form-control select2" name="course_id" id="course_id" >
                            <option value="">Select Course</option>
                            <?php foreach($course_list as $course) { 
                                $selected_course = $course['id'] == $edit_data['course_id'] ? 'selected' : '';
                                echo "<option value = \" {$course['id']}\" {$selected_course} > {$course['title']}</option>";
                             
                                }
                            ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-lg-12">
                    <div>
                        <label for="address" class="form-label">Address </label>
                        <input type="text" class="form-control" name="address" id="address" placeholder="Enter Address " value="<?=$edit_data['address']?>" />
                    </div>
                </div>
                
                
                <div class="col-lg-12">
                    <div>
                        <label for="telecaller_id" class="form-label font-size-13 text-muted">Tele Caller</label>
                        <select class="form-control select2" name="telecaller_id" id="telecaller_id">
                            <option value="">Select Tele Caller</option>
                            <?php
                                foreach($tele_callers as $caller){
                                  $selected_telecaller = $caller['id'] == $edit_data['telecaller_id'] ? 'selected' : '';
                                echo "<option value = \" {$caller['id']}\" {$selected_telecaller} > {$caller['name']}</option>";
                            }   
                           
                            ?>
                            
                        </select>
                    </div>
                </div>
                
                
                   <div class="col-lg-12">
                        <div>
                            <label for="remarks" class="form-label font-size-13 text-muted">Remarks</label>
                            <input type="text" class="form-control select2" name="remarks" value="<?=$edit_data['remarks']?>">
                        </div>
                    </div>
                <div class="col-12 p-2">
                    <button class="btn btn-success float-end" type="submit"><i class="ri-check-fill"></i>Save</button>
                </div>
            </div>
        </form>
        <?php
    }
?>

