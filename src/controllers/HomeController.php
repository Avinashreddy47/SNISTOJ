<?php

namespace SNISTOJ\Controllers;

/**
 * Home Controller
 * Handles home page
 */
class HomeController
{
    public function index()
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: /problems');
            exit;
        }

        include_once dirname(__DIR__) . '/views/home/index.php';
    }
}
