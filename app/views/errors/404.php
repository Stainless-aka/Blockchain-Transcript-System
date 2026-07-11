<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 bg-light">
    <div class="text-center">
        <div class="mb-4">
            <i class="bi bi-exclamation-triangle text-warning" style="font-size: 6rem;"></i>
        </div>
        <h1 class="display-1 fw-bold text-dark">404</h1>
        <h3 class="mb-3">Page Not Found</h3>
        <p class="text-muted mb-4">
            Sorry, the page you're looking for doesn't exist.
        </p>
        <div class="d-flex gap-2 justify-content-center">
            <a href="<?= rtrim($_ENV['APP_URL'] ?? '/', '/') ?>/dashboard" class="btn btn-primary">
                <i class="bi bi-house me-1"></i> Go to Dashboard
            </a>
            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Go Back
            </a>
        </div>
    </div>
</body>
</html>
