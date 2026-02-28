<?php

// SINGLETON
class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        try {
            $this->pdo = new PDO("mysql:host=127.0.0.1;dbname=team_tasks", "root", "");
            // Set the PDO error mode to exception
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // If connection fails, stop everything and show an error.
            http_response_code(500);
            echo json_encode([
                'error' => 'Database connection failed: ' . $e->getMessage()
            ]);
            exit;
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->pdo;
    }
}
