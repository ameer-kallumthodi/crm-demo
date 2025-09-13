<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0"><?= $page_title ?? 'Call Logs List' ?></h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('app/dashboard/index') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('app/voxbay_calllogs') ?>">Call Logs</a></li>
                    <li class="breadcrumb-item active">Filtered Logs</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h5 class="card-title mb-0"><?= $page_title ?? 'Filtered Call Logs' ?></h5>
                    </div>
                    <div class="col-4 text-end">
                        <a href="<?= base_url('app/leads/index') ?>" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="data_table_basic table table-bordered nowrap table-striped align-middle" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Telecaller</th>                 
                                <th>Caller</th>
                                <th>Called</th>
                                <th>Date</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Call Recording</th>
                                <th>Copy Link</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($call_logs as $log): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td>
                                        <?php if ($log['type'] === 'incoming'): ?>
                                            <span class="badge bg-success">Incoming</span>
                                        <?php elseif ($log['type'] === 'outgoing'): ?>
                                            <span class="badge bg-primary">Outgoing</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?= ucfirst($log['type']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $log['telecaller_name'] ?></td>
                                    

                                    <td><?= $log['type'] === 'incoming' ? $log['callerNumber'] : ($log['callerid'] ?? '-') ?></td>
                                    <td><?= $log['type'] === 'incoming' ? $log['calledNumber'] : ($log['destinationNumber'] ?? '-') ?></td>
                                    <td><?= $log['date'] ? date('d-m-Y',strtotime($log['date'])) : '' ?></td>
                                    <td><?= $log['start_time'] ? date('h:i A',strtotime($log['start_time'])) : '' ?></td>
                                    <td><?= $log['end_time'] ? date('h:i A',strtotime($log['end_time'])) :'' ?></td>
                                    <td style="width: 300px;">
                                    <?php if (!empty($log['recording_URL'])): ?>
                                        <audio controls style="width: 280px;">
                                            <source src="<?= $log['recording_URL'] ?>" type="audio/wav">
                                            Your browser does not support the audio element.
                                        </audio>
                                    <?php else: ?>
                                        <span class="text-muted">No recording</span>
                                    <?php endif; ?>
                                    </td>
                                    <td>
                                    <?php if (!empty($log['recording_URL'])): ?>
                                        <button class="btn btn-sm btn-outline-primary" onclick="copyToClipboard('<?= $log['recording_URL'] ?>')" title="Copy recording link">
                                            <i class="ri-file-copy-line"></i>
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                    </td>

                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const toast = document.createElement('div');
        toast.className = 'alert alert-success position-fixed';
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; opacity: 0.9; min-width: 250px;';
        toast.innerHTML = '<i class="fas fa-check-circle me-2"></i>Recording link copied to clipboard!';
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }).catch(function(err) {
        console.error('Failed to copy: ', err);
        alert('Failed to copy link to clipboard');
    });
}
</script>
