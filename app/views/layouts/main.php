<!DOCTYPE html>
<html lang="en" data-bs-theme="light" id="htmlRoot">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Dashboard') ?> — <?= e($_ENV['APP_NAME'] ?? 'Transcript Verification') ?></title>

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body>

<!-- ── Sidebar ──────────────────────────────────────────────────────────── -->
<?php require BASE_PATH . '/app/views/layouts/sidebar.php'; ?>

<!-- ── Main Content ─────────────────────────────────────────────────────── -->
<div class="main-content" id="mainContent">

    <!-- ── Top Navbar ──────────────────────────────────────────────────── -->
    <?php require BASE_PATH . '/app/views/layouts/header.php'; ?>

    <!-- ── Page Body ───────────────────────────────────────────────────── -->
    <div class="container-fluid p-4">

        <!-- Flash Message -->
        <?php
        $flash = $flash ?? flash();
        if ($flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
            <i class="bi <?= $flash['type'] === 'success' ? 'bi-check-circle' : ($flash['type'] === 'danger' ? 'bi-exclamation-triangle' : 'bi-info-circle') ?> me-2"></i>
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <!-- View Content -->
        <?= $content ?>

    </div>

    <!-- ── Footer ──────────────────────────────────────────────────────── -->
    <?php require BASE_PATH . '/app/views/layouts/footer.php'; ?>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- App JS -->
<script src="<?= url('assets/js/app.js') ?>"></script>
</body>
</html>
