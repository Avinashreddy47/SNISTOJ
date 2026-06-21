<?php

namespace SNISTOJ\Middleware;

use SNISTOJ\Utils\Logger;

/**
 * Authentication Middleware
 * Ensures user is logged in before accessing protected routes
 */
class AuthMiddleware
{
    /**
     * Handle authentication check
     * 
     * @return bool|array User data if authenticated, false otherwise
     */
    public static function handle()
    {
        if (!isset($_SESSION['user_id'])) {
            Logger::warning('Unauthorized access attempt', [
                'ip' => $_SERVER['REMOTE_ADDR'],
                'path' => $_SERVER['REQUEST_URI'],
            ]);
            return false;
        }

        return [
            'user_id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'] ?? null,
        ];
    }

    /**
     * Redirect to login if not authenticated
     */
    public static function requireAuth()
    {
        if (!self::handle()) {
            header('Location: /login');
            exit;
        }
    }

    /**
     * Redirect to home if already authenticated
     */
    public static function guestOnly()
    {
        if (self::handle()) {
            header('Location: /home');
            exit;
        }
    }
}
