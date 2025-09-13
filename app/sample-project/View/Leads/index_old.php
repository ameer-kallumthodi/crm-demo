<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0"><?=$page_title ?? ''?></h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?=base_url('app/dashboard/index')?>">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?=$page_title ?? ''?></li>
                </ol>
            </div>
 
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-8">
                        <h5 class="card-title mb-0"><?=$page_title ?? ''?></h5>
                    </div>
                    <div class="col-12">
                        <?php if(is_admin() || is_team_lead()){ ?>
                            <button class="btn btn-md btn-primary float-end "
                                    onclick="show_ajax_modal('<?=base_url('app/leads/ajax_add/')?>', 'Add Leads')">
                                <i class="mdi mdi-plus"></i>
                                Create <?=$page_title ?? ''?>
                            </button>
                            <button class="btn btn-md btn-success float-end me-2"
                                    onclick="show_small_modal('<?=base_url('app/leads/ajax_bulk_upload/')?>', 'Bulk Upload')">
                                <i class="mdi mdi-cloud-upload-outline"></i>
                                Bulk Upload
                            </button>
                        <?php } 
                        if(is_admin() || is_team_lead()){
                        ?>    
                            <button class="btn btn-md btn-info float-end me-2"
                                    onclick="show_large_modal('<?= base_url('app/leads/ajax_bulk_reassign/') ?>', 'Bulk Telecaller Re-assign')">
                                <i class="mdi mdi-cloud-upload-outline"></i>
                                Bulk Telecaller Re-assign
                            </button>
                        <?php } 
                        if(is_admin()){
                        ?> 
                            <button class="btn btn-md btn-danger float-end me-2"
                                    onclick="show_large_modal('<?= base_url('app/leads/ajax_bulk_delete/') ?>', 'Bulk Delete')">
                                <i class="mdi mdi-cloud-upload-outline"></i>
                                Bulk Delete
                            </button>
                        <?php }
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <form method="get" action="">
                        <div class="row g-3">
                            <!--end col-->
                            <div class="col-xxl-2 col-sm-4">
                                <label for="from_date" class="form-label">From Date</label>
                                <input type="date" name="from_date" class="form-control bg-light border-light" required value="<?= $from_date ?>" >
                            </div>

                            <div class="col-xxl-2 col-sm-4">
                                <label for="to_date" class="form-label">To Date</label>
                                <input type="date" name="to_date" class="form-control bg-light border-light" required value="<?= $to_date ?>" >
                            </div>
                            <!--end col-->
                            <div class="col-xxl-2 col-sm-4">
                                <div class="input-light">
                                    <label for="lead_status" class="form-label">Lead Status</label>
                                    <select class="form-control" name="lead_status" id="lead_status">
                                        <option value="0" <?= !isset($_GET['lead_status']) || $_GET['lead_status'] == 0 ? 'selected' : '' ?> >All</option>
                                        <?php foreach($lead_status as $status){ ?>
                                            <option value="<?=$status['id']?>" <?= isset($_GET['lead_status']) && $_GET['lead_status'] == $status['id'] ? 'selected' : '' ?> ><?=$status['title']?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <!--end col-->

                            <div class="col-xxl-2 col-sm-4">
                                <div class="input-light">
                                    <label for="lead_source" class="form-label">Lead Source</label>
                                    <select class="form-control" name="lead_source" id="lead_source">
                                        <option value="0" <?= !isset($_GET['lead_source']) || $_GET['lead_source'] == 0 ? 'selected' : '' ?> >All</option>
                                        <?php foreach($lead_source as $source){ ?>
                                            <option value="<?=$source['id']?>" <?= isset($_GET['lead_source']) && $_GET['lead_source'] == $source['id'] ? 'selected' : '' ?> ><?=$source['title']?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <!--end col-->
                            
                            <div class="col-xxl-2 col-sm-4">
                                <div class="input-light">
                                    <label for="country_id" class="form-label">Country</label>
                                    <select class="form-control" name="country_id" id="country_id">
                                        <option value="0" <?= !isset($_GET['country_id']) || $_GET['country_id'] == 0 ? 'selected' : '' ?> >All</option>
                                        <?php foreach($countrys as $country){ ?>
                                            <option value="<?=$country['id']?>" <?= isset($_GET['country_id']) && $_GET['country_id'] == $country['id'] ? 'selected' : '' ?> ><?=$country['title']?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <!--end col-->
                            
                            <div class="col-xxl-2 col-sm-4">
                                <div class="input-light">
                                    <label for="university_id" class="form-label">University</label>
                                    <select class="form-control" name="university_id" id="university_id">
                                        <option value="0" <?= !isset($_GET['university_id']) || $_GET['university_id'] == 0 ? 'selected' : '' ?> >All</option>
                                        <?php foreach($university_list as $university){ ?>
                                            <option value="<?=$university['id']?>" <?= isset($_GET['university_id']) && $_GET['university_id'] == $university['id'] ? 'selected' : '' ?> ><?=$university['title']?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <!--end col-->
                            
                             <div class="col-xxl-2 col-sm-4">
                                <div class="input-light">
                                    <label for="course_id" class="form-label">Course</label>
                                    <select class="form-control" name="course_id" id="course_id">
                                        <option value="0" <?= !isset($_GET['course_id']) || $_GET['course_id'] == 0 ? 'selected' : '' ?> >All</option>
                                        <?php foreach($course_list as $course){ ?>
                                            <option value="<?=$course['id']?>" <?= isset($_GET['course_id']) && $_GET['course_id'] == $course['id'] ? 'selected' : '' ?> ><?=$course['title']?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <!--end col-->
                            
                            
                            <div class="col-xxl-2 col-sm-4">
                                <div class="input-light">
                                    <label for="telecaller_id" class="form-label">Tele Callers</label>
                                    <select class="form-control" name="telecaller_id" id="telecaller_id">
                                        <option value="0" <?= !isset($_GET['telecaller_id']) || $_GET['telecaller_id'] == 0 ? 'selected' : '' ?> >All</option>
                                        <?php foreach($telecaller as $tele){ ?>
                                            <option value="<?=$tele['id']?>" <?= isset($_GET['telecaller_id']) && $_GET['telecaller_id'] == $tele['id'] ? 'selected' : '' ?> ><?=$tele['name']?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            
                            
                             
                            <!--end col-->

                            <div class="col-xxl-2 col-sm-4 ">
                                <button type="submit" class="btn btn-primary w-100 mt-4">
                                    <i class="ri-equalizer-fill me-1 align-bottom"></i> Filters
                                </button>
                            </div>
                            <div class="col-xxl-1 col-sm-4 ">
                                <a href="<?= base_url('app/leads/index') ?>" class="btn btn-outline-dark w-100 mt-4">
                                    <i class="ri-arrow-go-back-line align-bottom"></i> Reset
                                </a>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </form>
                </div><!-- end row -->
            </div><!-- end card-body -->
        </div>
        
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="" class="data_table_basic table table-bordered nowrap table-striped align-middle" style="width:100%">
                        <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 120px;">Action</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Lead Source</th>
                            <th>Lead Status</th>
                            <th>Telecaller</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Course Interested</th>
                            <th>Remarks</th>
                            <th>E-mail</th>
                            <th>Gender</th>
                            <th>Age</th>
                            <th>Qualification</th>
                            <th>Address</th>
                            <th>Place</th>
                            <th>Created On</th>
                            <!--<th>Country</th>-->
                            <!--<th>University</th>-->
                            
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            if (isset($list_items)){
                                foreach ($list_items as $key => $list_item){ 
                                    ?>
                                    <tr>
                                        <td><?=$key + 1?></td>
                                        <td class="text-center">
                                            <a href="javascript:void(0);" class="btn btn-sm btn-success" onclick="makeCall('<?='+'.$list_item['code'].$list_item['phone']?>','<?=$list_item['id']?>')">
                                                <i class="ri-phone-fill"></i>
                                            </a>
                                            
                                            <a href="javascript:void(0);" class="btn btn-sm btn-danger" onclick="hangUp()">
                                                <i class="ri-phone-fill"></i>
                                            </a>
                                            
                                            <a href="javascript::void()" class="btn btn-sm btn-outline-primary" onclick="show_small_modal('<?=base_url('app/leads/ajax_lead_status/'.$list_item['id'])?>', 'Update Lead Status ')">
                                                <i class="ri-pencil-fill"></i> Status
                                            </a>
                                            <?php if ($list_item['lead_status'] == 'Demo') { ?>
                                                <a href="https://docs.google.com/forms/d/e/1FAIpQLSchtc8xlKUJehZNmzoKTkRvwLwk4-SGjzKSHM2UFToAhgdTlQ/viewform?usp=sf_link" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="ri-mac-fill"></i>
                                                </a>
                                            <?php } ?>
                                            <br><hr>
                                            
                                            <a href="javascript::void()" class="btn btn-sm btn-dark" onclick="show_large_modal('<?=base_url('app/leads/ajax_view/'.$list_item['id'])?>', 'View Lead Details')">
                                                <i class="ri-eye-fill"></i>
                                            </a>
                                            <a href="javascript::void()" class="btn btn-sm btn-info" onclick="show_ajax_modal('<?=base_url('app/leads/ajax_lead_convert/'.$list_item['id'])?>', 'Convert')">
                                                <i class="ri-refresh-line"></i>
                                            </a>
                                            <?php if(is_admin()){ ?>
                                                <a href="javascript::void()" class="btn btn-sm btn-warning" onclick="show_ajax_modal('<?=base_url('app/leads/ajax_edit/'.$list_item['id'])?>', 'Update Lead')">
                                                    <i class="ri-pencil-fill"></i>
                                                </a>
    
                                                <a href="javascript::void()" class="btn btn-sm btn-danger" onclick="delete_modal('<?=base_url('app/leads/delete/'.$list_item['id'])?>')">
                                                    <i class="ri-delete-bin-fill"></i>
                                                </a>
                                            <?php } ?>
                                        </td>
                                        <td><strong><?=$list_item['title']?></strong></td>
                                        <td><?='+'.$list_item['code'].' '.$list_item['phone']?></td>
                                        <td>
                                            <?= $list_item['lead_source'] ?? '' ?>

                                        </td>
                                        <td><?= $list_item['lead_status'] ?? '' ?></td>
                                        <td><?= $list_item['telecaller'] ?? '' ?></td>
                                        <td><?= $list_item['date'] ? date('d-m-Y', strtotime($list_item['date'])) : '' ?></td>
                                        <td><?= $list_item['time'] ? date('h:i A', strtotime($list_item['time'])) : '' ?></td>
                                        <td><?= $list_item['course'] ?? '' ?></td>
                                        <td><?=$list_item['remarks']?></td>
                                        <td><?=$list_item['email']?></td>
                                        <td><?=$list_item['gender']?></td>
                                        <td><?=$list_item['age']?></td>
                                        <td><?=$list_item['qualification']?></td>
                                        <td><?=$list_item['address']?></td>
                                        <td><?=$list_item['place']?></td>
                                        <td><?=$list_item['created_at'] ? date('Y-m-d h:i A', strtotime($list_item['created_at'])) : ''?></td>
                                        <!--<td><?//= $country_name[$list_item['country_id']] ?? '' ?></td>-->
                                        <!--<td><?//= $university_name[$list_item['university_id']] ?? '' ?></td>-->
                                    </tr>
                                    <?php
                                }
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div><!--end row-->

<script src="//media.twiliocdn.com/sdk/js/client/v1.13/twilio.min.js"></script>
<script>
    var connection; // Declare 'connection' at the global level so it's accessible by both functions

    // Fetch token from server
    fetch('<?=base_url('twilio/generateToken')?>')
        .then(response => response.json())
        .then(data => {
            Twilio.Device.setup(data.token);

            Twilio.Device.ready(function () {
                console.log("Twilio Device is ready.");
            });

            Twilio.Device.error(function (error) {
                console.error("Twilio Device error: " + error.message);
            });
        })
        .catch(error => console.error('Error fetching token:', error));

    // Make a call to the passed phone number
    function makeCall(phoneNumber,lead_id) {
        if (Twilio.Device.status() === "ready") {
            const params = {
                To: phoneNumber,
                lead_id : lead_id,
                telecaller_id : <?=get_user_id()?>
            };
            connection = Twilio.Device.connect(params); // Store the active connection
        } else {
            console.log("Twilio Device is not ready yet. Please wait.");
        }
    }

    // Hang up the active call
    function hangUp() {
        if (connection) {
            connection.disconnect(); // Disconnect the active connection
        } else {
            console.log("No active connection to disconnect.");
        }
    }

    // Listen for incoming calls
    Twilio.Device.incoming(function (incomingConnection) {
        console.log("Incoming call from: " + incomingConnection.parameters.From);
        incomingConnection.accept();
        connection = incomingConnection; // Store the incoming connection
    });
</script>