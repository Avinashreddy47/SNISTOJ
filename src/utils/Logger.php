<?php

namespace SNISTOJ\Utils;

use SNISTOJ\Config\Config;

/**
 * Application Logger
 * Handles error logging and debugging
 */
class Logger
{
    const LEVEL_DEBUG = 'DEBUG';
    const LEVEL_INFO = 'INFO';
    const LEVEL_WARNING = 'WARNING';
    const LEVEL_ERROR = 'ERROR';
    const LEVEL_CRITICAL = 'CRITICAL';

    private static $logFile;
    private static $levels = [
        'debug' => 0,
        'info' => 1,
        'warning' => 2,
        'error' => 3,
        'critical' => 4,
    ];

    public static function initialize()
    {
        $config = Config::getLogging();
        self::$logFile = $config['file'];

        // Ensure log directory exists
        $logDir = dirname(self::$logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }

    /**
     * Log message
     */
    private static function log($level, $message, $context = [])
    {
        if (self::$logFile === null) {
            self::initialize();
        }

        $config = Config::getLogging();
        $currentLevel = self::$levels[strtolower($config['level'])] ?? 1;
        $messageLevel = self::$levels[strtolower($level)] ?? 1;

        // Only log if level meets threshold
        if ($messageLevel < $currentLevel && !Config::isDebug()) {
            return;
        }

        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? json_encode($context) : '';
        $logMessage = "[$timestamp] [$level] $message $contextStr\n";

        file_put_contents(self::$logFile, $logMessage, FILE_APPEND);
    }

    public static function debug($message, $context = [])
    {
        self::log(self::LEVEL_DEBUG, $message, $context);
    }

    public static function info($message, $context = [])
    {
        self::log(self::LEVEL_INFO, $message, $context);
    }

    public static function warning($message, $context = [])
    {
        self::log(self::LEVEL_WARNING, $message, $context);
    }

    public static function error($message, $context = [])
    {
        self::log(self::LEVEL_ERROR, $message, $context);
    }

    public static function critical($message, $context = [])
    {
        self::log(self::LEVEL_CRITICAL, $message, $context);
    }

    /**
     * Get recent logs
     */
    public static function getRecentLogs($lines = 50)
    {
        if (!file_exists(self::$logFile)) {
            return [];
        }

        $file = new \SplFileObject(self::$logFile, 'r');
        $file->seek(PHP_INT_MAX);
        $lastLine = $file->key();
        
        $logs = [];
        $startLine = max(0, $lastLine - $lines);
        
        $file->seek($startLine);
        foreach ($file as $line) {
            if (!empty(trim($line))) {
                $logs[] = trim($line);
            }
        }

        return $logs;
    }

    /**
     * Clear old logs (older than X days)
     */
    public static function clearOldLogs($days = 30)
    {
        if (!file_exists(self::$logFile)) {
            return;
        }

        $cutoffTime = time() - ($days * 86400);
        $lines = file(self::$logFile);
        $newLines = [];

        foreach ($lines as $line) {
            if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                $lineTime = strtotime($matches[1]);
                if ($lineTime > $cutoffTime) {
                    $newLines[] = $line;
                }
            }
        }

        file_put_contents(self::$logFile, implode('', $newLines));
    }
}

Logger::initialize();
