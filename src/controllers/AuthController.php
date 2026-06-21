<?php

namespace SNISTOJ\Controllers;

use SNISTOJ\Services\UserService;
use SNISTOJ\Utils\Validator;
use SNISTOJ\Utils\Security;
use SNISTOJ\Utils\Logger;

/**
 * Auth Controller
 * Handles authentication
 */
class AuthController
{
    private $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function showLogin()
    {
        include_once dirname(__DIR__) . '/views/auth/login.php';
    }

    public function showRegister()
    {
        include_once dirname(__DIR__) . '/views/auth/register.php';
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            die('CSRF token validation failed');
        }

        $validator = Validator::make($_POST);
        $validator->required('username')
                  ->length('username', 3, 20)
                  ->username('username')
                  ->required('email')
                  ->email('email')
                  ->required('password')
                  ->length('password', 8, 255)
                  ->required('password_confirm')
                  ->matches('password', 'password_confirm');

        if ($validator->fails()) {
            Logger::warning('Registration validation failed', ['errors' => $validator->errors()]);
            http_response_code(422);
            return ['success' => false, 'errors' => $validator->errors()];
        }

        try {
            $this->userService->createUser([
                'username' => $_POST['username'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
            ]);

            Logger::info('User registered', ['username' => $_POST['username']]);
            header('Location: /login');
            exit;

        } catch (\Exception $e) {
            Logger::error('Registration failed', ['error' => $e->getMessage()]);
            http_response_code(500);
            return ['success' => false, 'message' => 'Registration failed'];
        }
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        if (Security::isRateLimited('login', 5, 300)) {
            http_response_code(429);
            Logger::warning('Too many login attempts', ['ip' => Security::getClientIP()]);
            die('Too many login attempts');
        }

        if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            die('CSRF token validation failed');
        }

        $validator = Validator::make($_POST);
        $validator->required('username')
                  ->required('password');

        if ($validator->fails()) {
            http_response_code(422);
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        try {
            $user = $this->userService->authenticate($_POST['username'], $_POST['password']);

            if (!$user) {
                Logger::warning('Failed login', ['username' => $_POST['username']]);
                http_response_code(401);
                return ['success' => false, 'message' => 'Invalid credentials'];
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            Logger::info('User logged in', ['username' => $user['username']]);
            header('Location: /problems');
            exit;

        } catch (\Exception $e) {
            Logger::error('Login failed', ['error' => $e->getMessage()]);
            http_response_code(500);
            return ['success' => false, 'message' => 'Login failed'];
        }
    }

    public function logout()
    {
        Logger::info('User logged out', ['user_id' => $_SESSION['user_id'] ?? null]);
        session_destroy();
        header('Location: /');
        exit;
    }
}
