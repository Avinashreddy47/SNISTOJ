<?php

namespace SNISTOJ\Controllers;

use SNISTOJ\Services\UserService;
use SNISTOJ\Utils\Security;
use SNISTOJ\Utils\Logger;

/**
 * User Controller
 * Handles user profile management
 * Note: Authentication is handled by AuthController
 */
class UserController
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
