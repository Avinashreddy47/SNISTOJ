<?php

namespace SNISTOJ\Repository;

use SNISTOJ\Config\Database;

/**
 * Base Repository
 * Implements common database operations
 */
abstract class BaseRepository implements RepositoryInterface
{
    protected $db;
    protected $table;
    protected $model;

    public function __construct($database = 'problems')
    {
        $this->db = Database::getInstance($database);
    }

    /**
     * Find by ID
     */
    public function find($id)
    {
        return $this->db->selectOne(
            "SELECT * FROM {$this->table} WHERE id = ?",
            [$id]
        );
    }

    /**
     * Find all
     */
    public function all($limit = null)
    {
        $query = "SELECT * FROM {$this->table}";
        if ($limit) {
            $query .= " LIMIT ?";
            return $this->db->selectAll($query, [$limit]);
        }
        return $this->db->selectAll($query);
    }

    /**
     * Find by criteria
     */
    public function where($column, $value)
    {
        return $this->db->selectAll(
            "SELECT * FROM {$this->table} WHERE {$column} = ?",
            [$value]
        );
    }

    /**
     * Create
     */
    public function create($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $values = array_values($data);

        $this->db->execute_query(
            "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})",
            $values
        );

        return $this->db->getLastId();
    }

    /**
     * Update
     */
    public function update($id, $data)
    {
        $updates = [];
        $values = [];

        foreach ($data as $column => $value) {
            $updates[] = "{$column} = ?";
            $values[] = $value;
        }

        $values[] = $id;

        return $this->db->execute_query(
            "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = ?",
            $values
        );
    }

    /**
     * Delete
     */
    public function delete($id)
    {
        return $this->db->execute_query(
            "DELETE FROM {$this->table} WHERE id = ?",
            [$id]
        );
    }

    /**
     * Count
     */
    public function count()
    {
        $result = $this->db->selectOne("SELECT COUNT(*) as count FROM {$this->table}");
        return $result['count'] ?? 0;
    }
}
