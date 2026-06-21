<?php

namespace SNISTOJ\Services;

use SNISTOJ\Utils\Logger;

/**
 * Compiler Service
 * Handles code compilation and execution
 */
class CompilerService
{
    private $languages = ['c', 'cpp', 'java', 'python'];
    private $timeLimit = 10;
    private $memoryLimit = 256;

    /**
     * Compile and run code
     */
    public function compile($code, $language, $input = '', $timeLimit = 10, $memoryLimit = 256)
    {
        if (!in_array($language, $this->languages)) {
            throw new \Exception('Unsupported language: ' . $language);
        }

        try {
            $tempDir = sys_get_temp_dir() . '/snistoj_' . uniqid();
            mkdir($tempDir);

            $fileName = $this->getFileName($language, $tempDir);
            file_put_contents($fileName, $code);

            $compileResult = $this->compileLanguage($language, $fileName, $tempDir);

            if ($compileResult['error']) {
                Logger::warning('Compilation error', [
                    'language' => $language,
                    'error' => $compileResult['message']
                ]);
                return $compileResult;
            }

            $executeResult = $this->executeLanguage($language, $fileName, $tempDir, $input, $timeLimit);

            $this->cleanup($tempDir);

            return $executeResult;

        } catch (\Exception $e) {
            Logger::error('Compilation service error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get file name based on language
     */
    private function getFileName($language, $tempDir)
    {
        switch ($language) {
            case 'c':
                return $tempDir . '/code.c';
            case 'cpp':
                return $tempDir . '/code.cpp';
            case 'java':
                return $tempDir . '/Code.java';
            case 'python':
                return $tempDir . '/code.py';
            default:
                return $tempDir . '/code';
        }
    }

    /**
     * Compile based on language
     */
    private function compileLanguage($language, $fileName, $tempDir)
    {
        switch ($language) {
            case 'c':
                return $this->compileC($fileName, $tempDir);
            case 'cpp':
                return $this->compileCpp($fileName, $tempDir);
            case 'java':
                return $this->compileJava($fileName, $tempDir);
            case 'python':
                return ['error' => false, 'message' => 'Python interpreted'];
            default:
                return ['error' => true, 'message' => 'Unknown language'];
        }
    }

    /**
     * Compile C code
     */
    private function compileC($fileName, $tempDir)
    {
        $outputFile = $tempDir . '/code';
        $cmd = "gcc -o {$outputFile} {$fileName} 2>&1";
        
        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0) {
            return [
                'error' => true,
                'message' => implode('\n', $output)
            ];
        }

        return ['error' => false, 'message' => 'Compiled successfully'];
    }

    /**
     * Compile C++ code
     */
    private function compileCpp($fileName, $tempDir)
    {
        $outputFile = $tempDir . '/code';
        $cmd = "g++ -o {$outputFile} {$fileName} 2>&1";
        
        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0) {
            return [
                'error' => true,
                'message' => implode('\n', $output)
            ];
        }

        return ['error' => false, 'message' => 'Compiled successfully'];
    }

    /**
     * Compile Java code
     */
    private function compileJava($fileName, $tempDir)
    {
        $cmd = "javac {$fileName} 2>&1";
        
        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0) {
            return [
                'error' => true,
                'message' => implode('\n', $output)
            ];
        }

        return ['error' => false, 'message' => 'Compiled successfully'];
    }

    /**
     * Execute code based on language
     */
    private function executeLanguage($language, $fileName, $tempDir, $input, $timeLimit)
    {
        switch ($language) {
            case 'c':
            case 'cpp':
                return $this->executeCompiledLanguage($tempDir . '/code', $input, $timeLimit);
            case 'java':
                return $this->executeJava($tempDir, $input, $timeLimit);
            case 'python':
                return $this->executePython($fileName, $input, $timeLimit);
            default:
                return ['error' => true, 'message' => 'Unknown language'];
        }
    }

    /**
     * Execute compiled languages (C, C++)
     */
    private function executeCompiledLanguage($executable, $input, $timeLimit)
    {
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w']
        ];

        $process = proc_open($executable, $descriptors, $pipes);

        if (!is_resource($process)) {
            return ['error' => true, 'message' => 'Failed to execute'];
        }

        fwrite($pipes[0], $input);
        fclose($pipes[0]);

        $startTime = microtime(true);
        $output = '';
        $error = '';

        while (!feof($pipes[1])) {
            $output .= fgets($pipes[1]);
            if (microtime(true) - $startTime > $timeLimit) {
                proc_terminate($process);
                return ['error' => true, 'message' => 'Time limit exceeded', 'status' => 'TLE'];
            }
        }

        while (!feof($pipes[2])) {
            $error .= fgets($pipes[2]);
        }

        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);

        $executionTime = microtime(true) - $startTime;

        return [
            'error' => false,
            'output' => trim($output),
            'execution_time' => round($executionTime, 3),
            'status' => 'OK'
        ];
    }

    /**
     * Execute Python code
     */
    private function executePython($fileName, $input, $timeLimit)
    {
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w']
        ];

        $process = proc_open("python3 {$fileName}", $descriptors, $pipes);

        if (!is_resource($process)) {
            return ['error' => true, 'message' => 'Failed to execute'];
        }

        fwrite($pipes[0], $input);
        fclose($pipes[0]);

        $startTime = microtime(true);
        $output = '';

        while (!feof($pipes[1]) && microtime(true) - $startTime < $timeLimit) {
            $output .= fgets($pipes[1]);
        }

        if (microtime(true) - $startTime >= $timeLimit) {
            proc_terminate($process);
            return ['error' => true, 'message' => 'Time limit exceeded', 'status' => 'TLE'];
        }

        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);

        $executionTime = microtime(true) - $startTime;

        return [
            'error' => false,
            'output' => trim($output),
            'execution_time' => round($executionTime, 3),
            'status' => 'OK'
        ];
    }

    /**
     * Execute Java code
     */
    private function executeJava($tempDir, $input, $timeLimit)
    {
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w']
        ];

        $cmd = "cd {$tempDir} && java Code";
        $process = proc_open($cmd, $descriptors, $pipes);

        if (!is_resource($process)) {
            return ['error' => true, 'message' => 'Failed to execute'];
        }

        fwrite($pipes[0], $input);
        fclose($pipes[0]);

        $startTime = microtime(true);
        $output = '';

        while (!feof($pipes[1]) && microtime(true) - $startTime < $timeLimit) {
            $output .= fgets($pipes[1]);
        }

        if (microtime(true) - $startTime >= $timeLimit) {
            proc_terminate($process);
            return ['error' => true, 'message' => 'Time limit exceeded', 'status' => 'TLE'];
        }

        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);

        $executionTime = microtime(true) - $startTime;

        return [
            'error' => false,
            'output' => trim($output),
            'execution_time' => round($executionTime, 3),
            'status' => 'OK'
        ];
    }

    /**
     * Cleanup temporary files
     */
    private function cleanup($directory)
    {
        if (is_dir($directory)) {
            array_map('unlink', glob("{$directory}/*.*"));
            rmdir($directory);
        }
    }

    /**
     * Supported languages
     */
    public function getSupportedLanguages()
    {
        return $this->languages;
    }
}
