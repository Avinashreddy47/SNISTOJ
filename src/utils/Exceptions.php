<?php

namespace SNISTOJ\Exceptions;

/**
 * Base Exception
 */
class Exception extends \Exception {}

/**
 * Validation Exception
 * Thrown when input validation fails
 */
class ValidationException extends Exception
{
    private $errors = [];

    public function __construct($message, $errors = [])
    {
        parent::__construct($message);
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}

/**
 * Authentication Exception
 * Thrown when authentication fails
 */
class AuthenticationException extends Exception
{
    public function __construct($message = 'Authentication failed')
    {
        parent::__construct($message, 401);
    }
}

/**
 * Authorization Exception
 * Thrown when user lacks permissions
 */
class AuthorizationException extends Exception
{
    public function __construct($message = 'Insufficient permissions')
    {
        parent::__construct($message, 403);
    }
}

/**
 * Not Found Exception
 * Thrown when resource doesn't exist
 */
class NotFoundException extends Exception
{
    public function __construct($message = 'Resource not found')
    {
        parent::__construct($message, 404);
    }
}

/**
 * Database Exception
 * Thrown when database operations fail
 */
class DatabaseException extends Exception
{
    public function __construct($message = 'Database error')
    {
        parent::__construct($message, 500);
    }
}

/**
 * Runtime Exception
 * Thrown for runtime errors
 */
class RuntimeException extends Exception {}
