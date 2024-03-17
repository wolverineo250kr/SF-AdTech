<?php
namespace Controllers;
// WebmasterController.php

// Подключение необходимых моделей
use Controllers\MainController;
use Models\AuthModel;
use Models\OfferModel;
use Helpers\UrlHelper;
use Models\WebmasterModel;

class WebmasterController extends MainController
{
    private $authModel;

    public function __construct(AuthModel $authModel)
    {
        $this->authModel = $authModel;
    }

    public function index()
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

        $userId = $this->authModel->getRoleId();

        if ($userId === 1) {
            header("Location: /login");
            exit();
        }

        $controllerName = get_class($this);
        $controllerName = $this->resolveControllerName($controllerName);
        return $this->renderView('/webmaster/index.php', [
            'controllerName' => $controllerName,
            'actionName' => __FUNCTION__
        ]);
    }

    public function offersOfMine()
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

        $userId = $this->authModel->getRoleId();
        if ($userId === 1) {
            header("Location: /login");
            exit();
        }

        $controllerName = get_class($this);
        $controllerName = $this->resolveControllerName($controllerName);
        return $this->renderView('/webmaster/offers-of-mine.php', [
            'controllerName' => $controllerName,
            'actionName' => __FUNCTION__
        ]);
    }

    public function unsubscribe()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->pageNotFound();
        }

        $roleId = $this->authModel->getRoleId();
        if ($roleId === 1) {
            header("Location: /offers");
            exit();
        }

        $offerId = (int)$_GET['id'];

        $userId = $this->authModel->getUserId();

        $offerModel = new OfferModel();
        header('Content-Type: application/json');

        $result = $offerModel->unsubscribeOffer($userId, $offerId);

        if ($result) {
            $this->respondWithJson(['code' => 1, 'message' => 'Подписка отменена']);
            return;
        } else {
            $this->respondWithJson(['code' => 0, 'message' => 'Неизвестная ошибка']);
            return;
        }
    }

    public function subscribe()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->pageNotFound();
        }

        $roleId = $this->authModel->getRoleId();
        if ($roleId === 1) {
            header("Location: /offers");
            exit();
        }

        $offerId = (int)$_GET['id'];

        $userId = $this->authModel->getUserId();

        $offerModel = new OfferModel();
        $isSuscribed = $offerModel->subscribeCheck($userId, $offerId);

        if (!$isSuscribed) {
            $result = $offerModel->subscribeOffer($userId, $offerId);

            if ($result) {
                $this->respondWithJson(['code' => 1, 'message' => 'Подписка оформлена. Сыылка для оффера будет в разделе Мои офферы']);
                return;
            }
        } else {
            $this->respondWithJson(['code' => 0, 'message' => 'Вы уже подписаны на данный оффер']);
            return;
        }

        $this->respondWithJson(['code' => 0, 'message' => 'Неизвестная ошибка']);
        return;
    }

    // Метод контроллера для получения списка офферов с пагинацией
    public function getList()
    {
        $roleId = $this->authModel->getRoleId();

        if ($roleId === 1) {
            header("Location: /offers");
            exit();
        }
        $userId = $this->authModel->getUserId();

        // Получаем значения параметров из запроса, если они заданы
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['perPage']) ? (int)$_GET['perPage'] : 10;

        // Получаем advertiserId из авторизации (например, из сессии)
        session_start();
        // Вычисляем смещение для LIMIT в SQL запросе
        $offset = ($page - 1) * $perPage;

        // Получаем список офферов с пагинацией и опциональным advertiserId
        $offerModel = new OfferModel();
        $offers = $offerModel->getOffersWithPaginationWebmaster($perPage, $offset, $userId);
        $response['data'] = $offers;
        $response['total'] = $offerModel->countOffersWebmaster($userId);
        // Возвращаем список офферов
        // в формате JSON

        $this->respondWithJson($response);
    }

    public function getListSubscribed()
    {
        $roleId = $this->authModel->getRoleId();

        if ($roleId === 1) {
            header("Location: /offers");
            exit();
        }
        $userId = $this->authModel->getUserId();

        // Получаем значения параметров из запроса, если они заданы
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['perPage']) ? (int)$_GET['perPage'] : 10;

        // Получаем advertiserId из авторизации (например, из сессии)
        session_start();
        // Вычисляем смещение для LIMIT в SQL запросе
        $offset = ($page - 1) * $perPage;

        // Получаем список офферов с пагинацией и опциональным advertiserId
        $offerModel = new OfferModel();
        $offers = $offerModel->getOffersWithPaginationWebmasterSubscribed($perPage, $offset, $userId);

        foreach ($offers as $index => $value) {
            $offers[$index]['url'] = UrlHelper::host() . "://" . UrlHelper::base() . "/redirect?id=" . UrlHelper::encryptData($value["offer_relation"] . '--' . $userId);
        }

        $response['data'] = $offers;
        $response['total'] = $offerModel->countOffersWebmasterSubscribed($userId);
        // Возвращаем список офферов
        // в формате JSON

        $this->respondWithJson($response);
    }
}

?>
