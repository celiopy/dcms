<?php

class Database {
    private $host;
    private $username;
    private $password;
    private $database;
    private $connection;

    public function __construct($host, $username, $password, $database) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;

        $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->connection = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            die("Could not connect to the database: " . $e->getMessage());
        }
    }

    public function prepare($query) {
        return $this->connection->prepare($query);
    }

    public function execute($stmt) {
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function bindParam($stmt, $param, $value) {
        $stmt->bindParam($param, $value);
    }

    public function getLastInsertId() {
        return $this->connection->lastInsertId();
    }
}
