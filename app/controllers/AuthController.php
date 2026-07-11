<?php

/**
 * AuthController
 * Handles login, logout, and profile management
 */
class AuthController extends Controller {

    private AuthService $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    /**
     * GET /auth/login - Show login form
     */
    public function login(array $params = []): void {
        // Redirect if already logged in
        if ($this->authService->isAuthenticated()) {
            $this->redirect(BASE_URL . '/dashboard');
        }

        $flash = $this->getFlash();
        $this->view('auth/login', [
            'title'     => 'Login',
            'flash'     => $flash,
            'csrfToken' => $this->generateCsrfToken(),
        ], 'auth');
    }

    /**
     * POST /auth/login - Process login
     */
    public function processLogin(array $params = []): void {
        if (!$this->validateCsrf()) {
            $this->setFlash('danger', 'Invalid security token. Please try again.');
            $this->redirect(BASE_URL . '/auth/login');
        }

        $username = $this->input('username');
        $password = $_POST['password'] ?? '';

        // Basic validation
        if (empty($username) || empty($password)) {
            $this->setFlash('danger', 'Username and password are required.');
            $this->redirect(BASE_URL . '/auth/login');
        }

        $result = $this->authService->login($username, $password);

        if (!$result['success']) {
            $this->setFlash('danger', $result['message']);
            $this->redirect(BASE_URL . '/auth/login');
        }

        // Redirect to intended URL or dashboard
        $intended = $_SESSION['intended_url'] ?? '';
        unset($_SESSION['intended_url']);

        if (!empty($intended) && strpos($intended, BASE_URL) !== false) {
            header('Location: ' . $intended);
            exit;
        }

        $this->redirect(BASE_URL . '/dashboard');
    }

    /**
     * GET /auth/logout - Log out the user
     */
    public function logout(array $params = []): void {
        $this->authService->logout();
        $this->setFlash('success', 'You have been logged out successfully.');
        $this->redirect(BASE_URL . '/auth/login');
    }

    /**
     * GET /profile - Show profile page
     */
    public function profile(array $params = []): void {
        $this->requireAuth();

        $userModel = new User();
        $user      = $userModel->findById($this->currentUserId());
        $flash     = $this->getFlash();

        $this->view('auth/profile', [
            'title'     => 'My Profile',
            'user'      => $user,
            'flash'     => $flash,
            'csrfToken' => $this->generateCsrfToken(),
        ]);
    }

    /**
     * POST /profile/update - Update profile
     */
    public function updateProfile(array $params = []): void {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->setFlash('danger', 'Invalid security token.');
            $this->redirect(BASE_URL . '/profile');
        }

        $data = [
            'full_name' => $this->input('full_name'),
            'email'     => $this->input('email'),
        ];

        $errors = $this->validate($data, [
            'full_name' => 'required|min:3|max:100',
            'email'     => 'required|email|max:150',
        ]);

        if (!empty($errors)) {
            $this->setFlash('danger', implode(' ', array_column($errors, 0)));
            $this->redirect(BASE_URL . '/profile');
        }

        $userModel = new User();
        $userModel->updateProfile($this->currentUserId(), $data);

        // Update session
        $_SESSION['full_name'] = $data['full_name'];

        logActivity('UPDATE_PROFILE', 'User updated their profile.');
        $this->setFlash('success', 'Profile updated successfully.');
        $this->redirect(BASE_URL . '/profile');
    }

    /**
     * POST /profile/password - Update password
     */
    public function updatePassword(array $params = []): void {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->setFlash('danger', 'Invalid security token.');
            $this->redirect(BASE_URL . '/profile');
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword     = $_POST['new_password']     ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $userModel = new User();
        $user      = $userModel->findById($this->currentUserId());

        if (!$userModel->verifyPassword($currentPassword, $user['password'])) {
            $this->setFlash('danger', 'Current password is incorrect.');
            $this->redirect(BASE_URL . '/profile');
        }

        if (strlen($newPassword) < 8) {
            $this->setFlash('danger', 'New password must be at least 8 characters.');
            $this->redirect(BASE_URL . '/profile');
        }

        if ($newPassword !== $confirmPassword) {
            $this->setFlash('danger', 'Passwords do not match.');
            $this->redirect(BASE_URL . '/profile');
        }

        $userModel->updatePassword($this->currentUserId(), $newPassword);

        logActivity('UPDATE_PASSWORD', 'User changed their password.');
        $this->setFlash('success', 'Password updated successfully.');
        $this->redirect(BASE_URL . '/profile');
    }
}
