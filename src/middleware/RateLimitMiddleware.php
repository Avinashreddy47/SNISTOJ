<?php

namespace SNISTOJ\Middleware;

use SNISTOJ\Utils\Security;
use SNISTOJ\Utils\Response;

/**
 * Rate Limit Middleware
 * Prevents brute force and DOS attacks
 */
class RateLimitMiddleware
{
    /**
     * Check rate limit for endpoint
     * 
     * @param string $identifier Identifier (e.g., 'login', 'api.submit')
     * @param int $limit Maximum requests allowed
     * @param int $window Time window in seconds
     * @return bool True if under limit, false if exceeded
     */
    public static function check($identifier, $limit = 10, $window = 60)
    {
        return !Security::isRateLimited($identifier, $limit, $window);
    }

    /**
     * Enforce rate limit
     */
    public static function enforce($identifier, $limit = 10, $window = 60)
    {
        if (!self::check($identifier, $limit, $window)) {
            Response::rateLimitExceeded();
        }
    }
}
