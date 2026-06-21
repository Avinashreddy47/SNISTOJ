<?php

namespace SNISTOJ\Controllers;

/**
 * Contest Controller
 * Handles contest-related operations
 */
class ContestController
{
    public function index()
    {
        echo "Contests list will be displayed here";
    }

    public function show()
    {
        $id = $_GET[0] ?? null;
        echo "Contest detail: " . htmlspecialchars($id);
    }

    public function register()
    {
        echo "User registered for contest";
    }
}
