<?php

namespace SNISTOJ\Middleware;

use SNISTOJ\Utils\Security;
use SNISTOJ\Utils\Logger;

/**
 * CSRF Middleware
 * Validates CSRF tokens on form submissions
 */
class CSRFMiddleware
{
    /**
     * Handle CSRF validation for POST requests
     * 
     * @return bool True if valid, false otherwise
     */
    public static function handle()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return true;
        }

        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

        if (!$token || !Security::verifyCSRFToken($token)) {
            Logger::warning('CSRF token validation failed', [
                'ip' => Security::getClientIP(),
                'path' => $_SERVER['REQUEST_URI'],
            ]);
            return false;
        }

        return true;
    }

    /**
     * Require valid CSRF token
     */
    public static function require()
    {
        if (!self::handle()) {
            http_response_code(403);
            die('CSRF token validation failed');
        }
    }

    /**
     * Inject CSRF token into response
     * Use in templates: <?php echo CSRFMiddleware::token(); ?>
     */
    public static function token()
    {
        return Security::getCSRFTokenField();
    }
}
