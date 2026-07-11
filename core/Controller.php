<?php

/**
 * Base Controller — all controllers extend this.
 */
abstract class Controller
{
    // ──────────────────────────────────────────────────────────────────────
    // View Rendering
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Render a view inside a layout.
     *
     * @param string $view   Slash- or dot-separated path, e.g. 'students/index'
     * @param array  $data   Variables extracted into the view
     * @param string $layout Layout name: 'main' (default), 'auth', or 'public'
     */
    protected function view(string $view, array $data = [], string $layout = 'main'): void
    {
        extract($data, EXTR_SKIP);

        $file = BASE_PATH . '/app/views/' . str_replace(['.', '\\'], '/', $view) . '.php';
        if (!file_exists($file)) {
            die("View not found: {$file}");
        }

        // Capture view content
        ob_start();
        require $file;
        $content = ob_get_clean();

        // Wrap in layout
        $layoutFile = BASE_PATH . '/app/views/layouts/' . $layout . '.php';
        if (!file_exists($layoutFile)) {
            die("Layout not found: {$layoutFile}");
        }
        require $layoutFile;
    }

    /** Render a view with NO layout */
    protected function bare(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $file = BASE_PATH . '/app/views/' . str_replace(['.', '\\'], '/', $view) . '.php';
        if (!file_exists($file)) die("View not found: {$file}");
        require $file;
    }

    // ──────────────────────────────────────────────────────────────────────
    // HTTP Helpers
    // ──────────────────────────────────────────────────────────────────────

    /** HTTP redirect */
    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    /** JSON response */
    protected function json(array $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // ──────────────────────────────────────────────────────────────────────
    // Flash Messages
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Store a flash message in the session.
     * Stored as ['type' => ..., 'message' => ...] to match the layout template.
     */
    protected function setFlash(string $type, string $msg): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $msg];
    }

    /**
     * Retrieve and clear the flash message from the session.
     * Returns null if no flash message is set.
     */
    protected function getFlash(): ?array
    {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    /**
     * Alias for setFlash() — kept for backward compatibility.
     */
    protected function flash(string $type, string $msg): void
    {
        $this->setFlash($type, $msg);
    }

    // ──────────────────────────────────────────────────────────────────────
    // CSRF Protection
    // ──────────────────────────────────────────────────────────────────────

    /** Generate (or reuse) a CSRF token and return it */
    protected function generateCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Validate CSRF token from POST.
     * Returns true on success, false on failure.
     */
    protected function validateCsrf(): bool
    {
        $tokenName = $_ENV['CSRF_TOKEN_NAME'] ?? 'csrf_token';
        $token     = $_POST[$tokenName] ?? '';

        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            return false;
        }

        // One-time token — regenerate after use
        unset($_SESSION['csrf_token']);
        return true;
    }

    /**
     * Alias for generateCsrfToken() — kept for backward compatibility.
     */
    protected function csrf(): string
    {
        return $this->generateCsrfToken();
    }

    /**
     * Alias for validateCsrf() that dies on failure — kept for backward compatibility.
     */
    protected function verifyCsrf(): void
    {
        if (!$this->validateCsrf()) {
            $this->setFlash('danger', 'Invalid security token. Please try again.');
            $this->redirect($_SERVER['HTTP_REFERER'] ?? BASE_URL);
        }
    }

    // ──────────────────────────────────────────────────────────────────────
    // Input
    // ──────────────────────────────────────────────────────────────────────

    /** Sanitize a single string */
    protected function clean(string $v): string
    {
        return htmlspecialchars(strip_tags(trim($v)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Read + sanitize a value from $_POST or $_GET (POST takes priority).
     * Alias: input()
     */
    protected function input(string $key, string $default = ''): string
    {
        if (isset($_POST[$key])) {
            return $this->clean($_POST[$key]);
        }
        if (isset($_GET[$key])) {
            return $this->clean($_GET[$key]);
        }
        return $default;
    }

    /** Read + sanitize from $_POST */
    protected function post(string $key, string $default = ''): string
    {
        return isset($_POST[$key]) ? $this->clean($_POST[$key]) : $default;
    }

    /** Read + sanitize from $_GET */
    protected function get(string $key, string $default = ''): string
    {
        return isset($_GET[$key]) ? $this->clean($_GET[$key]) : $default;
    }

    /**
     * Read + sanitize from $_GET.
     * Alias: query() — used for pagination/search query params.
     */
    protected function query(string $key, string $default = ''): string
    {
        return $this->get($key, $default);
    }

    // ──────────────────────────────────────────────────────────────────────
    // Authentication
    // ──────────────────────────────────────────────────────────────────────

    /** Require authenticated session or redirect to login */
    protected function requireAuth(): void
    {
        if (empty($_SESSION['user_id'])) {
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'] ?? '';
            $this->setFlash('warning', 'Please log in to continue.');
            $this->redirect(BASE_URL . '/auth/login');
        }
    }

    /**
     * Alias for requireAuth() — kept for backward compatibility.
     */
    protected function auth(): void
    {
        $this->requireAuth();
    }

    /** Return the current authenticated user's ID */
    protected function currentUserId(): int
    {
        return (int) ($_SESSION['user_id'] ?? 0);
    }

    /**
     * Alias for currentUserId() — kept for backward compatibility.
     */
    protected function userId(): int
    {
        return $this->currentUserId();
    }

    // ──────────────────────────────────────────────────────────────────────
    // Validation
    // ──────────────────────────────────────────────────────────────────────

    /** Simple server-side validation */
    protected function validate(array $data, array $rules): array
    {
        $errors = [];
        foreach ($rules as $field => $rule) {
            $value = trim($data[$field] ?? '');
            $label = ucfirst(str_replace('_', ' ', $field));
            foreach (explode('|', $rule) as $r) {
                if ($r === 'required' && $value === '') {
                    $errors[$field][] = "{$label} is required.";
                } elseif (str_starts_with($r, 'min:') && strlen($value) < (int) substr($r, 4)) {
                    $errors[$field][] = "{$label} must be at least " . substr($r, 4) . " characters.";
                } elseif (str_starts_with($r, 'max:') && strlen($value) > (int) substr($r, 4)) {
                    $errors[$field][] = "{$label} may not exceed " . substr($r, 4) . " characters.";
                } elseif ($r === 'email' && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = "{$label} must be a valid email.";
                } elseif ($r === 'numeric' && $value !== '' && !is_numeric($value)) {
                    $errors[$field][] = "{$label} must be numeric.";
                }
            }
        }
        return $errors;
    }
}
