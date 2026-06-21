<?php

namespace SNISTOJ\Utils;

/**
 * Security Utilities
 * Handles CSRF tokens, password hashing, input validation, etc.
 */
class Security
{
    /**
     * Generate and store CSRF token
     */
    public static function generateCSRFToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF token
     */
    public static function verifyCSRFToken($token)
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Get CSRF token for forms
     */
    public static function getCSRFTokenField()
    {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(self::generateCSRFToken()) . '">';
    }

    /**
     * Hash password securely using bcrypt
     */
    public static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Verify password against hash
     */
    public static function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Check if password needs rehashing (e.g., algorithm change)
     */
    public static function needsRehash($hash)
    {
        return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Sanitize string input
     */
    public static function sanitize($input)
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate email
     */
    public static function validateEmail($email)
    {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate username (alphanumeric + underscore, 3-20 chars)
     */
    public static function validateUsername($username)
    {
        return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username) === 1;
    }

    /**
     * Generate random token
     */
    public static function generateToken($length = 32)
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Set secure headers
     */
    public static function setSecureHeaders()
    {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\'');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }

    /**
     * Rate limit check (simple implementation)
     */
    public static function isRateLimited($key, $limit = 10, $window = 60)
    {
        if (!isset($_SESSION["rate_limit_$key"])) {
            $_SESSION["rate_limit_$key"] = [];
        }

        $now = time();
        $_SESSION["rate_limit_$key"] = array_filter(
            $_SESSION["rate_limit_$key"],
            fn($timestamp) => $now - $timestamp < $window
        );

        if (count($_SESSION["rate_limit_$key"]) >= $limit) {
            return true;
        }

        $_SESSION["rate_limit_$key"][] = $now;
        return false;
    }

    /**
     * Get client IP address safely
     */
    public static function getClientIP()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }

        // Validate IP
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }

        return '0.0.0.0';
    }
}
