<?php

namespace SNISTOJ\Utils;

/**
 * View Renderer
 * Handles rendering of view templates
 */
class View
{
    private static $viewPath = '/src/views/';

    /**
     * Render a view template
     *
     * @param string $view View path relative to views directory (e.g., 'auth/login')
     * @param array $data Variables to pass to the view
     * @return void
     */
    public static function render($view, $data = [])
    {
        $viewFile = dirname(__DIR__, 2) . self::$viewPath . $view . '.php';

        if (!file_exists($viewFile)) {
            Logger::error('View file not found', ['view' => $view]);
            die('View not found: ' . $view);
        }

        // Extract data into variables
        extract($data);

        // Include view file
        include $viewFile;
    }

    /**
     * Get view path
     */
    public static function getPath($view)
    {
        return dirname(__DIR__, 2) . self::$viewPath . $view . '.php';
    }
}
