<?php

namespace SNISTOJ\Controllers;

use SNISTOJ\Services\CompilerService;
use SNISTOJ\Utils\Logger;

/**
 * Compiler Controller
 * Handles code compilation and execution
 */
class CompilerController
{
    private $compilerService;

    public function __construct()
    {
        $this->compilerService = new CompilerService();
    }

    public function index()
    {
        include_once dirname(__DIR__) . '/views/compiler/index.php';
    }

    public function run()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        try {
            $code = $_POST['code'] ?? '';
            $language = $_POST['language'] ?? 'cpp';
            $input = $_POST['input'] ?? '';

            if (empty($code)) {
                http_response_code(400);
                return ['success' => false, 'message' => 'Code cannot be empty'];
            }

            $result = $this->compilerService->compile($code, $language, $input);

            Logger::info('Code executed', [
                'user_id' => $_SESSION['user_id'],
                'language' => $language,
                'status' => $result['status'] ?? 'unknown'
            ]);

            return [
                'success' => !$result['error'],
                'output' => $result['output'] ?? '',
                'error' => $result['message'] ?? '',
                'execution_time' => $result['execution_time'] ?? 0,
                'status' => $result['status'] ?? 'ERROR'
            ];

        } catch (\Exception $e) {
            Logger::error('Compilation failed', ['error' => $e->getMessage()]);
            http_response_code(500);
            return ['success' => false, 'message' => 'Compilation service error'];
        }
    }
}
