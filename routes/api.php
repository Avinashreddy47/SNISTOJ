<?php
/**
 * API Routes
 * Define all application routes here
 */

use SNISTOJ\Routing\Router;
use SNISTOJ\Middleware\AuthMiddleware;
use SNISTOJ\Middleware\CSRFMiddleware;
use SNISTOJ\Middleware\RateLimitMiddleware;

// Public routes
Router::get('/', 'HomeController@index');
Router::get('/login', 'AuthController@showLogin', ['middleware' => [AuthMiddleware::class . '@guestOnly']]);
Router::post('/login', 'AuthController@login', ['middleware' => [CSRFMiddleware::class . '@require', 'RateLimitMiddleware@enforce:login:5:300']]);
Router::get('/register', 'AuthController@showRegister', ['middleware' => [AuthMiddleware::class . '@guestOnly']]);
Router::post('/register', 'AuthController@register', ['middleware' => [CSRFMiddleware::class . '@require']]);
Router::get('/logout', 'AuthController@logout', ['middleware' => [AuthMiddleware::class . '@requireAuth']]);

// User routes
Router::get('/user/profile', 'UserController@showProfile', ['middleware' => [AuthMiddleware::class . '@requireAuth']]);
Router::post('/user/update', 'UserController@updateProfile', ['middleware' => [AuthMiddleware::class . '@requireAuth', CSRFMiddleware::class . '@require']]);

// Problem routes
Router::get('/problems', 'ProblemController@index');
Router::get('/problem/:id', 'ProblemController@show');
Router::post('/problem/submit', 'ProblemController@submit', ['middleware' => [AuthMiddleware::class . '@requireAuth', CSRFMiddleware::class . '@require']]);

// Contest routes
Router::get('/contests', 'ContestController@index');
Router::get('/contest/:id', 'ContestController@show');
Router::post('/contest/register', 'ContestController@register', ['middleware' => [AuthMiddleware::class . '@requireAuth', CSRFMiddleware::class . '@require']]);

// Admin routes
Router::get('/admin/dashboard', 'AdminController@dashboard', ['middleware' => [AuthMiddleware::class . '@requireAuth', 'AdminMiddleware@requireAdmin']]);
Router::post('/admin/problem/create', 'AdminController@createProblem', ['middleware' => [AuthMiddleware::class . '@requireAuth', 'AdminMiddleware@requireAdmin', CSRFMiddleware::class . '@require']]);

// 404 fallback is handled by Router::dispatch()
