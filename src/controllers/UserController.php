<?php

namespace SNISTOJ\Controllers;

use SNISTOJ\Services\UserService;
use SNISTOJ\Utils\Validator;
use SNISTOJ\Utils\Security;
use SNISTOJ\Utils\Logger;

/**
 * User Controller
 * Handles user registration, login, and profile management
 */
class UserController
{
    private $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    /**
     * Show registration form
     */
    public function showRegisterForm()
    {
        Security::setSecureHeaders();
        include_once dirname(__DIR__) . '/views/auth/register.php';
    }

    /**
     * Handle user registration
     */
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        // Verify CSRF token
        if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            die('CSRF token validation failed');
        }

        // Validate input
        $validator = Validator::make($_POST);
        $validator->required('username')
                  ->length('username', 3, 20)
                  ->username('username')
                  ->required('email')
                  ->email('email')
                  ->required('password')
                  ->length('password', 8, 255)
                  ->required('password_confirm')
                  ->matches('password', 'password_confirm', 'Passwords do not match');

        if ($validator->fails()) {
            http_response_code(422);
            return [
                'success' => false,
                'errors' => $validator->errors(),
            ];
        }

        try {
            // Create user
            $user = $this->userService->createUser([
                'username' => $_POST['username'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
            ]);

            Logger::info('User registered', ['username' => $_POST['username']]);

            // Redirect to login
            header('Location: /login');
            exit;

        } catch (\Exception $e) {
            Logger::error('Registration failed', ['error' => $e->getMessage()]);
            http_response_code(500);
            return [
                'success' => false,
                'message' => 'Registration failed. Please try again.',
            ];
        }
    }

    /**
     * Show login form
     */
    public function showLoginForm()
    {
        Security::setSecureHeaders();
        include_once dirname(__DIR__) . '/views/auth/login.php';
    }

    /**
     * Handle user login
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        // Rate limiting
        if (Security::isRateLimited('login', 5, 300)) {
            http_response_code(429);
            Logger::warning('Too many login attempts', ['ip' => Security::getClientIP()]);
            die('Too many login attempts. Please try again later.');
        }

        // Verify CSRF token
        if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            die('CSRF token validation failed');
        }

        // Validate input
        $validator = Validator::make($_POST);
        $validator->required('username')
                  ->required('password');

        if ($validator->fails()) {
            http_response_code(422);
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        try {
            // Authenticate user
            $user = $this->userService->authenticate(
                $_POST['username'],
                $_POST['password']
            );

            if (!$user) {
                Logger::warning('Failed login attempt', ['username' => $_POST['username']]);
                http_response_code(401);
                return ['success' => false, 'message' => 'Invalid credentials'];
            }

            // Create session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            Logger::info('User logged in', ['username' => $user['username']]);

            // Redirect to home
            header('Location: /home');
            exit;

        } catch (\Exception $e) {
            Logger::error('Login failed', ['error' => $e->getMessage()]);
            http_response_code(500);
            return ['success' => false, 'message' => 'Login failed'];
        }
    }

    /**
     * Handle logout
     */
    public function logout()
    {
        Logger::info('User logged out', ['user_id' => $_SESSION['user_id'] ?? null]);
        session_destroy();
        header('Location: /');
        exit;
    }

    /**
     * Show user profile
     */
    public function showProfile($username)
    {
        try {
            $user = $this->userService->getUserByUsername($username);

            if (!$user) {
                http_response_code(404);
                die('User not found');
            }

            include_once dirname(__DIR__) . '/views/user/profile.php';

        } catch (\Exception $e) {
            Logger::error('Profile retrieval failed', ['error' => $e->getMessage()]);
            http_response_code(500);
            die('Failed to load profile');
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile()
    {
        // Check if user is authenticated
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            die('Unauthorized');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        // Verify CSRF token
        if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            die('CSRF token validation failed');
        }

        try {
            $this->userService->updateUser($_SESSION['user_id'], [
                'email' => $_POST['email'] ?? null,
                'full_name' => $_POST['full_name'] ?? null,
            ]);

            Logger::info('Profile updated', ['user_id' => $_SESSION['user_id']]);

            return ['success' => true, 'message' => 'Profile updated successfully'];

        } catch (\Exception $e) {
            Logger::error('Profile update failed', ['error' => $e->getMessage()]);
            http_response_code(500);
            return ['success' => false, 'message' => 'Failed to update profile'];
        }
    }
}
