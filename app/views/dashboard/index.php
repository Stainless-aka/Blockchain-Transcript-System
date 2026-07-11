<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                    <i class="bi bi-people-fill fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Students</div>
                    <div class="fw-bold fs-4"><?= number_format($stats['total_students']) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="icon-box bg-info bg-opacity-10 text-info rounded-3 p-3">
                    <i class="bi bi-file-earmark-text-fill fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Transcripts</div>
                    <div class="fw-bold fs-4"><?= number_format($stats['total_transcripts']) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="icon-box bg-success bg-opacity-10 text-success rounded-3 p-3">
                    <i class="bi bi-patch-check-fill fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Verified Transcripts</div>
                    <div class="fw-bold fs-4"><?= number_format($stats['verified_transcripts']) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="icon-box bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                    <i class="bi bi-link-45deg fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Blockchain Blocks</div>
                    <div class="fw-bold fs-4"><?= number_format($stats['total_blocks']) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Blockchain Status Alert -->
<div class="row mb-4">
    <div class="col-12">
        <?php if ($chainStatus['valid']): ?>
        <div class="alert alert-success d-flex align-items-center gap-2 mb-0 rounded-4 border-0 shadow-sm" role="alert">
            <i class="bi bi-shield-check fs-4"></i>
            <div>
                <strong>Blockchain Integrity: VALID</strong> — All <?= $chainStatus['total_blocks'] ?? 0 ?> blocks are intact and unmodified.
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-danger d-flex align-items-center gap-2 mb-0 rounded-4 border-0 shadow-sm" role="alert">
            <i class="bi bi-shield-exclamation fs-4"></i>
            <div>
                <strong>Blockchain Integrity: COMPROMISED</strong> — <?= count($chainStatus['tampered_blocks'] ?? []) ?> tampered block(s) detected.
                <a href="<?= url('blockchain') ?>" class="alert-link ms-2">View Details</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Transcripts -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-file-earmark-text me-2 text-info"></i>Recent Transcripts</h6>
                <a href="<?= url('transcripts') ?>" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body px-4 pb-4">
                <?php if (empty($recentTranscripts)): ?>
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    No transcripts yet.
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle small mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Transcript ID</th>
                                <th>Student</th>
                                <th>Degree</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($recentTranscripts as $t): ?>
                            <tr>
                                <td class="font-monospace small"><?= e($t['transcript_id']) ?></td>
                                <td><?= e($t['full_name']) ?></td>
                                <td><?= e(truncate($t['degree'], 30)) ?></td>
                                <td>
                                    <span class="badge bg-<?= statusBadge($t['status']) ?>">
                                        <?= e(ucfirst($t['status'])) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-warning"></i>Recent Activity</h6>
            </div>
            <div class="card-body px-3 pb-4">
                <?php if (empty($recentActivities)): ?>
                <div class="text-center text-muted py-4">
                    <i class="bi bi-clock fs-1 d-block mb-2"></i>
                    No activity yet.
                </div>
                <?php else: ?>
                <ul class="list-unstyled mb-0">
                    <?php foreach ($recentActivities as $activity): ?>
                    <li class="d-flex align-items-start gap-2 py-2 border-bottom border-light">
                        <span class="badge rounded-circle bg-<?= actionColor($activity['action']) ?> p-2 mt-1">
                            <i class="bi <?= actionIcon($activity['action']) ?>"></i>
                        </span>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="small fw-semibold text-truncate"><?= e($activity['description']) ?></div>
                            <div class="text-muted" style="font-size:0.72rem;">
                                <?= e($activity['full_name'] ?? $activity['username'] ?? 'System') ?>
                                &bull; <?= formatDate($activity['created_at']) ?>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
