<?php

namespace SNISTOJ\Middleware;

use SNISTOJ\Utils\Logger;
use SNISTOJ\Utils\Response;

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
            Response::forbidden('Admin access required');
        }
    }
}
