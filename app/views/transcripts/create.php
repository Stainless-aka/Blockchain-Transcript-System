<div class="row">
    <div class="col-lg-9 mx-auto">
        <!-- Header -->
        <div class="d-flex align-items-center mb-4">
            <a href="<?= url('transcripts') ?>" class="btn btn-sm btn-outline-secondary me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h5 class="fw-bold mb-0"><i class="bi bi-plus-circle me-2 text-primary"></i>Create Transcript</h5>
                <p class="text-muted small mb-0">New transcript will be automatically hashed and anchored to the blockchain.</p>
            </div>
        </div>

        <!-- Info Banner -->
        <div class="alert alert-info d-flex gap-2 rounded-4 border-0 shadow-sm mb-4">
            <i class="bi bi-info-circle-fill fs-5 mt-1"></i>
            <div class="small">
                Upon saving, a <strong>SHA-256 hash</strong> will be generated from the transcript data and permanently stored
                as a new <strong>block in the blockchain</strong>. Any future modification will invalidate the chain.
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <form action="<?= url('transcripts/store') ?>" method="POST" enctype="multipart/form-data" novalidate>
                    <?= csrfField() ?>

                    <div class="row g-3">
                        <!-- Student -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Student <span class="text-danger">*</span></label>
                            <select name="student_id" class="form-select <?= !empty($errors['student_id']) ? 'is-invalid' : '' ?>" required>
                                <option value="">— Select a Student —</option>
                                <?php foreach ($students as $s): ?>
                                <option value="<?= $s['id'] ?>"
                                    <?= ((int)($old['student_id'] ?? 0) === (int)$s['id']) ? 'selected' : '' ?>>
                                    <?= e($s['full_name']) ?> (<?= e($s['matric_number']) ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (!empty($errors['student_id'])): ?>
                                <div class="invalid-feedback"><?= e($errors['student_id'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Degree -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Degree / Programme <span class="text-danger">*</span></label>
                            <input type="text" name="degree" class="form-control <?= !empty($errors['degree']) ? 'is-invalid' : '' ?>"
                                   placeholder="e.g. Bachelor of Science in Computer Science"
                                   value="<?= e($old['degree'] ?? '') ?>" required>
                            <?php if (!empty($errors['degree'])): ?>
                                <div class="invalid-feedback"><?= e($errors['degree'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- GPA / CGPA -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">GPA <span class="text-danger">*</span></label>
                            <input type="number" name="gpa" step="0.01" min="0" max="5"
                                   class="form-control <?= !empty($errors['gpa']) ? 'is-invalid' : '' ?>"
                                   placeholder="0.00 – 5.00"
                                   value="<?= e($old['gpa'] ?? '') ?>" required>
                            <?php if (!empty($errors['gpa'])): ?>
                                <div class="invalid-feedback"><?= e($errors['gpa'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">CGPA <span class="text-danger">*</span></label>
                            <input type="number" name="cgpa" step="0.01" min="0" max="5"
                                   class="form-control <?= !empty($errors['cgpa']) ? 'is-invalid' : '' ?>"
                                   placeholder="0.00 – 5.00"
                                   value="<?= e($old['cgpa'] ?? '') ?>" required>
                            <?php if (!empty($errors['cgpa'])): ?>
                                <div class="invalid-feedback"><?= e($errors['cgpa'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Graduation Year -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Graduation Year <span class="text-danger">*</span></label>
                            <input type="number" name="graduation_year" min="1990" max="<?= date('Y') + 5 ?>"
                                   class="form-control <?= !empty($errors['graduation_year']) ? 'is-invalid' : '' ?>"
                                   placeholder="<?= date('Y') ?>"
                                   value="<?= e($old['graduation_year'] ?? date('Y')) ?>" required>
                            <?php if (!empty($errors['graduation_year'])): ?>
                                <div class="invalid-feedback"><?= e($errors['graduation_year'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- PDF Upload (optional) -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Transcript PDF <span class="text-muted small">(optional, max 5MB)</span></label>
                            <input type="file" name="pdf_file" class="form-control" accept=".pdf">
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-link-45deg me-1"></i> Create &amp; Anchor to Blockchain
                        </button>
                        <a href="<?= url('transcripts') ?>" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
