<div class="row">
    <div class="col-lg-8 mx-auto">
        <!-- Header -->
        <div class="d-flex align-items-center mb-4">
            <a href="<?= url('students') ?>" class="btn btn-sm btn-outline-secondary me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h5 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2 text-info"></i>Edit Student</h5>
                <p class="text-muted small mb-0">Update <?= e($student['full_name']) ?>'s information.</p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <form action="<?= url('students/update/' . $student['id']) ?>" method="POST" novalidate>
                    <?= csrfField() ?>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Student ID <span class="text-danger">*</span></label>
                            <input type="text" name="student_id" class="form-control <?= !empty($errors['student_id']) ? 'is-invalid' : '' ?>"
                                   value="<?= e($student['student_id']) ?>" required>
                            <?php if (!empty($errors['student_id'])): ?>
                                <div class="invalid-feedback"><?= e($errors['student_id'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Matric Number <span class="text-danger">*</span></label>
                            <input type="text" name="matric_number" class="form-control <?= !empty($errors['matric_number']) ? 'is-invalid' : '' ?>"
                                   value="<?= e($student['matric_number']) ?>" required>
                            <?php if (!empty($errors['matric_number'])): ?>
                                <div class="invalid-feedback"><?= e($errors['matric_number'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control <?= !empty($errors['full_name']) ? 'is-invalid' : '' ?>"
                                   value="<?= e($student['full_name']) ?>" required>
                            <?php if (!empty($errors['full_name'])): ?>
                                <div class="invalid-feedback"><?= e($errors['full_name'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Department <span class="text-danger">*</span></label>
                            <input type="text" name="department" class="form-control <?= !empty($errors['department']) ? 'is-invalid' : '' ?>"
                                   value="<?= e($student['department']) ?>" required>
                            <?php if (!empty($errors['department'])): ?>
                                <div class="invalid-feedback"><?= e($errors['department'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Faculty <span class="text-danger">*</span></label>
                            <input type="text" name="faculty" class="form-control <?= !empty($errors['faculty']) ? 'is-invalid' : '' ?>"
                                   value="<?= e($student['faculty']) ?>" required>
                            <?php if (!empty($errors['faculty'])): ?>
                                <div class="invalid-feedback"><?= e($errors['faculty'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Level <span class="text-danger">*</span></label>
                            <select name="level" class="form-select <?= !empty($errors['level']) ? 'is-invalid' : '' ?>" required>
                                <option value="">Select Level</option>
                                <option value="100" <?= ($student['level']) === '100' ? 'selected' : '' ?>>100</option>
                                <option value="200" <?= ($student['level']) === '200' ? 'selected' : '' ?>>200</option>
                                <option value="300" <?= ($student['level']) === '300' ? 'selected' : '' ?>>300</option>
                                <option value="400" <?= ($student['level']) === '400' ? 'selected' : '' ?>>400</option>
                                <option value="500" <?= ($student['level']) === '500' ? 'selected' : '' ?>>500</option>
                            </select>
                            <?php if (!empty($errors['level'])): ?>
                                <div class="invalid-feedback"><?= e($errors['level'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control <?= !empty($errors['email']) ? 'is-invalid' : '' ?>"
                                   value="<?= e($student['email']) ?>" required>
                            <?php if (!empty($errors['email'])): ?>
                                <div class="invalid-feedback"><?= e($errors['email'][0]) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-info text-white">
                            <i class="bi bi-save me-1"></i> Update Student
                        </button>
                        <a href="<?= url('students') ?>" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
