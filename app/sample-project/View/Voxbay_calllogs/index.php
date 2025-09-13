<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0"><?= $page_title ?? '' ?></h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('app/dashboard/index') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= $page_title ?? '' ?></li>
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
                        <h5 class="card-title mb-0"><?= $page_title ?? '' ?></h5>
                    </div>
                    <div class="col-4 text-end">
                        <!-- No create button needed here -->
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
                                <th>Call UUID</th>
                                <th>Caller</th>
                                <th>Called</th>
                                <th>Date</th>
                                <th>Start</th>
                                <th>End</th>
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
                                    <td><?= $log['call_uuid'] ?></td>
                                    <td><?= $log['type'] === 'incoming' ? $log['callerNumber'] : ($log['callerid'] ?? '-') ?></td>
                                    <td><?= $log['type'] === 'incoming' ? $log['calledNumber'] : ($log['destinationNumber'] ?? '-') ?></td>
                                    <td><?= $log['date'] ?></td>
                                    <td><?= $log['start_time'] ?></td>
                                    <td><?= $log['end_time'] ?? '' ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
