<?php

namespace SNISTOJ\Controllers;

/**
 * Admin Controller
 * Handles admin operations
 */
class AdminController extends BaseController
{
    public function dashboard()
    {
        $this->render('admin/dashboard');
    }

    public function createProblem()
    {
        $this->render('admin/create-problem');
    }
}
