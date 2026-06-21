<?php

namespace SNISTOJ\Utils;

/**
 * HTTP Response Helper
 * Centralizes HTTP status codes and response handling
 */
class Response
{
    // Success status codes
    const OK = 200;
    const CREATED = 201;
    const ACCEPTED = 202;

    // Client error status codes
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const METHOD_NOT_ALLOWED = 405;
    const UNPROCESSABLE_ENTITY = 422;
    const TOO_MANY_REQUESTS = 429;

    // Server error status codes
    const INTERNAL_SERVER_ERROR = 500;
    const SERVICE_UNAVAILABLE = 503;

    /**
     * Send JSON response
     */
    public static function json($data, $statusCode = self::OK)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Send success response
     */
    public static function success($message = 'Success', $data = null, $statusCode = self::OK)
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        self::json($response, $statusCode);
    }

    /**
     * Send error response
     */
    public static function error($message = 'Error', $statusCode = self::INTERNAL_SERVER_ERROR, $errors = null)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        self::json($response, $statusCode);
    }

    /**
     * Send created response
     */
    public static function created($message = 'Created', $data = null)
    {
        self::success($message, $data, self::CREATED);
    }

    /**
     * Send bad request response
     */
    public static function badRequest($message = 'Bad Request', $errors = null)
    {
        self::error($message, self::BAD_REQUEST, $errors);
    }

    /**
     * Send unauthorized response
     */
    public static function unauthorized($message = 'Unauthorized')
    {
        self::error($message, self::UNAUTHORIZED);
    }

    /**
     * Send forbidden response
     */
    public static function forbidden($message = 'Forbidden')
    {
        self::error($message, self::FORBIDDEN);
    }

    /**
     * Send not found response
     */
    public static function notFound($message = 'Not Found')
    {
        self::error($message, self::NOT_FOUND);
    }

    /**
     * Send method not allowed response
     */
    public static function methodNotAllowed($message = 'Method Not Allowed')
    {
        self::error($message, self::METHOD_NOT_ALLOWED);
    }

    /**
     * Send validation error response
     */
    public static function validationError($errors)
    {
        self::error('Validation failed', self::UNPROCESSABLE_ENTITY, $errors);
    }

    /**
     * Send rate limit exceeded response
     */
    public static function rateLimitExceeded($message = 'Too many requests. Please try again later.')
    {
        self::error($message, self::TOO_MANY_REQUESTS);
    }

    /**
     * Send server error response
     */
    public static function serverError($message = 'Internal Server Error')
    {
        self::error($message, self::INTERNAL_SERVER_ERROR);
    }

    /**
     * Redirect to URL
     */
    public static function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }
}
