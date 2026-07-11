<?php

/**
 * Global Helper Functions
 */

/**
 * Generate a full URL from a path
 */
function url(string $path = ''): string {
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

/**
 * Redirect to a URL
 */
function redirect(string $path): void {
    header('Location: ' . url($path));
    exit;
}

/**
 * Escape output to prevent XSS
 */
function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Get and clear a flash message from the session
 */
function flash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        // Normalise legacy 'msg' key to 'message' for template compatibility
        if (isset($flash['msg']) && !isset($flash['message'])) {
            $flash['message'] = $flash['msg'];
            unset($flash['msg']);
        }
        return $flash;
    }
    return null;
}

/**
 * Generate a CSRF token input field
 */
function csrfField(): string {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    $token = $_SESSION['csrf_token'];
    $name  = $_ENV['CSRF_TOKEN_NAME'] ?? 'csrf_token';
    return '<input type="hidden" name="' . $name . '" value="' . $token . '">';
}

/**
 * Get the CSRF token value
 */
function csrfToken(): string {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Check if a user is currently logged in
 */
function isLoggedIn(): bool {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * Get the currently logged-in user's data from session
 */
function currentUser(): ?array {
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'id'        => $_SESSION['user_id']   ?? null,
        'username'  => $_SESSION['username']  ?? '',
        'full_name' => $_SESSION['full_name'] ?? '',
        'role'      => $_SESSION['role']      ?? '',
    ];
}

/**
 * Format a datetime string for display
 */
function formatDate(string $dateString, string $format = 'd M Y, H:i'): string {
    if (empty($dateString)) return 'N/A';
    try {
        $dt = new DateTime($dateString);
        return $dt->format($format);
    } catch (Exception $e) {
        return $dateString;
    }
}

/**
 * Truncate a string to a given length
 */
function truncate(string $string, int $length = 50, string $suffix = '...'): string {
    if (strlen($string) <= $length) {
        return $string;
    }
    return substr($string, 0, $length) . $suffix;
}

/**
 * Return a Bootstrap badge class for a transcript status
 */
function statusBadge(string $status): string {
    return match (strtolower($status)) {
        'verified'  => 'success',
        'pending'   => 'warning',
        'rejected'  => 'danger',
        'tampered'  => 'danger',
        default     => 'secondary',
    };
}

/**
 * Return Bootstrap icon class for an activity action
 */
function actionIcon(string $action): string {
    return match (true) {
        str_contains($action, 'LOGIN')      => 'bi-box-arrow-in-right',
        str_contains($action, 'LOGOUT')     => 'bi-box-arrow-right',
        str_contains($action, 'CREATE')     => 'bi-plus-circle',
        str_contains($action, 'UPDATE')     => 'bi-pencil',
        str_contains($action, 'DELETE')     => 'bi-trash',
        str_contains($action, 'VERIFY')     => 'bi-shield-check',
        str_contains($action, 'BLOCKCHAIN') => 'bi-link-45deg',
        default                             => 'bi-activity',
    };
}

/**
 * Return Bootstrap color class for an activity action
 */
function actionColor(string $action): string {
    return match (true) {
        str_contains($action, 'LOGIN')      => 'primary',
        str_contains($action, 'LOGOUT')     => 'secondary',
        str_contains($action, 'CREATE')     => 'success',
        str_contains($action, 'UPDATE')     => 'info',
        str_contains($action, 'DELETE')     => 'danger',
        str_contains($action, 'VERIFY')     => 'success',
        str_contains($action, 'BLOCKCHAIN') => 'warning',
        default                             => 'dark',
    };
}

/**
 * Generate pagination HTML
 */
function paginationLinks(array $pagination, string $baseUrl): string {
    if ($pagination['last_page'] <= 1) {
        return '';
    }

    $html    = '<nav aria-label="Page navigation"><ul class="pagination pagination-sm mb-0">';
    $current = $pagination['current_page'];
    $last    = $pagination['last_page'];

    // Previous button
    $prevDisabled = ($current <= 1) ? 'disabled' : '';
    $prevPage     = max(1, $current - 1);
    $html .= "<li class=\"page-item {$prevDisabled}\">
                <a class=\"page-link\" href=\"{$baseUrl}?page={$prevPage}\" aria-label=\"Previous\">
                    <span aria-hidden=\"true\">&laquo;</span>
                </a>
              </li>";

    // Page numbers
    $start = max(1, $current - 2);
    $end   = min($last, $current + 2);

    if ($start > 1) {
        $html .= "<li class=\"page-item\"><a class=\"page-link\" href=\"{$baseUrl}?page=1\">1</a></li>";
        if ($start > 2) {
            $html .= '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
        }
    }

    for ($i = $start; $i <= $end; $i++) {
        $active = ($i === $current) ? 'active' : '';
        $html  .= "<li class=\"page-item {$active}\">
                     <a class=\"page-link\" href=\"{$baseUrl}?page={$i}\">{$i}</a>
                   </li>";
    }

    if ($end < $last) {
        if ($end < $last - 1) {
            $html .= '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
        }
        $html .= "<li class=\"page-item\"><a class=\"page-link\" href=\"{$baseUrl}?page={$last}\">{$last}</a></li>";
    }

    // Next button
    $nextDisabled = ($current >= $last) ? 'disabled' : '';
    $nextPage     = min($last, $current + 1);
    $html .= "<li class=\"page-item {$nextDisabled}\">
                <a class=\"page-link\" href=\"{$baseUrl}?page={$nextPage}\" aria-label=\"Next\">
                    <span aria-hidden=\"true\">&raquo;</span>
                </a>
              </li>";

    $html .= '</ul></nav>';
    return $html;
}

/**
 * Log an activity (convenience wrapper)
 */
function logActivity(string $action, string $description): void {
    try {
        $log = new ActivityLog();
        $log->log([
            'user_id'     => $_SESSION['user_id'] ?? null,
            'action'      => $action,
            'description' => $description,
        ]);
    } catch (Exception $e) {
        // Silently fail to avoid disrupting the user flow
        error_log('Activity log failed: ' . $e->getMessage());
    }
}

/**
 * Abort with a given HTTP status code
 */
function abort(int $code, string $message = ''): void {
    http_response_code($code);
    echo "<h1>{$code} Error</h1><p>" . e($message) . "</p>";
    exit;
}
