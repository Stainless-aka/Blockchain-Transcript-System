<div class="min-vh-100 d-flex align-items-center justify-content-center p-3">
    <div class="card shadow-lg border-0 rounded-4" style="width:100%;max-width:440px;">
        <div class="card-body p-4 p-md-5">

            <!-- Logo / Brand -->
            <div class="text-center mb-4">
                <div class="mb-3">
                    <span class="bg-primary bg-opacity-10 p-3 rounded-circle d-inline-block">
                        <i class="bi bi-shield-lock-fill text-primary fs-2"></i>
                    </span>
                </div>
                <h4 class="fw-bold mb-1"><?= e($_ENV['APP_NAME'] ?? 'Transcript Verification') ?></h4>
                <p class="text-muted small">Administrator Login</p>
            </div>

            <!-- Flash Message -->
            <?php if (!empty($flash)): ?>
            <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show py-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                <?= e($flash['message']) ?>
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form action="<?= url('auth/login') ?>" method="POST" novalidate>
                <?= csrfField() ?>

                <div class="mb-3">
                    <label for="username" class="form-label fw-semibold">Username or Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-person text-muted"></i>
                        </span>
                        <input type="text"
                               class="form-control border-start-0 ps-0"
                               id="username"
                               name="username"
                               placeholder="Enter your username"
                               value="<?= e($_POST['username'] ?? '') ?>"
                               required
                               autocomplete="username">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-lock text-muted"></i>
                        </span>
                        <input type="password"
                               class="form-control border-start-0 ps-0"
                               id="password"
                               name="password"
                               placeholder="Enter your password"
                               required
                               autocomplete="current-password">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword" tabindex="-1">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Sign In
                </button>
            </form>

            <hr class="my-4">

            <div class="text-center">
                <a href="<?= url('verify') ?>" class="text-decoration-none small text-muted">
                    <i class="bi bi-patch-check me-1"></i> Public Transcript Verification
                </a>
            </div>

        </div>
    </div>
</div>

<script>
document.getElementById('togglePassword').addEventListener('click', function() {
    const pwd  = document.getElementById('password');
    const icon = document.getElementById('toggleIcon');
    if (pwd.type === 'password') {
        pwd.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        pwd.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
});
</script>
