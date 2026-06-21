<?php

namespace SNISTOJ\Controllers;

/**
 * Contest Controller
 * Handles contest-related operations
 */
class ContestController extends BaseController
{
    public function index()
    {
        $this->render('contests/index');
    }

    public function show()
    {
        $id = $this->query(0);
        $this->render('contests/show', ['id' => $id]);
    }

    public function register()
    {
        $this->render('contests/register');
    }
}
