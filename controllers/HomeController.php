<?php

namespace Controllers;

use Controllers\MainController;
use Models\AuthModel;

class HomeController extends MainController
{
    private $authModel;

    public function __construct(AuthModel $authModel)
    {
        $this->authModel = $authModel;
    }

    public function index()
    {
        // Проверяем, авторизован ли пользователь (здесь должна быть ваша логика авторизации)
        $isLoggedIn = $this->authModel->checkAuthentication();

        // Если пользователь не авторизован, перенаправляем на страницу логина
        if (!$isLoggedIn) {
            header('Location: /login'); // Измените путь, если страница логина находится в другом месте
            exit();
        }

        //  if (isset($_SESSION['user_id'])) {
        header('Location: /dashboard');
        exit();
        //   }

        // Возвращаем данные для вывода
        //     return $this->renderView('home.php');
    }

    public function dashboard()
    {
        $isLoggedIn = $this->authModel->checkAuthentication();
        if (!$isLoggedIn) {
            header("Location: /login");
            exit();
        }

        $roleId = $this->authModel->getRoleId();

        if ($roleId === 1) {
            header('Location: /advertiser');
            exit();
        } elseif ($roleId === 2) {
            header('Location: /webmaster');
            exit();
        } elseif ($roleId === 3) {
            header('Location: /admin/users');
            exit();
        }

        $controllerName = get_class($this);
        $controllerName = $this->resolveControllerName($controllerName);

        return $this->renderView('/home/dashboard.php', [
            'controllerName' => $controllerName,
            'actionName' => __FUNCTION__
        ]);
    }
}