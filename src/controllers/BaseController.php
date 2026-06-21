<?php

namespace SNISTOJ\Controllers;

use SNISTOJ\Utils\Response;
use SNISTOJ\Utils\View;

/**
 * Base Controller
 * Provides common functionality for all controllers
 */
class BaseController
{
    /**
     * Render view
     */
    protected function render($view, $data = [])
    {
        View::render($view, $data);
    }

    /**
     * Check if request is POST
     */
    protected function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Check if request is GET
     */
    protected function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * Get POST parameter
     */
    protected function post($key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Get GET parameter
     */
    protected function query($key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Check if user is authenticated
     */
    protected function isAuthenticated()
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Get current user ID
     */
    protected function getUserId()
    {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Require authentication
     */
    protected function requireAuth()
    {
        if (!$this->isAuthenticated()) {
            Response::unauthorized('Authentication required');
        }
    }

    /**
     * Require POST request
     */
    protected function requirePost()
    {
        if (!$this->isPost()) {
            Response::methodNotAllowed();
        }
    }

    /**
     * Require GET request
     */
    protected function requireGet()
    {
        if (!$this->isGet()) {
            Response::methodNotAllowed();
        }
    }
}
