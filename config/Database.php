<?php

namespace Config;

use Dotenv\Dotenv;
use PDO;

class Database
{
    private $pdo;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->load();

        $db_host = $_ENV['DB_HOST'];
        $db_database = $_ENV['DB_DATABASE'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];

        try {
            $this->pdo = new \PDO("mysql:host=$db_host;dbname=$db_database;charset=utf8mb4", $db_username, $db_password);
            // Устанавливаем режим обработки ошибок PDO на исключения
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Устанавливаем режим выборки данных по умолчанию на ассоциативный массив
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Ошибка подключения к базе данных: " . $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->pdo;
    }
}