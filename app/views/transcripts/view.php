<div class="d-flex align-items-center mb-4">
    <a href="<?= url('transcripts') ?>" class="btn btn-sm btn-outline-secondary me-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h5 class="fw-bold mb-0"><i class="bi bi-file-earmark-text me-2 text-info"></i>Transcript Details</h5>
        <p class="text-muted small mb-0">Full transcript information and blockchain verification result.</p>
    </div>
</div>

<!-- Verification Status Banner -->
<?php if ($verificationResult['verified'] && $verificationResult['blockchain_valid']): ?>
<div class="alert alert-success d-flex align-items-center gap-3 rounded-4 border-0 shadow-sm mb-4">
    <i class="bi bi-shield-fill-check fs-2"></i>
    <div>
        <strong class="d-block">VERIFIED — Transcript is Authentic</strong>
        <span class="small"><?= e($verificationResult['message']) ?></span>
    </div>
</div>
<?php elseif (!$verificationResult['verified']): ?>
<div class="alert alert-danger d-flex align-items-center gap-3 rounded-4 border-0 shadow-sm mb-4">
    <i class="bi bi-shield-fill-exclamation fs-2"></i>
    <div>
        <strong class="d-block">TAMPERED — Transcript Integrity Compromised</strong>
        <span class="small"><?= e($verificationResult['message']) ?></span>
    </div>
</div>
<?php endif; ?>

<div class="row g-4">
    <!-- Transcript Details -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h6 class="fw-bold mb-0"><i class="bi bi-file-earmark-text me-2 text-info"></i>Transcript Information</h6>
            </div>
            <div class="card-body px-4 pb-4">
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted small">Transcript ID</dt>
                    <dd class="col-sm-7 font-monospace small fw-semibold"><?= e($transcript['transcript_id']) ?></dd>

                    <dt class="col-sm-5 text-muted small">Verification Code</dt>
                    <dd class="col-sm-7 font-monospace small">
                        <span class="badge bg-secondary text-uppercase"><?= e($transcript['verification_code']) ?></span>
                    </dd>

                    <dt class="col-sm-5 text-muted small">Degree</dt>
                    <dd class="col-sm-7 small"><?= e($transcript['degree']) ?></dd>

                    <dt class="col-sm-5 text-muted small">GPA / CGPA</dt>
                    <dd class="col-sm-7 small">
                        <?= e(number_format((float)$transcript['gpa'], 2)) ?> /
                        <strong><?= e(number_format((float)$transcript['cgpa'], 2)) ?></strong>
                    </dd>

                    <dt class="col-sm-5 text-muted small">Graduation Year</dt>
                    <dd class="col-sm-7"><?= e($transcript['graduation_year']) ?></dd>

                    <dt class="col-sm-5 text-muted small">Status</dt>
                    <dd class="col-sm-7">
                        <span class="badge bg-<?= statusBadge($transcript['status']) ?>">
                            <?= e(ucfirst($transcript['status'])) ?>
                        </span>
                    </dd>

                    <dt class="col-sm-5 text-muted small">Created</dt>
                    <dd class="col-sm-7 small"><?= formatDate($transcript['created_at']) ?></dd>

                    <?php if (!empty($transcript['pdf_path'])): ?>
                    <dt class="col-sm-5 text-muted small">PDF File</dt>
                    <dd class="col-sm-7">
                        <a href="<?= url($transcript['pdf_path']) ?>" target="_blank" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-file-pdf me-1"></i> View PDF
                        </a>
                    </dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>
    </div>

    <!-- Student Details -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h6 class="fw-bold mb-0"><i class="bi bi-person me-2 text-primary"></i>Student Information</h6>
            </div>
            <div class="card-body px-4 pb-4">
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted small">Full Name</dt>
                    <dd class="col-sm-7 fw-semibold"><?= e($transcript['full_name']) ?></dd>

                    <dt class="col-sm-5 text-muted small">Matric Number</dt>
                    <dd class="col-sm-7 font-monospace small"><?= e($transcript['matric_number']) ?></dd>

                    <dt class="col-sm-5 text-muted small">Student ID</dt>
                    <dd class="col-sm-7 font-monospace small"><?= e($transcript['student_code']) ?></dd>

                    <dt class="col-sm-5 text-muted small">Department</dt>
                    <dd class="col-sm-7 small"><?= e($transcript['department']) ?></dd>

                    <dt class="col-sm-5 text-muted small">Faculty</dt>
                    <dd class="col-sm-7 small"><?= e($transcript['faculty']) ?></dd>

                    <dt class="col-sm-5 text-muted small">Level</dt>
                    <dd class="col-sm-7"><span class="badge bg-secondary"><?= e($transcript['level']) ?></span></dd>

                    <dt class="col-sm-5 text-muted small">Email</dt>
                    <dd class="col-sm-7 small"><?= e($transcript['email']) ?></dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- Hash & Blockchain Info -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h6 class="fw-bold mb-0"><i class="bi bi-link-45deg me-2 text-warning"></i>Blockchain & Hash Information</h6>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small text-muted">SHA-256 Stored Hash</label>
                        <div class="input-group">
                            <input type="text" class="form-control font-monospace small bg-light"
                                   value="<?= e($transcript['hash']) ?>" readonly>
                            <button class="btn btn-outline-secondary btn-sm"
                                    onclick="navigator.clipboard.writeText('<?= e($transcript['hash']) ?>')" title="Copy">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    </div>

                    <?php if (!empty($verificationResult['computed_hash'])): ?>
                    <div class="col-12">
                        <label class="form-label small text-muted">Computed Hash (Live Verification)</label>
                        <input type="text" class="form-control font-monospace small bg-light"
                               value="<?= e($verificationResult['computed_hash']) ?>" readonly>
                        <?php if ($verificationResult['computed_hash'] === $transcript['hash']): ?>
                        <div class="form-text text-success"><i class="bi bi-check-circle me-1"></i>Hashes match — data is intact.</div>
                        <?php else: ?>
                        <div class="form-text text-danger"><i class="bi bi-x-circle me-1"></i>Hash mismatch — data has been tampered!</div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($verificationResult['block_index'])): ?>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 text-center">
                            <div class="text-muted small">Block Index</div>
                            <div class="fw-bold fs-5 text-warning">#<?= e($verificationResult['block_index']) ?></div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label small text-muted">Block Hash</label>
                        <input type="text" class="form-control font-monospace small bg-light"
                               value="<?= e($verificationResult['block_hash'] ?? '') ?>" readonly>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
