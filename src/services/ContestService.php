<?php

namespace SNISTOJ\Services;

use SNISTOJ\Utils\Database;
use SNISTOJ\Utils\Logger;

/**
 * Contest Service
 * Handles contest-related business logic
 */
class ContestService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance('problems');
    }

    /**
     * Get all contests
     */
    public function getContests($status = null)
    {
        $query = 'SELECT * FROM contests WHERE 1=1';
        $params = [];

        if ($status) {
            $query .= ' AND status = ?';
            $params[] = $status;
        }

        $query .= ' ORDER BY start_time DESC';

        return $this->db->selectAll($query, $params);
    }

    /**
     * Get contest by ID
     */
    public function getContest($id)
    {
        $contest = $this->db->selectOne('SELECT * FROM contests WHERE id = ?', [$id]);

        if ($contest) {
            $contest['problems'] = $this->db->selectAll(
                'SELECT p.*, cp.points FROM contest_problems cp
                 JOIN problems p ON cp.problem_id = p.id
                 WHERE cp.contest_id = ?',
                [$id]
            );

            $contest['standings'] = $this->getContestStandings($id);
        }

        return $contest;
    }

    /**
     * Create new contest
     */
    public function createContest($title, $description, $startTime, $endTime, $duration, $userId)
    {
        try {
            $result = $this->db->execute(
                'INSERT INTO contests (title, description, start_time, end_time, duration, status, created_by, created_at)
                 VALUES (?, ?, ?, ?, ?, "scheduled", ?, NOW())',
                [$title, $description, $startTime, $endTime, $duration, $userId]
            );

            Logger::info('Contest created', ['contest_id' => $result, 'user_id' => $userId]);
            return $result;

        } catch (\Exception $e) {
            Logger::error('Failed to create contest', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Add problem to contest
     */
    public function addProblemToContest($contestId, $problemId, $points = 100)
    {
        try {
            $result = $this->db->execute(
                'INSERT INTO contest_problems (contest_id, problem_id, points)
                 VALUES (?, ?, ?)',
                [$contestId, $problemId, $points]
            );

            return $result;

        } catch (\Exception $e) {
            Logger::error('Failed to add problem to contest', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Register user for contest
     */
    public function registerForContest($contestId, $userId)
    {
        try {
            // Check if already registered
            $existing = $this->db->selectOne(
                'SELECT id FROM contest_standings WHERE contest_id = ? AND user_id = ?',
                [$contestId, $userId]
            );

            if ($existing) {
                return $existing['id'];
            }

            // Create standing record
            $result = $this->db->execute(
                'INSERT INTO contest_standings (contest_id, user_id, rank, total_points, solved_problems)
                 VALUES (?, ?, 0, 0, 0)',
                [$contestId, $userId]
            );

            Logger::info('User registered for contest', ['contest_id' => $contestId, 'user_id' => $userId]);
            return $result;

        } catch (\Exception $e) {
            Logger::error('Failed to register for contest', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Submit solution in contest
     */
    public function submitContest($contestId, $problemId, $userId, $code, $language)
    {
        try {
            $result = $this->db->execute(
                'INSERT INTO contest_submissions (contest_id, problem_id, user_id, code, language, status, created_at)
                 VALUES (?, ?, ?, ?, ?, "pending", NOW())',
                [$contestId, $problemId, $userId, $code, $language]
            );

            Logger::info('Contest submission', ['submission_id' => $result, 'user_id' => $userId]);
            return $result;

        } catch (\Exception $e) {
            Logger::error('Failed to submit contest solution', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get contest standings
     */
    public function getContestStandings($contestId)
    {
        return $this->db->selectAll(
            'SELECT cs.*, u.username FROM contest_standings cs
             JOIN users u ON cs.user_id = u.id
             WHERE cs.contest_id = ?
             ORDER BY cs.rank ASC',
            [$contestId]
        );
    }

    /**
     * Update contest status
     */
    public function updateContestStatus($contestId, $status)
    {
        try {
            $this->db->execute(
                'UPDATE contests SET status = ? WHERE id = ?',
                [$status, $contestId]
            );

            Logger::info('Contest status updated', ['contest_id' => $contestId, 'status' => $status]);

        } catch (\Exception $e) {
            Logger::error('Failed to update contest status', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get upcoming contests
     */
    public function getUpcomingContests($limit = 5)
    {
        return $this->db->selectAll(
            'SELECT * FROM contests WHERE status = "scheduled" AND start_time > NOW()
             ORDER BY start_time ASC LIMIT ?',
            [$limit]
        );
    }

    /**
     * Get active contests
     */
    public function getActiveContests()
    {
        return $this->db->selectAll(
            'SELECT * FROM contests WHERE status = "running" AND NOW() BETWEEN start_time AND end_time
             ORDER BY end_time ASC'
        );
    }
}
