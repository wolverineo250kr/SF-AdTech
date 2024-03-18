<?php
// AuthModel.php
namespace Models;

use Config\Database;

class AuthModel
{
    private $response = [
        'status' => 0,
        'message' => 'Неизвесная ошибка'
    ];

    public function registerUser($username, $role_id, $email, $password)
    {
        $database = new Database();
        $pdo = $database->getConnection();

        // Подготовленный запрос для вставки нового пользователя
        $stmt = $pdo->prepare("INSERT INTO sf_users (username, role_id, email, password) VALUES (:username, :role_id, :email,:password)");

        try {
            // Выполнение запроса с использованием подготовленного выражения и передачей параметров
            $result = $stmt->execute([
                'username' => $username,
                'email' => $email,
                'role_id' => $role_id,
                'password' => password_hash($password, PASSWORD_DEFAULT) // Хеширование пароля
            ]);

            if ($result) {
                $this->response['status'] = 1;
                $this->response['message'] = 'Пользователь успешно зарегистрирован.';

                return $this->response;
            }
        } catch (\Exception $e) {
            $this->response['status'] = 0;
            // Проверка, возникла ли ошибка из-за дублирования имени пользователя
            if ($e->getCode() === '23000' && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $this->response['message'] = 'Имя пользователя или email уже занято.';
                return $this->response;
            }

            $this->response['message'] = $e->getMessage();

            return $this->response;
        }

        return $this->response;
    }

// В файле AuthModel.php

    public function authenticateUser($username, $password)
    {
        // Создаем экземпляр подключения к базе данных
        $database = new Database();
        $pdo = $database->getConnection();

        // Подготовленный запрос для выборки данных пользователя по имени пользователя
        $stmt = $pdo->prepare("
        SELECT sf_users.*, sf_roles.name as role_name  
        FROM sf_users
        JOIN sf_roles ON sf_users.role_id = sf_roles.id
        WHERE sf_users.username = :username
        AND sf_users.is_active = 1
        ");

        // Выполняем запрос с передачей параметра имени пользователя
        $stmt->execute(['username' => $username]);

        // Получаем результат запроса в виде ассоциативного массива
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Проверяем, найден ли пользователь и совпадает ли его пароль
        if ($user && password_verify($password, $user['password'])) {
            // Пользователь аутентифицирован успешно, возвращаем данные пользователя
            return $user;
        } else {
            // Неверные учетные данные, возвращаем false
            return false;
        }
    }

    public function getUserId(): int
    {
        session_start();

        if (isset($_SESSION['user_id'])) {
            return (int)$_SESSION['user_id'];
        }

        return 0;
    }

    public function getRoleId(): int
    {
        session_start();

        if (isset($_SESSION['role_id'])) {
            return (int)$_SESSION['role_id'];
        }

        return 0;
    }

    public function checkAuthentication()
    {
        session_start();

        if (isset($_SESSION['user_id'])) {
            return true;
        }

        return false;
    }

    public function checkIsActive()
    {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        $database = new Database();
        $pdo = $database->getConnection();

        $stmt = $pdo->prepare("
        SELECT * 
        FROM sf_users 
        WHERE id = :userId AND is_active = 1
        ");

        $stmt->bindParam(':userId', $_SESSION['user_id'], \PDO::PARAM_INT);
        $stmt->execute();
        $activeUser = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$activeUser) {
            return false;
        } else {
            return true;
        }
    }
} 
