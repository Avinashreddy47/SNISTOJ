<?php

namespace SNISTOJ\Middleware;

use SNISTOJ\Utils\Logger;

/**
 * Request Logger Middleware
 * Logs all incoming requests
 */
class RequestLoggerMiddleware
{
    public static function handle()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];
        $ip = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $userId = $_SESSION['user_id'] ?? 'Guest';

        Logger::info('HTTP Request', [
            'method' => $method,
            'uri' => $uri,
            'ip' => $ip,
            'user_id' => $userId,
            'user_agent' => substr($userAgent, 0, 100),
        ]);
    }
}
