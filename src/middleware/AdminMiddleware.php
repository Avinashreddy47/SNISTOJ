<?php

namespace SNISTOJ\Middleware;

use SNISTOJ\Utils\Logger;

/**
 * Admin Middleware
 * Ensures user has admin role
 */
class AdminMiddleware
{
    public static function requireAdmin()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            Logger::warning('Unauthorized admin access attempt', [
                'user_id' => $_SESSION['user_id'] ?? null,
                'ip' => $_SERVER['REMOTE_ADDR'],
            ]);
            http_response_code(403);
            die('Access denied');
        }
    }
}
