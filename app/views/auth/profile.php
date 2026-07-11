<div class="row g-4">
    <div class="col-12">
        <h5 class="fw-bold mb-0"><i class="bi bi-person-circle me-2 text-primary"></i>My Profile</h5>
        <p class="text-muted small">Manage your account details and password.</p>
    </div>

    <!-- Profile Info Card -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h6 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2 text-primary"></i>Update Profile</h6>
            </div>
            <div class="card-body px-4 pb-4">
                <form action="<?= url('profile/update') ?>" method="POST" novalidate>
                    <?= csrfField() ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name</label>
                        <input type="text" name="full_name" class="form-control"
                               value="<?= e($user['full_name'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" class="form-control"
                               value="<?= e($user['email'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Username</label>
                        <input type="text" class="form-control" value="<?= e($user['username'] ?? '') ?>" disabled>
                        <div class="form-text">Username cannot be changed.</div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Password Card -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h6 class="fw-bold mb-0"><i class="bi bi-lock me-2 text-warning"></i>Change Password</h6>
            </div>
            <div class="card-body px-4 pb-4">
                <form action="<?= url('profile/password') ?>" method="POST" novalidate>
                    <?= csrfField() ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required autocomplete="current-password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">New Password</label>
                        <input type="password" name="new_password" class="form-control" required autocomplete="new-password" minlength="8">
                        <div class="form-text">Minimum 8 characters.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" required autocomplete="new-password">
                    </div>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-key me-1"></i> Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Account Info -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body px-4 py-3 d-flex align-items-center gap-3">
                <span class="bg-primary bg-opacity-10 p-2 rounded-circle">
                    <i class="bi bi-info-circle text-primary fs-5"></i>
                </span>
                <div>
                    <div class="fw-semibold small">Account Role: <span class="badge bg-primary"><?= e(ucfirst($user['role'] ?? 'admin')) ?></span></div>
                    <div class="text-muted small">Member since: <?= formatDate($user['created_at'] ?? '', 'd M Y') ?> &nbsp;|&nbsp;
                        Last login: <?= formatDate($user['last_login'] ?? '', 'd M Y, H:i') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
