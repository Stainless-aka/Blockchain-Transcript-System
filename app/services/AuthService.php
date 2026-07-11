<?php

/**
 * AuthService
 * Handles authentication logic: login, logout, session management
 */
class AuthService {

    private User $userModel;
    private ActivityLog $activityLog;

    public function __construct() {
        $this->userModel   = new User();
        $this->activityLog = new ActivityLog();
    }

    /**
     * Attempt to log in a user
     */
    public function login(string $username, string $password): array {
        // Find user by username or email
        $user = $this->userModel->findByUsername($username);
        if (!$user) {
            $user = $this->userModel->findByEmail($username);
        }

        if (!$user) {
            return ['success' => false, 'message' => 'Invalid username or password.'];
        }

        if (!$this->userModel->verifyPassword($password, $user['password'])) {
            // Log failed attempt
            $this->activityLog->log([
                'user_id'     => $user['id'],
                'action'      => 'LOGIN_FAILED',
                'description' => "Failed login attempt for user: {$user['username']}",
            ]);
            return ['success' => false, 'message' => 'Invalid username or password.'];
        }

        // Regenerate session ID to prevent fixation
        session_regenerate_id(true);

        // Store user info in session
        $_SESSION['user_id']       = $user['id'];
        $_SESSION['username']      = $user['username'];
        $_SESSION['full_name']     = $user['full_name'];
        $_SESSION['role']          = $user['role'];
        $_SESSION['logged_in']     = true;
        $_SESSION['login_time']    = time();

        // Update last login timestamp
        $this->userModel->updateLastLogin($user['id']);

        // Log successful login
        $this->activityLog->log([
            'user_id'     => $user['id'],
            'action'      => 'LOGIN',
            'description' => "User {$user['username']} logged in successfully.",
        ]);

        return ['success' => true, 'user' => $user];
    }

    /**
     * Log out the current user
     */
    public function logout(): void {
        if (isset($_SESSION['user_id'])) {
            $this->activityLog->log([
                'user_id'     => $_SESSION['user_id'],
                'action'      => 'LOGOUT',
                'description' => "User {$_SESSION['username']} logged out.",
            ]);
        }

        // Destroy all session data
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    /**
     * Check whether a user is authenticated
     */
    public function isAuthenticated(): bool {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true
            && isset($_SESSION['user_id']);
    }

    /**
     * Get the current authenticated user's session data
     */
    public function currentUser(): ?array {
        if (!$this->isAuthenticated()) {
            return null;
        }

        return [
            'id'        => $_SESSION['user_id'],
            'username'  => $_SESSION['username'],
            'full_name' => $_SESSION['full_name'],
            'role'      => $_SESSION['role'],
        ];
    }
}
