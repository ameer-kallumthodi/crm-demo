<?php
    if (isset($view_data)){
        ?>
        
        <div class="row">
            <div class="col-6">
                <div class="col-12">
                    <div class="card card-widget widget-user shadow-pro">
                        <div class="widget-user-header bg-primary-lighten p-2">
        
                        </div>
                        <div class="widget-user-image">
                            <img class="img-circle elevation-2" src="<?=base_url()?>assets/app/images/place-holder/profile-place-holder.jpg?>" alt="User Avatar">
                        </div>
                        <div class="card-footer">
                            <div class="row justify-content-center text-center"> 
                                <div class="col-sm-4 border-right text-center">
                                    <div class="description-block text-center">
                                        <!--<span class="description-text" style="font-size: 13px;"><strong>Name:</strong></span>-->
                                        <h5 class="description-header mb-2" style="font-size: 16px;"><?=$view_data['title'] ?? ''?></h5>
                                        <!--<span class="description-text" style="font-size: 11px;"><strong>Email:</strong></span>-->
                                        <h6 class="description-header mb-2" style="font-size: 11px;"><?=$view_data['email']?></h6>
                                        <!--<span class="description-text" style="font-size: 11px;"><strong>Phone:</strong></span>-->
                                        <h6 class="description-header mb-2" style="font-size: 11px;"><?=$view_data['code'].$view_data['phone']?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 row">
                    <div class="col-6">
                        <div class="info-box shadow-pro">
                            <div class="info-box-content">
                                <span class="info-box-text">Place</span>
                                <span class="info-box-number"><?=$view_data['place']?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="info-box shadow-pro">
                            <div class="info-box-content">
                                <span class="info-box-text">Qualification</span>
                                <span class="info-box-number"><?=$view_data['qualification']?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="info-box shadow-pro">
                            <div class="info-box-content bg-gradient-light">
                                <span class="info-box-text">Lead Status</span>
                                <span class="info-box-number fs-12">
                                    <?= isset($lead_status_list[$view_data['lead_status_id']]) ? $lead_status_list[$view_data['lead_status_id']] : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="info-box shadow-pro">
                            <div class="info-box-content bg-gradient-light">
                                <span class="info-box-text">Date</span>
                                <span class="info-box-number fs-12"><?= $view_data['date'] ? date('d-m-Y', strtotime($view_data['date'])) : ''?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="info-box shadow-pro">
                            <div class="info-box-content bg-gradient-light">
                                <span class="info-box-text">Time</span>
                                <span class="info-box-number fs-12"><?= $view_data['time'] ? date('h:i A', strtotime($view_data['time'])) : ''?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="info-box shadow-pro">
                            <div class="info-box-content bg-gradient-light">
                                <span class="info-box-text">Remarks</span>
                                <span class="info-box-number fs-12"><?=$view_data['remarks']?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="info-box shadow-pro">
                            <div class="info-box-content bg-gradient-light">
                                <span class="info-box-text">Reason</span>
                                <span class="info-box-number fs-12"><?=$view_data['reason']?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 overflow-scroll" style="height:500px;">
                <div class="card card-widget shadow-sm card-outline lead-history">
                    <div class="card-header d-flex justify-content-between">
                        <h3 class="card-title text-info" style=""><?=strtoupper('Lead History')?></h3>
                        <button class="btn btn-primary" onclick="printLeadHistory()">Print</button>
                    </div>
    
                    <div class="card-body p-1 lead-history-body">
                        <div id="accordion" class="d-none3">
                            <?php
                            if(isset($lead_history)){
                                foreach ($lead_history as $key => $item){
                                    ?>
                                    <div class="card card-widget shadow-pro">
                                        <div class="card-body">
                                            <!--<div style="font-size: 19px;font-weight: bold" class="text-primary"><?//=$leads_name[$item['lead_id']]?></div>-->
                                            <div style="font-size: 14px;font-weight: bold"><?= isset($lead_status_list[$item['lead_status_id']]) ? $lead_status_list[$item['lead_status_id']] : '' ?> : 
                                            <?= $item['date'] !== null ? date('d-m-Y', strtotime($item['date'])) : '' ?> <?= $item['time'] !== null ? date('h:i A', strtotime($item['time'])) : '' ?>
                                            </div>
                                            <?php if($item['remarks'] != null){ ?>
                                                <div style="font-size: 14px;" >
                                                    <b>Remarks:</b> <?=$item['remarks']?>
                                                </div>
                                            <?php } ?>
                                            <?php if($item['reason'] != null){ ?>
                                                <div style="font-size: 14px;" >
                                                    <b>Reason:</b> <?=$item['reason']?>
                                                </div>
                                            <?php } ?>
                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                <div style="font-size: 14px;" class="text-muted">
                                                    <b>Last updated on:</b> <?= $item['created_at'] !== null ? date('d-m-Y', strtotime($item['created_at'])) : '' ?>
                                                </div>
                                                <div style="font-size: 14px;" class="text-muted">
                                                    <b>Updated By:</b> <?= isset($all_users[$item['created_by']]) ? $all_users[$item['created_by']] : '' ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }else{
                            ?>
                                <div class="card card-widget shadow-pro">
                                    <div class="card-body">
                                        <div style="font-size: 19px;font-weight: bold" class="text-primary">No History</div>
                                    </div>
                                </div>
                            <?php } ?>
     
                        </div>
                    </div>
    
                </div>
            </div>
        </div>
        
        <script>
            function printLeadHistory() {
                // Get the lead history div content
                var content = document.querySelector('.lead-history-body').innerHTML;
        
                // Get the lead name from the appropriate field (adjust this selector if necessary)
                var leadName = "<?=strtoupper($view_data['title']) ?? ''?>";
        
                // Create a new window to print the div content
                var printWindow = window.open('', '', 'height=600,width=800');
        
                // Add necessary styles and content for printing
                printWindow.document.write('<html><head><title>Print Lead History</title>');
                printWindow.document.write('<style>');
                
                // Add CSS for a clean and user-friendly print view
                printWindow.document.write(`
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .card { border: 1px solid #ddd; margin-bottom: 15px; padding: 15px; page-break-inside: avoid; }
                    .card-header { font-size: 18px; font-weight: bold; margin-bottom: 10px; }
                    .card-body { padding: 10px; font-size: 14px; line-height: 1.6; }
                    .card-title { font-size: 16px; font-weight: bold; margin-bottom: 10px; }
                    .text-muted { color: #666; }
                    .d-flex { display: flex; }
                    .justify-content-between { justify-content: space-between; }
                    .align-items-center { align-items: center; }
                    .mt-2 { margin-top: 0.5rem; }
                    /* Make the print layout table-like */
                    .lead-history table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                    .lead-history table, .lead-history th, .lead-history td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    .lead-history th { background-color: #f2f2f2; }
                    .btn { display: none; } /* Hide the print button in the print view */
                    @media print {
                        .card { page-break-inside: avoid; }
                    }
                `);
                printWindow.document.write('</style>');
                printWindow.document.write('</head><body>');
        
                // Insert the lead name and the lead history content
                printWindow.document.write('<h3 style="text-align: center;">LEAD HISTORY OF ' + leadName + '</h3>');
                printWindow.document.write('<div>' + content + '</div>');
        
                // Close the document to apply styles
                printWindow.document.write('</body></html>');
                printWindow.document.close();
        
                // Trigger print
                printWindow.print();
            }
        </script>
        
        <style>
            .bg-primary-lighten
            {
                background-color:rgba(114,124,245,.25)!important
            }
            .widget-user .widget-user-image{
                left: 50%;
                margin-left: -45px;
                position: absolute;
                top: 80px;
            }
            .img-circle {
                border-radius: 50%;
            }
            .elevation-2 {
                box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23) !important;
            }
            .widget-user .widget-user-header {
              border-top-left-radius: 0.25rem;
              border-top-right-radius: 0.25rem;
              height: 135px;
              padding: 1rem;
              text-align: center;
            }
            
            .widget-user .widget-user-username {
              font-size: 25px;
              font-weight: 300;
              margin-bottom: 0;
              margin-top: 0;
              text-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
            }
            
            .widget-user .widget-user-desc {
              margin-top: 0;
            }
            
            .widget-user .widget-user-image {
              left: 50%;
              margin-left: -45px;
              position: absolute;
              top: 80px;
            }
            
            .widget-user .widget-user-image > img {
              border: 3px solid #ffffff;
              height: auto;
              width: 90px;
            }
            
            .widget-user .card-footer {
              padding-top: 35px;
            }
            
            .widget-user-2 .widget-user-header {
              border-top-left-radius: 0.25rem;
              border-top-right-radius: 0.25rem;
              padding: 1rem;
            }
            
            .widget-user-2 .widget-user-username {
              font-size: 25px;
              font-weight: 300;
              margin-bottom: 5px;
              margin-top: 5px;
            }
            
            .widget-user-2 .widget-user-desc {
              margin-top: 0;
            }
            
            .widget-user-2 .widget-user-username,
            .widget-user-2 .widget-user-desc {
              margin-left: 75px;
            }
            
            .widget-user-2 .widget-user-image > img {
              float: left;
              height: auto;
              width: 65px;
            }
            .description-block {
              display: block;
              margin: 10px 0;
              text-align: center;
            }
            
            .description-block.margin-bottom {
              margin-bottom: 25px;
            }
            
            .description-block > .description-header {
              font-size: 16px;
              font-weight: 600;
              margin: 0;
              padding: 0;
            }
            
            .description-block > .description-text {
              text-transform: uppercase;
            }
            
            .description-block .description-icon {
              font-size: 16px;
            }
            .info-box {
              /*box-shadow: 0 0 1px rgba(0, 0, 0, 0.125), 0 1px 3px rgba(0, 0, 0, 0.2);*/
              box-shadow: 5px 1px 15px rgb(0 0 0 / 6%);
            
              border-radius: 0.25rem;
              background: #ffffff;
              display: -ms-flexbox;
              display: flex;
              margin-bottom: 1rem;
              min-height: 80px;
              padding: .5rem;
              position: relative;
            }
            
            .info-box .progress {
              background-color: rgba(0, 0, 0, 0.125);
              height: 2px;
              margin: 5px 0;
            }
            
            .info-box .progress .progress-bar {
              background-color: #ffffff;
            }
            
            .info-box .info-box-icon {
              border-radius: 0.25rem;
              -ms-flex-align: center;
              align-items: center;
              display: -ms-flexbox;
              display: flex;
              font-size: 1.875rem;
              -ms-flex-pack: center;
              justify-content: center;
              text-align: center;
              width: 70px;
            }
            
            .info-box .info-box-icon > img {
              max-width: 100%;
            }
            
            .info-box .info-box-content {
              -ms-flex: 1;
              flex: 1;
              padding: 5px 10px;
            }
            
            .info-box .info-box-number {
              display: block;
              font-weight: 700;
            }
            
            .info-box .progress-description,
            .info-box .info-box-text {
              display: block;
              overflow: hidden;
              text-overflow: ellipsis;
              white-space: nowrap;
            }
            
            .info-box .info-box .bg-primary,
            .info-box .info-box .bg-gradient-primary {
              color: #ffffff;
            }
            
            .info-box .info-box .bg-primary .progress-bar,
            .info-box .info-box .bg-gradient-primary .progress-bar {
              background-color: #ffffff;
            }
            
            .info-box .info-box .bg-secondary,
            .info-box .info-box .bg-gradient-secondary {
              color: #ffffff;
            }
            
            .info-box .info-box .bg-secondary .progress-bar,
            .info-box .info-box .bg-gradient-secondary .progress-bar {
              background-color: #ffffff;
            }
            
            .info-box .info-box .bg-success,
            .info-box .info-box .bg-gradient-success {
              color: #ffffff;
            }
            
            .info-box .info-box .bg-success .progress-bar,
            .info-box .info-box .bg-gradient-success .progress-bar {
              background-color: #ffffff;
            }
            
            .info-box .info-box .bg-info,
            .info-box .info-box .bg-gradient-info {
              color: #ffffff;
            }
            
            .info-box .info-box .bg-info .progress-bar,
            .info-box .info-box .bg-gradient-info .progress-bar {
              background-color: #ffffff;
            }
            
            .info-box .info-box .bg-warning,
            .info-box .info-box .bg-gradient-warning {
              color: #1F2D3D;
            }
            
            .info-box .info-box .bg-warning .progress-bar,
            .info-box .info-box .bg-gradient-warning .progress-bar {
              background-color: #1F2D3D;
            }
            
            .info-box .info-box .bg-danger,
            .info-box .info-box .bg-gradient-danger {
              color: #ffffff;
            }
            
            .info-box .info-box .bg-danger .progress-bar,
            .info-box .info-box .bg-gradient-danger .progress-bar {
              background-color: #ffffff;
            }
            
            .info-box .info-box .bg-light,
            .info-box .info-box .bg-gradient-light {
              color: #1F2D3D;
            }
            
            .info-box .info-box .bg-light .progress-bar,
            .info-box .info-box .bg-gradient-light .progress-bar {
              background-color: #1F2D3D;
            }
            
            .info-box .info-box .bg-dark,
            .info-box .info-box .bg-gradient-dark {
              color: #ffffff;
            }
            
            .info-box .info-box .bg-dark .progress-bar,
            .info-box .info-box .bg-gradient-dark .progress-bar {
              background-color: #ffffff;
            }
            
            .info-box .info-box-more {
              display: block;
            }
            
            .info-box .progress-description {
              margin: 0;
            }
            .lead-history.card-outline {
              border-top: 3px solid #3498db;
            }
            
            .lead-history.card-outline-tabs .card-header a:hover {
              border-top: 3px solid #dee2e6;
            }
            
            .lead-history.card-outline-tabs .card-header a.active {
              border-top: 3px solid #3498db;
            }
            .shadow-pro {
              /*box-shadow: 0 19px 38px rgba(0, 0, 0, 0.3), 0 15px 12px rgba(0, 0, 0, 0.22) !important;*/
              box-shadow: 5px 1px 15px rgba(0, 0, 0, 0.06);
            }
        </style>
        <?php
    }
?>