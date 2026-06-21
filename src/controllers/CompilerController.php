<?php

namespace SNISTOJ\Controllers;

use SNISTOJ\Services\CompilerService;
use SNISTOJ\Utils\Logger;
use SNISTOJ\Utils\Response;

/**
 * Compiler Controller
 * Handles code compilation and execution
 */
class CompilerController extends BaseController
{
    private $compilerService;

    public function __construct()
    {
        $this->compilerService = new CompilerService();
    }

    public function index()
    {
        $this->render('compiler/index');
    }

    public function run()
    {
        $this->requirePost();

        $code = $this->post('code', '');
        $language = $this->post('language', 'cpp');
        $input = $this->post('input', '');

        if (empty($code)) {
            Response::badRequest('Code cannot be empty');
        }

        try {
            $result = $this->compilerService->compile($code, $language, $input);

            Logger::info('Code executed', [
                'user_id' => $this->getUserId(),
                'language' => $language,
                'status' => $result['status'] ?? 'unknown'
            ]);

            Response::success('Code executed', [
                'output' => $result['output'] ?? '',
                'execution_time' => $result['execution_time'] ?? 0,
                'status' => $result['status'] ?? 'ERROR',
                'error' => $result['message'] ?? ''
            ]);

        } catch (\Exception $e) {
            Logger::error('Compilation failed', ['error' => $e->getMessage()]);
            Response::serverError('Compilation service error');
        }
    }
}
