<nav id="sidebar" class="sidebar bg-dark">
    <!-- Brand -->
    <div class="sidebar-brand d-flex align-items-center px-3 py-3 border-bottom border-secondary">
        <i class="bi bi-shield-lock-fill text-primary fs-4 me-2"></i>
        <span class="fw-bold text-white text-truncate">BlockChain Transcript</span>
    </div>

    <!-- Navigation Links -->
    <ul class="nav flex-column px-2 pt-3">

        <li class="nav-item">
            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false ? 'active' : '' ?>"
               href="<?= url('dashboard') ?>">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>

        <li class="nav-item mt-1">
            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/students') !== false ? 'active' : '' ?>"
               href="<?= url('students') ?>">
                <i class="bi bi-people-fill me-2"></i> Students
            </a>
        </li>

        <li class="nav-item mt-1">
            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/transcripts') !== false ? 'active' : '' ?>"
               href="<?= url('transcripts') ?>">
                <i class="bi bi-file-earmark-text-fill me-2"></i> Transcripts
            </a>
        </li>

        <li class="nav-item mt-1">
            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/blockchain') !== false ? 'active' : '' ?>"
               href="<?= url('blockchain') ?>">
                <i class="bi bi-link-45deg me-2"></i> Blockchain
            </a>
        </li>

        <li class="nav-item mt-1">
            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/verify') !== false ? 'active' : '' ?>"
               href="<?= url('verify') ?>" target="_blank">
                <i class="bi bi-patch-check-fill me-2"></i> Verify Transcript
            </a>
        </li>

        <li class="sidebar-section-title text-uppercase text-secondary small px-2 mt-3 mb-1">Account</li>

        <li class="nav-item mt-1">
            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/profile') !== false ? 'active' : '' ?>"
               href="<?= url('profile') ?>">
                <i class="bi bi-person-circle me-2"></i> Profile
            </a>
        </li>

        <li class="nav-item mt-1">
            <a class="nav-link text-danger" href="<?= url('auth/logout') ?>">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </a>
        </li>
    </ul>

    <!-- Dark Mode Toggle -->
    <div class="px-3 mt-auto pb-3 position-absolute bottom-0 w-100">
        <hr class="border-secondary">
        <div class="d-flex align-items-center justify-content-between">
            <span class="text-secondary small"><i class="bi bi-moon-stars me-1"></i> Dark Mode</span>
            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="darkModeToggle" role="switch">
            </div>
        </div>
    </div>
</nav>
