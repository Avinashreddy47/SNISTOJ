<?php

namespace SNISTOJ\Controllers;

use SNISTOJ\Services\UserService;
use SNISTOJ\Utils\Validator;
use SNISTOJ\Utils\Security;
use SNISTOJ\Utils\Logger;
use SNISTOJ\Utils\Response;

/**
 * Auth Controller
 * Handles authentication
 */
class AuthController extends BaseController
{
    private $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function showLogin()
    {
        $this->render('auth/login');
    }

    public function showRegister()
    {
        $this->render('auth/register');
    }

    public function register()
    {
        $this->requirePost();

        if (!Security::verifyCSRFToken($this->post('csrf_token'))) {
            Response::forbidden('CSRF token validation failed');
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
            Response::validationError($validator->errors());
        }

        try {
            $this->userService->createUser([
                'username' => $_POST['username'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
            ]);

            Logger::info('User registered', ['username' => $_POST['username']]);
            Response::redirect('/login');

        } catch (\Exception $e) {
            Logger::error('Registration failed', ['error' => $e->getMessage()]);
            Response::serverError('Registration failed');
        }
    }

    public function login()
    {
        $this->requirePost();

        if (Security::isRateLimited('login', 5, 300)) {
            Logger::warning('Too many login attempts', ['ip' => Security::getClientIP()]);
            Response::rateLimitExceeded();
        }

        if (!Security::verifyCSRFToken($this->post('csrf_token'))) {
            Response::forbidden('CSRF token validation failed');
        }

        $validator = Validator::make($_POST);
        $validator->required('username')
                  ->required('password');

        if ($validator->fails()) {
            Response::validationError($validator->errors());
        }

        try {
            $user = $this->userService->authenticate($this->post('username'), $this->post('password'));

            if (!$user) {
                Logger::warning('Failed login', ['username' => $this->post('username')]);
                Response::unauthorized('Invalid credentials');
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            Logger::info('User logged in', ['username' => $user['username']]);
            Response::redirect('/problems');

        } catch (\Exception $e) {
            Logger::error('Login failed', ['error' => $e->getMessage()]);
            Response::serverError('Login failed');
        }
    }

    public function logout()
    {
        Logger::info('User logged out', ['user_id' => $this->getUserId()]);
        session_destroy();
        Response::redirect('/');
    }
}
