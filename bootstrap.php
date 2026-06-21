<?php

/**
 * SNISTOJ Bootstrap File
 * Entry point for the application
 * Initializes configuration, security, logging, and error handling
 */

// Define base path
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
    define('APP_PATH', BASE_PATH . '/');
    define('SRC_PATH', BASE_PATH . '/src/');
    define('CONFIG_PATH', BASE_PATH . '/config/');
}

// Start session with secure settings
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
ini_set('session.cookie_samesite', 'Strict');
session_start();

// Autoloader for classes
spl_autoload_register(function ($class) {
    // Replace namespace separators with directory separators
    $file = str_replace('\\', '/', $class);
    $path = BASE_PATH . '/' . $file . '.php';

    if (file_exists($path)) {
        require_once $path;
        return true;
    }

    return false;
});

// Load configuration and environment
require_once CONFIG_PATH . 'Environment.php';
require_once CONFIG_PATH . 'Config.php';
require_once CONFIG_PATH . 'Database.php';

use SNISTOJ\Config\Config;
use SNISTOJ\Config\Environment;
use SNISTOJ\Utils\Logger;
use SNISTOJ\Utils\Security;
use SNISTOJ\Utils\ExceptionHandler;

// Load environment variables
Environment::load();

// Set up security headers
Security::setSecureHeaders();

// Initialize logging
Logger::initialize();

// Set error and exception handlers
set_exception_handler([ExceptionHandler::class, 'handle']);
register_shutdown_function([ExceptionHandler::class, 'shutdown']);

// Error reporting
if (Config::isDebug()) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', Config::getLogging()['file']);
}

// Log application startup
Logger::info('Application started', [
    'environment' => Config::getApp()['env'],
    'debug' => Config::isDebug(),
    'ip' => Security::getClientIP(),
]);

// Make config available globally (optional)
// This can be removed if using dependency injection
global $config;
$config = Config::getInstance();

return [
    'config' => Config::getInstance(),
    'loaded' => true,
];
