<div class="row justify-content-center">
    <div class="col-lg-8">

        <!-- Title -->
        <div class="text-center mb-5">
            <span class="bg-primary bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                <i class="bi bi-shield-lock-fill text-primary fs-2"></i>
            </span>
            <h3 class="fw-bold">Transcript Verification Portal</h3>
            <p class="text-muted">Enter a Transcript ID or Verification Code to verify the authenticity of an academic transcript.</p>
        </div>

        <!-- Search Form -->
        <div class="card border-0 shadow rounded-4 mb-4">
            <div class="card-body p-4">
                <form action="<?= url('verify') ?>" method="POST" novalidate>
                    <?= csrfField() ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Query Type</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="query_type"
                                       id="byTranscriptId" value="transcript_id"
                                       <?= (($_POST['query_type'] ?? 'transcript_id') === 'transcript_id') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="byTranscriptId">
                                    <i class="bi bi-file-earmark-text me-1"></i> Transcript ID
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="query_type"
                                       id="byVerificationCode" value="verification_code"
                                       <?= (($_POST['query_type'] ?? '') === 'verification_code') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="byVerificationCode">
                                    <i class="bi bi-key me-1"></i> Verification Code
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="query_value" class="form-label fw-semibold">Enter Value</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" class="form-control" id="query_value" name="query_value"
                                   placeholder="e.g. TRX-ABCD1234-2024 or A1B2C3D4E5F6G7H8"
                                   value="<?= e($queryValue ?? '') ?>"
                                   required>
                        </div>
                        <div class="form-text">Transcript IDs and verification codes are case-sensitive.</div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="bi bi-patch-check-fill me-2"></i> Verify Transcript
                    </button>
                </form>
            </div>
        </div>

        <!-- Results -->
        <?php if (!empty($result)): ?>

            <?php if (!$result['found']): ?>
            <!-- Not Found -->
            <div class="card border-0 shadow rounded-4 border-start border-danger border-4">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <i class="bi bi-exclamation-triangle-fill text-danger fs-2"></i>
                    <div>
                        <h5 class="fw-bold text-danger mb-1">Transcript Not Found</h5>
                        <p class="mb-0 text-muted">No transcript matched your query. Please check the ID or code and try again.</p>
                    </div>
                </div>
            </div>

            <?php elseif ($result['verified'] && ($result['blockchain_valid'] ?? false)): ?>
            <!-- VERIFIED -->
            <div class="card border-0 shadow rounded-4 mb-4">
                <div class="card-header bg-success text-white rounded-top-4 py-3 px-4 d-flex align-items-center gap-2">
                    <i class="bi bi-shield-fill-check fs-3"></i>
                    <div>
                        <h5 class="fw-bold mb-0">✓ VERIFIED — Transcript is Authentic</h5>
                        <small>Blockchain integrity confirmed. This transcript has not been tampered with.</small>
                    </div>
                </div>
                <div class="card-body p-4">
                    <?php $t = $result['transcript']; ?>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase small fw-bold mb-3">Student Information</h6>
                            <dl class="row mb-0">
                                <dt class="col-5 text-muted small">Full Name</dt>
                                <dd class="col-7 fw-semibold"><?= e($t['full_name']) ?></dd>

                                <dt class="col-5 text-muted small">Matric Number</dt>
                                <dd class="col-7 font-monospace small"><?= e($t['matric_number']) ?></dd>

                                <dt class="col-5 text-muted small">Department</dt>
                                <dd class="col-7 small"><?= e($t['department']) ?></dd>

                                <dt class="col-5 text-muted small">Faculty</dt>
                                <dd class="col-7 small"><?= e($t['faculty']) ?></dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase small fw-bold mb-3">Academic Information</h6>
                            <dl class="row mb-0">
                                <dt class="col-5 text-muted small">Degree</dt>
                                <dd class="col-7 small"><?= e($t['degree']) ?></dd>

                                <dt class="col-5 text-muted small">CGPA</dt>
                                <dd class="col-7 fw-bold text-success"><?= e(number_format((float)$t['cgpa'], 2)) ?></dd>

                                <dt class="col-5 text-muted small">GPA</dt>
                                <dd class="col-7"><?= e(number_format((float)$t['gpa'], 2)) ?></dd>

                                <dt class="col-5 text-muted small">Graduation Year</dt>
                                <dd class="col-7"><?= e($t['graduation_year']) ?></dd>
                            </dl>
                        </div>

                        <div class="col-12">
                            <h6 class="text-muted text-uppercase small fw-bold mb-3">Blockchain Information</h6>
                            <div class="bg-light rounded-3 p-3">
                                <div class="row g-2 align-items-center">
                                    <div class="col-auto">
                                        <span class="badge bg-warning text-dark">Block #<?= e($result['block_index'] ?? '?') ?></span>
                                    </div>
                                    <div class="col">
                                        <small class="text-muted d-block">SHA-256 Hash</small>
                                        <code class="small text-break"><?= e($t['hash']) ?></code>
                                    </div>
                                </div>
                                <div class="mt-2 small text-success">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Verified on <?= formatDate(date('Y-m-d H:i:s'), 'd M Y \a\t H:i') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php else: ?>
            <!-- TAMPERED -->
            <div class="card border-0 shadow rounded-4">
                <div class="card-header bg-danger text-white rounded-top-4 py-3 px-4 d-flex align-items-center gap-2">
                    <i class="bi bi-shield-fill-exclamation fs-3"></i>
                    <div>
                        <h5 class="fw-bold mb-0">✗ TAMPERED — Integrity Compromised</h5>
                        <small><?= e($result['message']) ?></small>
                    </div>
                </div>
                <div class="card-body p-4 text-center text-muted">
                    <i class="bi bi-exclamation-octagon fs-1 text-danger mb-3 d-block"></i>
                    <p>This transcript record shows signs of modification. Its hash does not match the blockchain record.</p>
                    <p class="small">If you believe this is an error, please contact the institution directly.</p>
                </div>
            </div>
            <?php endif; ?>

        <?php endif; ?>

        <!-- How It Works -->
        <?php if (empty($result)): ?>
        <div class="card border-0 bg-light rounded-4 mt-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2 text-primary"></i>How Verification Works</h6>
                <div class="row g-3">
                    <div class="col-md-4 text-center">
                        <i class="bi bi-input-cursor-text fs-2 text-primary mb-2 d-block"></i>
                        <div class="fw-semibold small">1. Enter ID</div>
                        <div class="text-muted small">Input the Transcript ID or Verification Code</div>
                    </div>
                    <div class="col-md-4 text-center">
                        <i class="bi bi-cpu fs-2 text-warning mb-2 d-block"></i>
                        <div class="fw-semibold small">2. Hash Computed</div>
                        <div class="text-muted small">SHA-256 hash is calculated from live data</div>
                    </div>
                    <div class="col-md-4 text-center">
                        <i class="bi bi-shield-check fs-2 text-success mb-2 d-block"></i>
                        <div class="fw-semibold small">3. Blockchain Checked</div>
                        <div class="text-muted small">Hash is compared against the immutable blockchain</div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>
