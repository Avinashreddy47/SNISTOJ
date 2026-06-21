<?php

namespace SNISTOJ\Repository;

/**
 * User Repository
 * Handles all user-related database operations
 */
class UserRepository extends BaseRepository
{
    protected $table = 'users';
    protected $database = 'users';

    public function __construct()
    {
        parent::__construct($this->database);
    }

    /**
     * Find by username
     */
    public function findByUsername($username)
    {
        return $this->db->selectOne(
            "SELECT * FROM {$this->table} WHERE username = ?",
            [$username]
        );
    }

    /**
     * Find by email
     */
    public function findByEmail($email)
    {
        return $this->db->selectOne(
            "SELECT * FROM {$this->table} WHERE email = ?",
            [$email]
        );
    }

    /**
     * Get user by role
     */
    public function getByRole($role)
    {
        return $this->db->selectAll(
            "SELECT * FROM {$this->table} WHERE role = ?",
            [$role]
        );
    }

    /**
     * Check if username exists
     */
    public function usernameExists($username)
    {
        return $this->findByUsername($username) !== null;
    }

    /**
     * Check if email exists
     */
    public function emailExists($email)
    {
        return $this->findByEmail($email) !== null;
    }
}
