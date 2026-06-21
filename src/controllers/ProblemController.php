<?php

namespace SNISTOJ\Controllers;

/**
 * Problem Controller
 * Handles problem-related operations
 */
class ProblemController
{
    public function index()
    {
        echo "Problems list will be displayed here";
    }

    public function show()
    {
        $id = $_GET[0] ?? null;
        echo "Problem detail: " . htmlspecialchars($id);
    }

    public function submit()
    {
        echo "Problem submitted";
    }
}
