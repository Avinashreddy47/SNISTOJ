<?php

namespace SNISTOJ\Config;

/**
 * Environment Configuration Manager
 * Loads and manages environment variables from .env file
 */
class Environment
{
    private static $config = [];
    private static $loaded = false;

    /**
     * Load environment variables from .env file
     */
    public static function load($envFile = null)
    {
        if (self::$loaded) {
            return;
        }

        if ($envFile === null) {
            $envFile = dirname(dirname(__DIR__)) . '/.env';
        }

        if (file_exists($envFile)) {
            self::parseEnvFile($envFile);
        }

        self::$loaded = true;
    }

    /**
     * Parse .env file and load variables
     */
    private static function parseEnvFile($file)
    {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parse key=value
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Remove quotes if present
                if (preg_match('/^["\'](.+)["\']$/', $value, $matches)) {
                    $value = $matches[1];
                }

                self::$config[$key] = $value;
                putenv("$key=$value");
            }
        }
    }

    /**
     * Get environment variable
     */
    public static function get($key, $default = null)
    {
        if (!self::$loaded) {
            self::load();
        }

        return self::$config[$key] ?? getenv($key) ?: $default;
    }

    /**
     * Set environment variable
     */
    public static function set($key, $value)
    {
        self::$config[$key] = $value;
        putenv("$key=$value");
    }

    /**
     * Get all configuration
     */
    public static function all()
    {
        if (!self::$loaded) {
            self::load();
        }

        return self::$config;
    }
}
