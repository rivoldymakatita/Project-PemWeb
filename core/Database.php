<?php

namespace Core;

class Database
{
    private static $instance;
    private $connection;

    private function __construct()
    {
        $config = require __DIR__ . '/../config/database.php';
        
        try {
            $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
            $this->connection = new \PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $config['options']
            );
        } catch (\PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    /**
     * Get singleton instance
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->connection;
    }

    /**
     * Prevent cloning
     */
    private function __clone() {}
}