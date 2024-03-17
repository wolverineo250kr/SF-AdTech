<?php

namespace Controllers;

use Controllers\MainController;
use Models\AuthModel;
use Models\OfferClickLogModel;
use Models\OfferModel;
use Models\UsersModel;

class AdminController extends MainController
{
    private $authModel;

    public function __construct(AuthModel $authModel)
    {
        $this->authModel = $authModel;
    }

    public function users()
    {
        $isLoggedIn = $this->authModel->checkAuthentication();
        if (!$isLoggedIn) {
            header("Location: /admin/login");
            exit();
        }

        $isUserActive = $this->authModel->checkIsActive();
        if (!$isUserActive) {
            header("Location: /logout");
            exit();
        }

        $roleId = $this->authModel->getRoleId();

        if ($roleId != 3) {
            $this->pageNotFound();
        }

        $controllerName = get_class($this);
        $controllerName = $this->resolveControllerName($controllerName);
        return $this->renderView('/admin/users.php', [
            'controllerName' => $controllerName,
            'actionName' => __FUNCTION__
        ]);
    }

    public function updateUserAsync()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->pageNotFound();
        }

        $roleId = $this->authModel->getRoleId();

        if ((int)$_POST['role'] === 3 && (int)$roleId !== 3) {
            $this->pageNotFound();
        }

        $id = trim($_POST['id']);
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $is_active = trim($_POST['is_active']);
        $password = trim($_POST['password']);
        $roleId = $_POST['role'];

        if (!isset($_POST['_token']) || $_POST['_token'] !== $_SESSION['csrf_token']) {
            $this->pageNotFound();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->pageNotFound('Плохой email');
        }

        // Проведем базовую валидацию (можно использовать дополнительные проверки)
        if (empty($username) || empty($email)) {
            $this->pageNotFound();
        }

        $modelUser = new UsersModel();
        $modelUser->setId($id);
        $modelUser->setUsername($username);
        $modelUser->setRoleId($roleId);
        $modelUser->setEmail($email);
        $modelUser->setStatus($is_active);
        $modelUser->setPassword($password);

        // Регистрируем пользователя с использованием модели
        $result = $modelUser->updateUser();

        if ($result) {
            $this->respondWithJson(['code' => 1, 'message' => 'Аккаунт обновлен']);
        } else {
            $this->respondWithJson(['code' => 0, 'message' => 'Ошибка обновления']);
        }

        return;
    }

    public function userView()
    {
        // Проверяем наличие параметра 'id' в запросе
        if (!isset($_GET['id'])) {
            $this->pageNotFound();
        }

        // Проверяем аутентификацию и активность пользователя
        $this->checkUserStatus();

        // Проверяем роль пользователя
        $roleId = $this->authModel->getRoleId();
        if ($roleId != 3) {
            $this->pageNotFound();
        }

        // Получаем данные пользователя
        $modelUser = new UsersModel();
        $modelUser->setId($_GET['id']);
        $user = $modelUser->getUser();

        // Возвращаем представление с данными пользователя
        $controllerName = strtolower(str_replace('Controller', '', substr(get_class($this), strrpos(get_class($this), '\\') + 1)));
        return $this->renderView('/admin/userView.php', [
            'controllerName' => $controllerName,
            'actionName' => __FUNCTION__,
            'user' => $user
        ]);
    }

    public function getUserList()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Устанавливаем заголовок Content-Type
            header('Content-Type: application/json');

            $model = new UsersModel();

            // Получаем параметры пагинации
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $perPage = isset($_GET['perPage']) ? (int)$_GET['perPage'] : 10;
            $offset = ($page - 1) * $perPage;

            // Устанавливаем параметры пагинации в модели
            $model->setPerPage($perPage);
            $model->setOffset($offset);

            // Получаем список пользователей с пагинацией
            $users = $model->getUsersWithPagination();

            // Формируем JSON-ответ
            $response['data'] = $users;
            $response['total'] = $model->countUsers();

            // Отправляем JSON-ответ
            $this->respondWithJson($response);
        } else {
            $this->pageNotFound();
        }
    }

    public function changeUserStatus()
    {
        if (!isset($_POST['recordId'])) {
            $this->pageNotFound();
        }

        $recordId = (int)$_POST['recordId'];

        $userId = $this->authModel->getUserId();

        if ($recordId === $userId) {
            $this->respondWithJson(['code' => 0, 'message' => 'Это ваш аккаунт']);
            return;
        }

        $model = new UsersModel();
        $model->setId($recordId);
        $response = $model->changeStatus();

        $message = $response ? 'Статус изменен' : 'Ошибка смены статуса';
        $code = $response ? 1 : 0;

        $this->respondWithJson(['code' => $code, 'message' => $message]);
    }

    public function create()
    {
        if (!$this->authModel->checkAuthentication()) {
            header("Location: /admin/login");
            exit();
        }

        if (!$this->authModel->checkIsActive()) {
            header("Location: /logout");
            exit();
        }

        $roleId = (int)$this->authModel->getRoleId();

        if ($roleId !== 3) {
            $this->pageNotFound();
        }

        $controllerName = get_class($this);
        $controllerName = $this->resolveControllerName($controllerName);
        return $this->renderView('/admin/create.php', [
            'controllerName' => $controllerName,
            'actionName' => __FUNCTION__
        ]);
    }

    public function stats()
    {
        // Вызываем метод для проверки статуса пользователя
        $this->checkUserStatus();

        session_start();

        // Если был отправлен GET-запрос
        $controllerName = get_class($this);
        $controllerName = $this->resolveControllerName($controllerName);
        return $this->renderView('/admin/stats.php', [
            'controllerName' => $controllerName,
            'actionName' => __FUNCTION__,
        ]);
    }

    public function getOffersListAdvertiser()
    {
        if (!$this->authModel->checkAuthentication() || $this->authModel->getRoleId() != 3) {
            $this->pageNotFound();
        }

        $model = new OfferModel();
        $result = $model->getOffersAdvertiser();

        $this->respondWithJson($result);
    }

    public function adminGetStat()
    {
        $isLoggedIn = $this->authModel->checkAuthentication();
        if (!$isLoggedIn) {
            $this->pageNotFound();
        }

        $roleId = $this->authModel->getRoleId();
        if ($roleId != 3) {
            $this->pageNotFound();
        }

        // Проверка CSRF-токена
        if (!isset($_POST['_token']) || $_POST['_token'] !== $_SESSION['csrf_token']) {
            $this->pageNotFound();
        }

        $modelLog = new OfferClickLogModel();
        if ($_POST["offerId"]) {
            $modelLog->setOfferId($_POST["offerId"]);
        }
        $modelLog->setPeriod($_POST["period"]);
        $modelLog->setIsAdmin(1);

        $result = $modelLog->getStatsForPeriod();

        $this->respondWithJson($result);
    }

    public function statsRedirects()
    {
        // Вызываем метод для проверки статуса пользователя
        $this->checkUserStatus();

        // Если был отправлен GET-запрос
        $controllerName = get_class($this);
        $controllerName = $this->resolveControllerName($controllerName);
        return $this->renderView('/admin/stats-redirects.php', [
            'controllerName' => $controllerName,
            'actionName' => __FUNCTION__,
        ]);
    }

    public function statsRedirectsAsync()
    {
        $isLoggedIn = $this->authModel->checkAuthentication();
        if (!$isLoggedIn) {
            $this->pageNotFound();
        }

        $roleId = $this->authModel->getRoleId();
        if ($roleId != 3) {
            $this->pageNotFound();
        }

        // Получаем значения параметров из запроса, если они заданы
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['perPage']) ? (int)$_GET['perPage'] : 10;

        // Вычисляем смещение для LIMIT в SQL запросе
        $offset = ($page - 1) * $perPage;

        $modelLog = new OfferClickLogModel();
        $modelLog->setOffset($offset);
        $modelLog->setPerPage($perPage);

        $response['data'] = $modelLog->getAll();
        $response['total'] = $modelLog->countAll();
        $response['active'] = $modelLog->countAllRedirected();

        $this->respondWithJson($response);
    }

    public function statsOffers()
    {
        // Вызываем метод для проверки статуса пользователя
        $this->checkUserStatus();

        // Если был отправлен GET-запрос
        $controllerName = get_class($this);
        $controllerName = $this->resolveControllerName($controllerName);
        return $this->renderView('/admin/stats-offers.php', [
            'controllerName' => $controllerName,
            'actionName' => __FUNCTION__,
        ]);
    }

    public function statsOffersPure()
    {
        $isLoggedIn = $this->authModel->checkAuthentication();
        if (!$isLoggedIn) {
            $this->pageNotFound();
        }

        $roleId = $this->authModel->getRoleId();
        if ($roleId != 3) {
            $this->pageNotFound();
        }

        // Получаем значения параметров из запроса, если они заданы
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['perPage']) ? (int)$_GET['perPage'] : 10;

        // Вычисляем смещение для LIMIT в SQL запросе
        $offset = ($page - 1) * $perPage;

        $offerModel = new OfferModel();
        $offerModel->setOffset($offset);
        $offerModel->setPerPage($perPage);

        $offers = $offerModel->getAll();
        $response['data'] = $offers;
        $response['total'] = $offerModel->countOffers();
        $response['active'] = $offerModel->countOffersActive();

        $this->respondWithJson($response);
    }

    public function checkUserStatus()
    {
        $isLoggedIn = $this->authModel->checkAuthentication();
        if (!$isLoggedIn) {
            header("Location: /login");
            exit();
        }

        $isUserActive = $this->authModel->checkIsActive();
        if (!$isUserActive) {
            header("Location: /logout");
            exit();
        }

        $roleId = $this->authModel->getRoleId();
        if ($roleId != 3) {
            $this->pageNotFound();
        }
    }
}