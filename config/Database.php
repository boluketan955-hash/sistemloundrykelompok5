<?php
namespace App\Config;

use PDO;
use PDOException;
use App\Exceptions\DatabaseConnectionException;

class Database {
    private static ?Database $instance = null;
    private PDO $connection;
    private string $host = '127.0.0.1';
    private string $dbName = 'db_laundry_oop';
    private string $username = 'root';
    private string $password = '';

    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset=utf8mb4";
            $this->connection = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            throw new DatabaseConnectionException(
                "Gagal terhubung ke database MySQL: " . $e->getMessage(),
                (string)$e->getCode()
            );
        }
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO {
        return $this->connection;
    }
}
