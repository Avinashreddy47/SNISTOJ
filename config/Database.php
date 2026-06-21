<?php

namespace SNISTOJ\Config;

/**
 * Secure Database Connection Manager
 * Uses prepared statements and proper error handling
 */
class Database
{
    private static $connections = [];
    private $connection;
    private $config;

    public function __construct($type = 'problems')
    {
        $this->config = Config::getDatabase($type);
        $this->connect();
    }

    /**
     * Connect to database with error handling
     */
    private function connect()
    {
        try {
            $this->connection = new \mysqli(
                $this->config['host'],
                $this->config['user'],
                $this->config['password'],
                $this->config['database'],
                $this->config['port']
            );

            if ($this->connection->connect_error) {
                throw new \Exception('Database connection failed: ' . $this->connection->connect_error);
            }

            // Set charset to utf8mb4 to prevent SQL injection via character encoding
            $this->connection->set_charset('utf8mb4');

        } catch (\Exception $e) {
            throw new \Exception('Failed to connect to database: ' . $e->getMessage());
        }
    }

    /**
     * Get singleton connection
     */
    public static function getInstance($type = 'problems')
    {
        if (!isset(self::$connections[$type])) {
            self::$connections[$type] = new self($type);
        }
        return self::$connections[$type];
    }

    /**
     * Get MySQLi connection object
     */
    public function getConnection()
    {
        if (!$this->connection->ping()) {
            $this->connect();
        }
        return $this->connection;
    }

    /**
     * Execute prepared statement safely
     */
    public function execute($sql, $params = [], $types = '')
    {
        try {
            $stmt = $this->connection->prepare($sql);
            
            if ($stmt === false) {
                throw new \Exception('Prepare failed: ' . $this->connection->error);
            }

            if (!empty($params)) {
                if (empty($types)) {
                    // Auto-detect types
                    $types = '';
                    foreach ($params as $param) {
                        if (is_int($param)) {
                            $types .= 'i';
                        } elseif (is_float($param)) {
                            $types .= 'd';
                        } else {
                            $types .= 's';
                        }
                    }
                }

                $stmt->bind_param($types, ...$params);
            }

            if (!$stmt->execute()) {
                throw new \Exception('Execute failed: ' . $stmt->error);
            }

            return $stmt;

        } catch (\Exception $e) {
            if (Config::isDebug()) {
                throw $e;
            } else {
                throw new \Exception('Database query failed');
            }
        }
    }

    /**
     * Select query
     */
    public function select($sql, $params = [])
    {
        $stmt = $this->execute($sql, $params);
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    /**
     * Select single row
     */
    public function selectOne($sql, $params = [])
    {
        $result = $this->select($sql, $params);
        return $result->fetch_assoc();
    }

    /**
     * Select all rows as array
     */
    public function selectAll($sql, $params = [])
    {
        $result = $this->select($sql, $params);
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * Insert/Update/Delete query
     */
    public function execute_query($sql, $params = [])
    {
        $stmt = $this->execute($sql, $params);
        $affected = $this->connection->affected_rows;
        $stmt->close();
        return $affected;
    }

    /**
     * Get last inserted ID
     */
    public function getLastId()
    {
        return $this->connection->insert_id;
    }

    /**
     * Begin transaction
     */
    public function beginTransaction()
    {
        $this->connection->begin_transaction();
    }

    /**
     * Commit transaction
     */
    public function commit()
    {
        $this->connection->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollback()
    {
        $this->connection->rollback();
    }

    /**
     * Close connection
     */
    public function close()
    {
        if ($this->connection) {
            $this->connection->close();
        }
    }

    /**
     * Sanitize string input (backup to prepared statements)
     */
    public function sanitize($input)
    {
        return $this->connection->real_escape_string($input);
    }
}
