<!DOCTYPE html>
<html lang="en" data-bs-theme="light" id="htmlRoot">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Verify Transcript') ?> — <?= e($_ENV['APP_NAME'] ?? 'Transcript Verification') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body class="auth-bg">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= url('verify') ?>">
            <i class="bi bi-shield-lock-fill me-2"></i>
            <?= e($_ENV['APP_NAME'] ?? 'Transcript Verification') ?>
        </a>
        <div class="ms-auto">
            <?php if (isLoggedIn()): ?>
            <a href="<?= url('dashboard') ?>" class="btn btn-outline-light btn-sm">
                <i class="bi bi-speedometer2 me-1"></i> Dashboard
            </a>
            <?php else: ?>
            <a href="<?= url('auth/login') ?>" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-in-right me-1"></i> Admin Login
            </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container py-5">
    <?php
    $flash = $flash ?? flash();
    if ($flash): ?>
    <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
        <?= e($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?= $content ?>
</div>

<footer class="bg-dark text-white text-center py-3 mt-auto">
    <small>&copy; <?= date('Y') ?> <?= e($_ENV['APP_NAME'] ?? 'Transcript Verification System') ?>. All rights reserved.</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= url('assets/js/app.js') ?>"></script>
</body>
</html>
