<header class="top-navbar navbar navbar-expand-lg bg-white shadow-sm px-4 py-2 border-bottom">
    <!-- Sidebar Toggle -->
    <button class="btn btn-sm btn-outline-secondary me-3" id="sidebarToggle" aria-label="Toggle sidebar">
        <i class="bi bi-list fs-5"></i>
    </button>

    <!-- Page Title -->
    <span class="fw-semibold text-dark"><?= e($title ?? 'Dashboard') ?></span>

    <!-- Right side controls -->
    <div class="ms-auto d-flex align-items-center gap-3">

        <!-- Blockchain Status Badge -->
        <span class="badge bg-success d-none d-md-inline-flex align-items-center gap-1">
            <i class="bi bi-link-45deg"></i> Chain Active
        </span>

        <!-- User Dropdown -->
        <?php $user = currentUser(); ?>
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center gap-2"
                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle fs-5"></i>
                <span class="d-none d-md-inline"><?= e($user['full_name'] ?? 'Admin') ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li>
                    <span class="dropdown-item-text small text-muted">
                        Logged in as <strong><?= e($user['username'] ?? '') ?></strong>
                    </span>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="<?= url('profile') ?>">
                        <i class="bi bi-person me-2"></i>Profile
                    </a>
                </li>
                <li>
                    <a class="dropdown-item text-danger" href="<?= url('auth/logout') ?>">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>
