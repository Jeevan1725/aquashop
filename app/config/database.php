<?php

class Database
{
    private string $host;
    private string $db_name;
    private string $username;
    private string $password;

    public function __construct()
    {
        $configPath = __DIR__ . '/config.local.php';

        if (!file_exists($configPath)) {
            die("Missing app/config/config.local.php — copy the sample and fill credentials.");
        }

        $config = require $configPath;

        $this->host     = $config['db_host'];
        $this->db_name  = $config['db_name'];
        $this->username = $config['db_user'];
        $this->password = $config['db_pass'];
    }

    public function connect(): PDO
    {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";

            $pdo = new PDO($dsn, $this->username, $this->password);

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            return $pdo;

        } catch (PDOException $e) {
            die("Database connection failed.");
        }
    }
}
