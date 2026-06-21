<?php

namespace SNISTOJ\Routing;

use SNISTOJ\Utils\Logger;
use SNISTOJ\Utils\Response;

/**
 * Router
 * Handles routing and dispatches requests to controllers
 */
class Router
{
    private static $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
    ];

    private static $middlewares = [];

    /**
     * Register GET route
     */
    public static function get($path, $controller, $middlewares = [])
    {
        self::$routes['GET'][$path] = ['controller' => $controller, 'middlewares' => $middlewares];
    }

    /**
     * Register POST route
     */
    public static function post($path, $controller, $middlewares = [])
    {
        self::$routes['POST'][$path] = ['controller' => $controller, 'middlewares' => $middlewares];
    }

    /**
     * Register PUT route
     */
    public static function put($path, $controller, $middlewares = [])
    {
        self::$routes['PUT'][$path] = ['controller' => $controller, 'middlewares' => $middlewares];
    }

    /**
     * Register DELETE route
     */
    public static function delete($path, $controller, $middlewares = [])
    {
        self::$routes['DELETE'][$path] = ['controller' => $controller, 'middlewares' => $middlewares];
    }

    /**
     * Dispatch request to controller
     */
    public static function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = '/' . trim($path, '/');

        Logger::debug('Routing request', ['method' => $method, 'path' => $path]);

        // Exact match
        if (isset(self::$routes[$method][$path])) {
            return self::execute(self::$routes[$method][$path]);
        }

        // Pattern matching (e.g., /user/:id)
        $route = self::matchPattern($method, $path);
        if ($route) {
            return self::execute($route['route']);
        }

        // 404
        Response::notFound('Route not found');
    }

    /**
     * Match path patterns
     */
    private static function matchPattern($method, $path)
    {
        if (!isset(self::$routes[$method])) {
            return null;
        }

        foreach (self::$routes[$method] as $pattern => $route) {
            // Convert pattern to regex (e.g., /user/:id -> /user/(\d+))
            $regex = preg_replace('/:(\w+)/', '(\w+)', $pattern);
            $regex = str_replace('/', '\/', $regex);

            if (preg_match("/^{$regex}$/", $path, $matches)) {
                array_shift($matches); // Remove full match
                $_GET = array_merge($_GET, $matches); // Pass to controller
                return ['route' => $route, 'params' => $matches];
            }
        }

        return null;
    }

    /**
     * Execute controller with middlewares
     */
    private static function execute($route)
    {
        // Run middlewares
        foreach ($route['middlewares'] as $middleware) {
            if (is_callable($middleware)) {
                if ($middleware() === false) {
                    Response::forbidden('Middleware blocked request');
                }
            }
        }

        // Execute controller
        list($class, $method) = explode('@', $route['controller']);
        $className = 'SNISTOJ\\Controllers\\' . $class;

        if (!class_exists($className)) {
            Logger::error('Controller not found', ['class' => $className]);
            Response::serverError('Controller not found');
        }

        $controller = new $className();
        return $controller->$method();
    }

    /**
     * Get URL for route (for reverse routing)
     */
    public static function url($path, $params = [])
    {
        $url = $path;
        foreach ($params as $key => $value) {
            $url = str_replace(":{$key}", $value, $url);
        }
        return $url;
    }
}
