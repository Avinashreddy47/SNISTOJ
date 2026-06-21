<?php

namespace SNISTOJ\Models;

use SNISTOJ\Config\Database;

/**
 * User Model
 * Represents a user entity with database operations
 */
class User
{
    public $id;
    public $username;
    public $email;
    public $password;
    public $full_name;
    public $role;
    public $created_at;
    public $updated_at;

    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance('users');
    }

    /**
     * Find user by ID
     * 
     * @param int $id User ID
     * @return self|null User instance or null
     */
    public static function find($id)
    {
        $db = Database::getInstance('users');
        $data = $db->selectOne('SELECT * FROM users WHERE id = ?', [$id]);

        if (!$data) {
            return null;
        }

        $user = new self();
        $user->setAttributes($data);
        return $user;
    }

    /**
     * Find user by username
     * 
     * @param string $username Username
     * @return self|null User instance or null
     */
    public static function findByUsername($username)
    {
        $db = Database::getInstance('users');
        $data = $db->selectOne('SELECT * FROM users WHERE username = ?', [$username]);

        if (!$data) {
            return null;
        }

        $user = new self();
        $user->setAttributes($data);
        return $user;
    }

    /**
     * Get all users
     * 
     * @param int $limit Results limit
     * @return array User instances
     */
    public static function all($limit = 100)
    {
        $db = Database::getInstance('users');
        $results = $db->selectAll('SELECT * FROM users LIMIT ?', [$limit]);

        $users = [];
        foreach ($results as $data) {
            $user = new self();
            $user->setAttributes($data);
            $users[] = $user;
        }

        return $users;
    }

    /**
     * Set attributes from array
     * 
     * @param array $attributes Attributes
     */
    private function setAttributes($attributes)
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Save user to database
     * 
     * @return bool Success status
     */
    public function save()
    {
        if ($this->id) {
            return $this->update();
        }

        $this->created_at = date('Y-m-d H:i:s');

        $this->db->execute_query(
            'INSERT INTO users (username, email, password, full_name, role, created_at) 
             VALUES (?, ?, ?, ?, ?, ?)',
            [$this->username, $this->email, $this->password, $this->full_name, $this->role, $this->created_at]
        );

        $this->id = $this->db->getLastId();
        return true;
    }

    /**
     * Update user in database
     * 
     * @return bool Success status
     */
    private function update()
    {
        $this->updated_at = date('Y-m-d H:i:s');

        $this->db->execute_query(
            'UPDATE users SET username = ?, email = ?, password = ?, full_name = ?, role = ?, updated_at = ? 
             WHERE id = ?',
            [$this->username, $this->email, $this->password, $this->full_name, $this->role, $this->updated_at, $this->id]
        );

        return true;
    }

    /**
     * Delete user from database
     * 
     * @return bool Success status
     */
    public function delete()
    {
        $this->db->execute_query('DELETE FROM users WHERE id = ?', [$this->id]);
        return true;
    }

    /**
     * Convert to array
     * 
     * @return array User data
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'full_name' => $this->full_name,
            'role' => $this->role,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Check if user has role
     * 
     * @param string $role Role name
     * @return bool Has role
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Check if user is admin
     * 
     * @return bool Is admin
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }
}
