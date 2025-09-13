<form action="<?=base_url('app/leads/add')?>" method="post">
    <div class="row g-3">
        <div class="col-lg-12">
            <div>
                <label for="title" class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text"  class="form-control" id="title" name="title" placeholder="Enter name" required />
            </div>
        </div>
        
        <div class="col-lg-3">
            <div>
                <label for="gender" class="form-label">Gender</label>
                
                <div class="hstack gap-2 flex-wrap">
                    <input type="radio" class="btn-check" name="gender" id="gender-male" value="male" checked>
                    <label class="btn btn-outline-primary" for="gender-male">Male</label>
                
                    <input type="radio" class="btn-check" name="gender" id="gender-female" value="female">
                    <label class="btn btn-outline-primary" for="gender-female">Female</label>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div>
                <label for="age" class="form-label">Age </label>
                <input type="number" class="form-control" placeholder="Enter Age" id="age" name="age" oninput="number_length(3, 'age')" />
            </div>
        </div>
        <div class="col-lg-6">
            <div>
                <label for="place" class="form-label">Place</label>
                <input type="text" class="form-control" placeholder="Enter Place" id="place" name="place" />
            </div>
        </div>
        
        <div class="col-lg-6">
            <div>
                <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                <div class="col-sm-12">
                    <div class="input-group">
                        <div class="col-sm-3">
                            <select class="form-control  select2" name="code" required>
                                <?php
                                    // Loop through the $country_code array
                                    foreach ($country_code as $code => $country) {
                                        $code_displayed = false;
                                
                                        // Loop through $country_code array again to display multiple countries for the same code
                                        foreach ($country_code as $c => $ctry) {
                                            if ($c === $code) {
                                                if (!$code_displayed) {
                                                    // Display the code and country for the first occurrence of the code
                                                    ?>
                                                    <option value="<?= $code ?>"><?= $code ?> - <?= $country ?></option>
                                                    <?php
                                                    $code_displayed = true;
                                                }
                                
                                                // Display other countries associated with the same code
                                                if ($ctry !== $country) {
                                                    ?>
                                                    <option value="<?= $code ?>"><?= $code ?> - <?= $ctry ?></option>
                                                    <?php
                                                }
                                            }
                                        }
                                    }
                                    ?>
                            </select>
                        </div>
                        <div class="col-sm-9">
                            <input type="number" name="phone" id="phone"  class="form-control" oninput="number_length(15, 'phone')" placeholder="Enter phone no" required />
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
                            <select class="form-control  select2" name="whatsapp_code">
                                <?php
                                    // Loop through the $country_code array
                                    foreach ($country_code as $code => $country) {
                                        $code_displayed = false;
                                
                                        // Loop through $country_code array again to display multiple countries for the same code
                                        foreach ($country_code as $c => $ctry) {
                                            if ($c === $code) {
                                                if (!$code_displayed) {
                                                    // Display the code and country for the first occurrence of the code
                                                    ?>
                                                    <option value="<?= $code ?>"><?= $code ?> - <?= $country ?></option>
                                                    <?php
                                                    $code_displayed = true;
                                                }
                                
                                                // Display other countries associated with the same code
                                                if ($ctry !== $country) {
                                                    ?>
                                                    <option value="<?= $code ?>"><?= $code ?> - <?= $ctry ?></option>
                                                    <?php
                                                }
                                            }
                                        }
                                    }
                                    ?>
                            </select>
                        </div>
                        <div class="col-sm-9">
                            <input type="number" name="whatsapp" id="whatsapp"  class="form-control" oninput="number_length(15, 'whatsapp')" placeholder="Enter Whatsapp number" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-12">
            <div>
                <label for="email_id-field" class="form-label">Email ID <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required />
            </div>
        </div>
        <div class="col-lg-12">
            <div>
                <label for="qualification" class="form-label">Qualification</label>
                <input type="text" class="form-control" id="qualification" name="qualification" placeholder="Enter Qualification" />
            </div>
        </div>
        <div class="col-lg-12">
            <div>
                <label for="country_id" class="form-label font-size-13 text-muted">Which country you wish to migrate</label>
                <select class="form-control select2" name="country_id" id="country_id">
                    <option value="">Select Country</option>
                    <?php foreach($country_list as $list) { ?>
                    <option value="<?= $list['id']?>"><?= $list['title'] ?></option>
                       <?php 
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
                    <option value="1">Hot</option>
                    <option value="2">Warm</option>
                    <option value="3">Cold</option>
                </select>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div>
                <label for="lead_status_id" class="form-label font-size-13 text-muted">Lead status</label>
                <select class="form-control select2" name="lead_status_id" id="lead_status_id">
                    <option value="">Select Lead Status</option>
                    <?php foreach($lead_status as $status) {  ?>
                    <option value="<?= $status['id']?>"><?= $status['title']?> </option>
                      <?php 
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
                    <?php foreach($lead_source as $source) { ?>
                    <option value="<?= $source['id']?>"><?= $source['title']?></option>
                      <?php 
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
                    <?php foreach($lead_stages as $stage) { ?>
                    <option value="<?= $stage['id']?>"><?= $stage['title']?></option>
                      <?php 
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
                    <?php foreach($university_list as $university) { ?>
                    <option value="<?= $university['id']?>"><?= $university['title']?></option>
                      <?php 
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
                    <?php foreach($course_list as $course) { ?>
                    <option value="<?= $course['id']?>"><?= $course['title']?></option>
                      <?php 
                        }
                    ?>
                </select>
            </div>
        </div>
        
        <div class="col-lg-12">
            <div>
                <label for="address" class="form-label">Address </label>
                <input type="text" class="form-control" name="address" id="address" placeholder="Enter Address " />
            </div>
        </div>
        
        
        <div class="col-lg-4">
            <div>
                <label for="telecaller_id" class="form-label font-size-13 text-muted">Tele Caller</label>
                <select class="form-control select2" name="telecaller_id" id="telecaller_id">
                    <option value="">Select Tele Caller</option>
                    <?php
                        foreach($tele_callers as $caller){
                    ?>
                        <option value="<?=$caller['id']?>"><?=$caller['name']?></option>
                    <?php 
                        }
                    ?>
                    
                </select>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div>
                <label for="date" class="form-label font-size-13 text-muted">Date</label>
                <input type="date" class="form-control" id="date" name="date" />
            </div>
        </div>
        <div class="col-lg-4">
            <div>
                <label for="time" class="form-label font-size-13 text-muted">Time</label>
                <input type="time" class="form-control" id="time" name="time" />
            </div>
        </div>
        
        <div class="col-lg-12">
            <div>
                <label for="remarks" class="form-label font-size-13 text-muted">Remarks</label>
                <input type="text" class="form-control select2" name="remarks">
            </div>
        </div>
        
        <div class="col-12 p-2">
            <button class="btn btn-success float-end" type="submit"><i class="ri-check-fill"></i>Save</button>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
      $('.select2').select2(); 
    });
</script>