<?php

namespace SNISTOJ\Services;

use SNISTOJ\Repository\UserRepository;
use SNISTOJ\Utils\Database;
use SNISTOJ\Utils\Logger;

/**
 * Problem Service
 * Handles problem-related business logic
 */
class ProblemService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance('problems');
    }

    /**
     * Get all problems with pagination
     */
    public function getProblems($page = 1, $limit = 10, $difficulty = null, $category = null)
    {
        $offset = ($page - 1) * $limit;
        $query = 'SELECT * FROM problems WHERE 1=1';
        $params = [];

        if ($difficulty) {
            $query .= ' AND difficulty = ?';
            $params[] = $difficulty;
        }

        if ($category) {
            $query .= ' AND category = ?';
            $params[] = $category;
        }

        $query .= ' ORDER BY created_at DESC LIMIT ? OFFSET ?';
        $params[] = $limit;
        $params[] = $offset;

        return $this->db->selectAll($query, $params);
    }

    /**
     * Get problem by ID
     */
    public function getProblem($id)
    {
        $problem = $this->db->selectOne('SELECT * FROM problems WHERE id = ?', [$id]);
        
        if ($problem) {
            $problem['testcases'] = $this->db->selectAll(
                'SELECT * FROM test_cases WHERE problem_id = ? AND is_example = 1',
                [$id]
            );
        }

        return $problem;
    }

    /**
     * Submit solution
     */
    public function submitSolution($problemId, $userId, $code, $language)
    {
        try {
            $submission = [
                'problem_id' => $problemId,
                'user_id' => $userId,
                'code' => $code,
                'language' => $language,
                'status' => 'pending'
            ];

            $result = $this->db->execute(
                'INSERT INTO submissions (problem_id, user_id, code, language, status, created_at) 
                 VALUES (?, ?, ?, ?, ?, NOW())',
                [$problemId, $userId, $code, $language, 'pending']
            );

            Logger::info('Solution submitted', [
                'submission_id' => $result,
                'user_id' => $userId,
                'problem_id' => $problemId
            ]);

            return $result;

        } catch (\Exception $e) {
            Logger::error('Failed to submit solution', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get user submissions for a problem
     */
    public function getUserSubmissions($userId, $problemId = null)
    {
        $query = 'SELECT s.* FROM submissions s WHERE s.user_id = ?';
        $params = [$userId];

        if ($problemId) {
            $query .= ' AND s.problem_id = ?';
            $params[] = $problemId;
        }

        $query .= ' ORDER BY s.created_at DESC';

        return $this->db->selectAll($query, $params);
    }

    /**
     * Get problem statistics
     */
    public function getProblemStats($problemId)
    {
        $stats = $this->db->selectOne(
            'SELECT 
                COUNT(*) as total_submissions,
                SUM(CASE WHEN status = "accepted" THEN 1 ELSE 0 END) as accepted,
                SUM(CASE WHEN status = "wrong_answer" THEN 1 ELSE 0 END) as wrong_answer,
                SUM(CASE WHEN status = "time_limit_exceeded" THEN 1 ELSE 0 END) as timeout,
                COUNT(DISTINCT user_id) as unique_users
             FROM submissions WHERE problem_id = ?',
            [$problemId]
        );

        return $stats;
    }

    /**
     * Create new problem (admin only)
     */
    public function createProblem($title, $description, $difficulty, $category, $timeLimit, $memoryLimit, $userId)
    {
        try {
            $result = $this->db->execute(
                'INSERT INTO problems (title, description, difficulty, category, time_limit, memory_limit, created_by, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, NOW())',
                [$title, $description, $difficulty, $category, $timeLimit, $memoryLimit, $userId]
            );

            Logger::info('Problem created', ['problem_id' => $result, 'user_id' => $userId]);
            return $result;

        } catch (\Exception $e) {
            Logger::error('Failed to create problem', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Add test case to problem
     */
    public function addTestCase($problemId, $input, $expectedOutput, $isExample = false)
    {
        try {
            $result = $this->db->execute(
                'INSERT INTO test_cases (problem_id, input, expected_output, is_example, created_at)
                 VALUES (?, ?, ?, ?, NOW())',
                [$problemId, $input, $expectedOutput, $isExample ? 1 : 0]
            );

            return $result;

        } catch (\Exception $e) {
            Logger::error('Failed to add test case', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get total problem count
     */
    public function getProblemsCount($difficulty = null, $category = null)
    {
        $query = 'SELECT COUNT(*) as count FROM problems WHERE 1=1';
        $params = [];

        if ($difficulty) {
            $query .= ' AND difficulty = ?';
            $params[] = $difficulty;
        }

        if ($category) {
            $query .= ' AND category = ?';
            $params[] = $category;
        }

        $result = $this->db->selectOne($query, $params);
        return $result['count'] ?? 0;
    }
}
