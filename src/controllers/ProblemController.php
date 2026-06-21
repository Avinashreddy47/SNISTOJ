<?php

namespace SNISTOJ\Controllers;

/**
 * Problem Controller
 * Handles problem-related operations
 */
class ProblemController extends BaseController
{
    public function index()
    {
        $this->render('problems/index');
    }

    public function show()
    {
        $id = $this->query(0);
        $this->render('problems/show', ['id' => $id]);
    }

    public function submit()
    {
        $this->render('problems/submit');
    }
}
