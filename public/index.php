<?php
/**
 * SNISTOJ Entry Point
 * All requests route through this file
 */

// Define base paths
define('BASE_PATH', dirname(__DIR__));

// Load bootstrap
require_once BASE_PATH . '/bootstrap.php';

use SNISTOJ\Routing\Router;
use SNISTOJ\Middleware\AuthMiddleware;
use SNISTOJ\Middleware\CSRFMiddleware;
use SNISTOJ\Middleware\RateLimitMiddleware;
use SNISTOJ\Middleware\RequestLoggerMiddleware;

// Log request
RequestLoggerMiddleware::handle();

// Define routes
require_once BASE_PATH . '/routes/api.php';

// Dispatch request
Router::dispatch();
