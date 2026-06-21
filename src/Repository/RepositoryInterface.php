<?php

namespace SNISTOJ\Repository;

/**
 * Repository Interface
 * Defines common CRUD operations
 */
interface RepositoryInterface
{
    /**
     * Find by ID
     */
    public function find($id);

    /**
     * Find all
     */
    public function all($limit = null);

    /**
     * Find by criteria
     */
    public function where($column, $value);

    /**
     * Create
     */
    public function create($data);

    /**
     * Update
     */
    public function update($id, $data);

    /**
     * Delete
     */
    public function delete($id);

    /**
     * Count
     */
    public function count();
}
