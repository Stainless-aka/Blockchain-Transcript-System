<?php

/**
 * AuthMiddleware
 * Protects routes by ensuring the user is authenticated
 */
class AuthMiddleware {

    /**
     * Handle the middleware check
     * If not authenticated, redirect to login page
     */
    public function handle(array $params = []): void {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            // Store intended URL for redirect after login
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'] ?? '';

            // Set flash message
            $_SESSION['flash'] = [
                'type'    => 'warning',
                'message' => 'Please log in to access that page.',
            ];

            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        // Optional: session timeout check
        $sessionLifetime = (int)($_ENV['SESSION_LIFETIME'] ?? 7200);
        $loginTime       = $_SESSION['login_time'] ?? 0;

        if ((time() - $loginTime) > $sessionLifetime) {
            // Session expired
            session_unset();
            session_destroy();

            $_SESSION['flash'] = [
                'type'    => 'warning',
                'message' => 'Your session has expired. Please log in again.',
            ];

            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }
}
