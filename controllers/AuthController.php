<?php

namespace Controllers;

use Controllers\MainController;
use Models\AuthModel;

class AuthController extends MainController
{
    private $authModel;

    public function __construct(AuthModel $authModel)
    {
        $this->authModel = $authModel;
    }

    public function login()
    {
        session_start();

        if (isset($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit();
        }

        $controllerName = get_class($this);
        $controllerName = $this->resolveControllerName($controllerName);
        return $this->renderView('/auth/login.php', [
            'controllerName' => $controllerName,
            'actionName' => __FUNCTION__
        ]);
    }

    public function register()
    {
        session_start();
        // Проверяем, был ли отправлен POST-запрос
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $roleId = $this->authModel->getRoleId();

            if ((int)$_POST['role'] === 3 && (int)$roleId !== 3) {
                $this->pageNotFound();
            }

            // Валидация и сохранение данных пользователя
            $username = trim(htmlspecialchars($_POST['username']));
            $email = trim(htmlspecialchars($_POST['email']));
            $password = trim(htmlspecialchars($_POST['password']));
            $roleId = $_POST['role'];

            // Обработка CSRF-токена
            // Проверка CSRF-токена
            if (!isset($_POST['_token']) || $_POST['_token'] !== $_SESSION['csrf_token']) {
                // Обработка ошибки CSRF
                // Если маршрут не найден, выводим страницу 404
                $this->pageNotFound();
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->pageNotFound("Адрес электронной почты неверный.");
            }

            // Проведем базовую валидацию (можно использовать дополнительные проверки)
            if (empty($username) || empty($email) || empty($password)) {
                // Обработка ошибки валидации
                $this->pageNotFound("Ошибка: Не все поля заполнены.");
            }

            // Регистрируем пользователя с использованием модели
            $result = $this->authModel->registerUser($username, $roleId, $email, $password);

            $this->respondWithJson($result);
        } else {
            // Если был отправлен GET-запрос, отображаем страницу регистрации
            $controllerName = get_class($this);
            $controllerName = $this->resolveControllerName($controllerName);
            return $this->renderView('/auth/register.php', [
                'controllerName' => $controllerName,
                'actionName' => __FUNCTION__
            ]);
        }
    }

    public function processLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            session_start();
            if (!isset($_POST['_token']) || $_POST['_token'] !== $_SESSION['csrf_token']) {
                $this->pageNotFound();
            }

            // Получаем данные из формы входа
            $username = trim(htmlspecialchars($_POST['username']));
            $password = trim(htmlspecialchars($_POST['password']));
            // Проверяем аутентификацию пользователя с использованием модели
            $authenticatedUser = $this->authModel->authenticateUser($username, $password);

            if ($authenticatedUser) {
                // Пользователь аутентифицирован успешно, сохраняем данные в сессии и перенаправляем на главную страницу
                $_SESSION['user_id'] = (int)$authenticatedUser['id'];
                $_SESSION['username'] = htmlspecialchars($authenticatedUser['username']);
                $_SESSION['role_id'] = (int)$authenticatedUser['role_id'];
                $_SESSION['role_name'] = htmlspecialchars($authenticatedUser['role_name']);
                // Другие данные пользователя, которые сохранить в сессии
                $this->respondWithJson(['code' => 1, 'message' => 'Заходите']);
                exit();
            } else {
                $error = 'Неверное имя пользователя или пароль.';
                $this->respondWithJson(['code' => 0, 'message' => $error]);
            }
        } else {
            // Если был отправлен GET-запрос, перенаправляем на страницу входа
            header('Location: /login');
            exit();
        }
    }

    public function logout()
    {
        // Начинаем сеанс для доступа к сессионным данным
        session_start();

        // Очищаем все сессионные переменные
        $_SESSION = [];

        // Удаляем куки, связанные с сеансом
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Уничтожаем сеанс
        session_destroy();

        // Перенаправляем пользователя на страницу входа или на другую страницу
        header("Location: /login");
        exit();
    }
}
