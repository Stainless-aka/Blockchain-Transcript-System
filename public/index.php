<?php

/**
 * Application Entry Point
 * All HTTP requests are routed through this file via Apache mod_rewrite
 */

declare(strict_types=1);

// ── Bootstrap ──────────────────────────────────────────────────────────────────
require_once __DIR__ . '/../core/App.php';
require_once __DIR__ . '/../core/Router.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../core/Controller.php';

// ── Launch ─────────────────────────────────────────────────────────────────────
$app = new App();
$app->run();
