<?php

namespace SNISTOJ\Services;

use SNISTOJ\Config\Database;
use SNISTOJ\Utils\Security;
use SNISTOJ\Utils\Logger;

/**
 * User Service
 * Handles user business logic
 */
class UserService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance('users');
    }

    /**
     * Create new user
     * 
     * @param array $data User data (username, email, password)
     * @return array Created user
     * @throws \Exception If user already exists or validation fails
     */
    public function createUser($data)
    {
        // Check if username already exists
        $existing = $this->db->selectOne(
            'SELECT id FROM users WHERE username = ?',
            [$data['username']]
        );

        if ($existing) {
            throw new \Exception('Username already exists');
        }

        // Check if email already exists
        $existing = $this->db->selectOne(
            'SELECT id FROM users WHERE email = ?',
            [$data['email']]
        );

        if ($existing) {
            throw new \Exception('Email already exists');
        }

        // Hash password
        $passwordHash = Security::hashPassword($data['password']);

        // Insert user
        $this->db->execute_query(
            'INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())',
            [$data['username'], $data['email'], $passwordHash]
        );

        $userId = $this->db->getLastId();

        Logger::info('User created', ['user_id' => $userId, 'username' => $data['username']]);

        return $this->getUserById($userId);
    }

    /**
     * Authenticate user
     * 
     * @param string $username Username
     * @param string $password Password
     * @return array|false User data or false if authentication fails
     */
    public function authenticate($username, $password)
    {
        $user = $this->db->selectOne(
            'SELECT id, username, email, password FROM users WHERE username = ?',
            [$username]
        );

        if (!$user) {
            return false;
        }

        // Verify password
        if (!Security::verifyPassword($password, $user['password'])) {
            return false;
        }

        // Check if password needs rehashing
        if (Security::needsRehash($user['password'])) {
            $newHash = Security::hashPassword($password);
            $this->db->execute_query(
                'UPDATE users SET password = ? WHERE id = ?',
                [$newHash, $user['id']]
            );
        }

        // Unset password from returned data
        unset($user['password']);

        return $user;
    }

    /**
     * Get user by ID
     * 
     * @param int $userId User ID
     * @return array|null User data or null
     */
    public function getUserById($userId)
    {
        return $this->db->selectOne(
            'SELECT id, username, email, created_at FROM users WHERE id = ?',
            [$userId]
        );
    }

    /**
     * Get user by username
     * 
     * @param string $username Username
     * @return array|null User data or null
     */
    public function getUserByUsername($username)
    {
        return $this->db->selectOne(
            'SELECT id, username, email, created_at FROM users WHERE username = ?',
            [$username]
        );
    }

    /**
     * Update user profile
     * 
     * @param int $userId User ID
     * @param array $data Data to update
     * @return bool Success status
     */
    public function updateUser($userId, $data)
    {
        $updates = [];
        $params = [];

        if (isset($data['email'])) {
            $updates[] = 'email = ?';
            $params[] = $data['email'];
        }

        if (isset($data['full_name'])) {
            $updates[] = 'full_name = ?';
            $params[] = $data['full_name'];
        }

        if (empty($updates)) {
            return false;
        }

        $updates[] = 'updated_at = NOW()';
        $params[] = $userId;

        $sql = 'UPDATE users SET ' . implode(', ', $updates) . ' WHERE id = ?';

        $this->db->execute_query($sql, $params);

        Logger::info('User updated', ['user_id' => $userId]);

        return true;
    }

    /**
     * Delete user
     * 
     * @param int $userId User ID
     * @return bool Success status
     */
    public function deleteUser($userId)
    {
        $this->db->execute_query('DELETE FROM users WHERE id = ?', [$userId]);

        Logger::info('User deleted', ['user_id' => $userId]);

        return true;
    }

    /**
     * Get user submissions
     * 
     * @param int $userId User ID
     * @param int $limit Results limit
     * @return array Submissions
     */
    public function getUserSubmissions($userId, $limit = 50)
    {
        return $this->db->selectAll(
            'SELECT * FROM submissions WHERE user_id = ? ORDER BY created_at DESC LIMIT ?',
            [$userId, $limit]
        );
    }

    /**
     * Get user statistics
     * 
     * @param int $userId User ID
     * @return array Statistics
     */
    public function getUserStats($userId)
    {
        return $this->db->selectOne(
            'SELECT 
                COUNT(*) as total_submissions,
                SUM(CASE WHEN status = "accepted" THEN 1 ELSE 0 END) as accepted,
                SUM(CASE WHEN status = "wrong_answer" THEN 1 ELSE 0 END) as wrong_answer,
                SUM(CASE WHEN status = "runtime_error" THEN 1 ELSE 0 END) as runtime_error
             FROM submissions WHERE user_id = ?',
            [$userId]
        );
    }
}
