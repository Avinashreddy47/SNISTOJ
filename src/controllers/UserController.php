<?php

namespace SNISTOJ\Controllers;

use SNISTOJ\Services\UserService;
use SNISTOJ\Utils\Security;
use SNISTOJ\Utils\Logger;
use SNISTOJ\Utils\Response;

/**
 * User Controller
 * Handles user profile management
 * Note: Authentication is handled by AuthController
 */
class UserController extends BaseController
{
    private $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    /**
     * Show user profile
     */
    public function showProfile($username)
    {
        try {
            $user = $this->userService->getUserByUsername($username);

            if (!$user) {
                Response::notFound('User not found');
            }

            $this->render('user/profile', ['user' => $user]);

        } catch (\Exception $e) {
            Logger::error('Profile retrieval failed', ['error' => $e->getMessage()]);
            Response::serverError('Failed to load profile');
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile()
    {
        $this->requireAuth();
        $this->requirePost();

        if (!Security::verifyCSRFToken($this->post('csrf_token'))) {
            Response::forbidden('CSRF token validation failed');
        }

        try {
            $this->userService->updateUser($this->getUserId(), [
                'email' => $this->post('email'),
                'full_name' => $this->post('full_name'),
            ]);

            Logger::info('Profile updated', ['user_id' => $this->getUserId()]);
            Response::success('Profile updated successfully');

        } catch (\Exception $e) {
            Logger::error('Profile update failed', ['error' => $e->getMessage()]);
            Response::serverError('Failed to update profile');
        }
    }
}
