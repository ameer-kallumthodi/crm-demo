<form action="<?=base_url('app/leads/bulk_upload_add')?>" method="post" enctype="multipart/form-data">
    <div class="row g-3">
        <div class="col-12">
            <a href="<?=base_url('uploads/lead_sample.xlsx')?>" style="float: right"
               class="btn btn-primary btn-sm float-end">
                Download Template <i class="ri ri-file-excel-2-line"></i>
            </a>
        </div>
        <div class="col-lg-12">
            <div class="p-1">
                <label for="excel_title" class="form-label">Excel title</label>
                <input type="text"  class="form-control" id="excel_title" name="excel_title" placeholder="Excel title" required />
            </div>
            <div class="p-1">
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
            
            
             <div class="p-1">
                <div>
                <label for="course_id" class="form-label font-size-13 text-muted">Course  </label>
                <select class="form-control select2" name="course_id" id="course_id" required>
                    <option value="">Select Course</option>
                    <?php foreach($course_list as $course) { ?>
                    <option value="<?= $course['id']?>"><?= $course['title']?></option>
                      <?php 
                        }
                    ?>
                </select>
            </div>
            </div>
            
            <div class="p-1">
                <label for="excel_file" class="form-label font-size-13 text-muted">Upload File</label>
                <input type="file"  class="form-control" id="excel_file" name="excel_file" placeholder="Excel File" required />
            </div>
            <hr>
            <div class="form-group col-12">
                <label for="assign_all_counselor">Assign to all Telecallers - <span class="text-info" style="font-size: 12px;">Leads will be assigned to all team lead equally.</span></label>
                <input type="checkbox" class="form-check-input" id="assign_all_team_lead" name="assign_all_team_lead" value="1" onchange="assign_telecaller(this)" checked>
                <div style="display: none" id="telecaller_list">
                    <hr>
                    <div class="text-center p-3 text-info">
                        Uploaded leads will be assigned to the selected Telecallers.
                    </div>
                        <table class="table table-striped table-bordered">
                            <tr>
                                <th>#</th>
                                <th>Telecaller</th>
                                <th>Action</th>
                            </tr>
                            <?php
                            if (isset($tele_callers)){
                                foreach ($tele_callers as $key => $tele_caller){
                                    ?>
                                    <tr>
                                        <td><?=$key+1?></td>
                                        <td>
                                            <?=$tele_caller['name']?><br>
                                            <?=$tele_caller['phone']?>
                                        </td>
                                        <td>
                                            <input type="checkbox" class="form-check-input" id="<?=$tele_caller['id']?>" name="tele_callers[]" value="<?=$tele_caller['id']?>" checked>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </table>
                </div>
            </div>
        </div>

        <div class="col-12 p-2">
            <button class="btn btn-success float-end" type="submit"><i class="ri-check-fill"></i> Upload Leads</button>
        </div>
    </div>
</form>

<script>
    $('.select2').select2();

    // ASSIGN TO ALL TELECALLERS OR NOT
    function assign_telecaller(checkbox){
        if (checkbox.checked){
            $('#telecaller_list').hide();
        }else{
            $('#telecaller_list').show();
        }
    }
</script>
<style>
    input[type="checkbox"] {
        width: 22px;
        height: 22px;
    }
</style>