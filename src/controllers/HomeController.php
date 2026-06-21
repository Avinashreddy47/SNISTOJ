<?php

namespace SNISTOJ\Controllers;

/**
 * Home Controller
 * Handles home page
 */
class HomeController extends BaseController
{
    public function index()
    {
        if ($this->isAuthenticated()) {
            header('Location: /problems');
            exit;
        }

        $this->render('home/index');
    }
}
