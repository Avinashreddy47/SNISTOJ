<?php

namespace SNISTOJ\Utils;

/**
 * Validation Utility
 * Validates input data against various rules
 */
class Validator
{
    private $errors = [];
    private $data = [];

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * Create validator instance
     */
    public static function make($data)
    {
        return new self($data);
    }

    /**
     * Validate required field
     */
    public function required($field, $message = null)
    {
        if (empty($this->data[$field] ?? null)) {
            $this->errors[$field][] = $message ?? "$field is required";
        }
        return $this;
    }

    /**
     * Validate string length
     */
    public function length($field, $min = null, $max = null, $message = null)
    {
        $value = $this->data[$field] ?? '';
        $len = strlen($value);

        if ($min !== null && $len < $min) {
            $this->errors[$field][] = $message ?? "$field must be at least $min characters";
        }

        if ($max !== null && $len > $max) {
            $this->errors[$field][] = $message ?? "$field must not exceed $max characters";
        }

        return $this;
    }

    /**
     * Validate numeric
     */
    public function numeric($field, $message = null)
    {
        if (!is_numeric($this->data[$field] ?? null)) {
            $this->errors[$field][] = $message ?? "$field must be numeric";
        }
        return $this;
    }

    /**
     * Validate integer
     */
    public function integer($field, $message = null)
    {
        if (!filter_var($this->data[$field] ?? null, FILTER_VALIDATE_INT)) {
            $this->errors[$field][] = $message ?? "$field must be an integer";
        }
        return $this;
    }

    /**
     * Validate email
     */
    public function email($field, $message = null)
    {
        if (!Security::validateEmail($this->data[$field] ?? '')) {
            $this->errors[$field][] = $message ?? "$field must be a valid email";
        }
        return $this;
    }

    /**
     * Validate username
     */
    public function username($field, $message = null)
    {
        if (!Security::validateUsername($this->data[$field] ?? '')) {
            $this->errors[$field][] = $message ?? "$field must be alphanumeric (3-20 characters)";
        }
        return $this;
    }

    /**
     * Validate matches another field
     */
    public function matches($field, $otherField, $message = null)
    {
        if (($this->data[$field] ?? null) !== ($this->data[$otherField] ?? null)) {
            $this->errors[$field][] = $message ?? "$field must match $otherField";
        }
        return $this;
    }

    /**
     * Validate minimum value
     */
    public function min($field, $min, $message = null)
    {
        if ((int)($this->data[$field] ?? 0) < $min) {
            $this->errors[$field][] = $message ?? "$field must be at least $min";
        }
        return $this;
    }

    /**
     * Validate maximum value
     */
    public function max($field, $max, $message = null)
    {
        if ((int)($this->data[$field] ?? 0) > $max) {
            $this->errors[$field][] = $message ?? "$field must not exceed $max";
        }
        return $this;
    }

    /**
     * Validate in array
     */
    public function in($field, $allowed, $message = null)
    {
        if (!in_array($this->data[$field] ?? null, $allowed)) {
            $this->errors[$field][] = $message ?? "$field has invalid value";
        }
        return $this;
    }

    /**
     * Validate regex pattern
     */
    public function regex($field, $pattern, $message = null)
    {
        if (!preg_match($pattern, $this->data[$field] ?? '')) {
            $this->errors[$field][] = $message ?? "$field format is invalid";
        }
        return $this;
    }

    /**
     * Check if validation passed
     */
    public function passes()
    {
        return empty($this->errors);
    }

    /**
     * Check if validation failed
     */
    public function fails()
    {
        return !$this->passes();
    }

    /**
     * Get all errors
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * Get errors for specific field
     */
    public function getErrors($field = null)
    {
        if ($field === null) {
            return $this->errors;
        }
        return $this->errors[$field] ?? [];
    }

    /**
     * Get first error message
     */
    public function first($field)
    {
        return $this->errors[$field][0] ?? null;
    }

    /**
     * Get all error messages as flat array
     */
    public function getMessages()
    {
        $messages = [];
        foreach ($this->errors as $fieldErrors) {
            $messages = array_merge($messages, $fieldErrors);
        }
        return $messages;
    }
}

/**
 * Exception Handler
 */
class ExceptionHandler
{
    public static function handle(\Exception $e)
    {
        Logger::error('Exception caught', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'code' => $e->getCode(),
        ]);

        if (Config::isDebug()) {
            echo "<pre>";
            echo "Exception: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . " (Line " . $e->getLine() . ")\n";
            echo "Code: " . $e->getCode() . "\n";
            echo "</pre>";
        } else {
            echo "An error occurred. Please try again later.";
        }
    }

    public static function shutdown()
    {
        $error = error_get_last();
        if ($error !== null) {
            Logger::critical('Fatal error', [
                'message' => $error['message'],
                'file' => $error['file'],
                'line' => $error['line'],
            ]);
        }
    }
}
