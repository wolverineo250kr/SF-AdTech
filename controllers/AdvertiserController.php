<?php
// AdvertiserController.php
namespace Controllers;

use Controllers\MainController;
use http\Env\Response;
use Models\AuthModel;
use Models\OfferModel;
use Models\TargetUrlModel;

class AdvertiserController extends MainController
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
        if ($userId === 2) {
            header("Location: /webmaster");
            exit();
        }

        $controllerName = get_class($this);
        $controllerName = $this->resolveControllerName($controllerName);
        return $this->renderView('/advertiser/index.php', [
            'controllerName' => $controllerName,
            'actionName' => __FUNCTION__
        ]);
    }

    // Метод для создания нового оффера рекламодателем
    public function create()
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

        if ($roleId === 2) {
            header("Location: /webmaster");
            exit();
        }

        // Проверяем, был ли отправлен POST-запрос
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Проверка CSRF-токена
            if (!isset($_POST['_token']) || $_POST['_token'] !== $_SESSION['csrf_token']) {
                $this->pageNotFound();
            }

            // Получаем данные из формы создания оффера

            $offer_name = htmlspecialchars($_POST['offerName']);
            $cost_per_click = htmlspecialchars($_POST['costPerClick']);
            $target_url = htmlspecialchars($_POST['targetUrl']);
            $themes = htmlspecialchars($_POST['themes']); // Предполагается, что темы передаются как массив

            // Получаем ID рекламодателя из сессии
            $advertiser_id = $_SESSION['user_id'];

            $urlId = null;
            $urlModel = new TargetUrlModel();
            $urlId = $urlModel->getUrlId($target_url);
            if (!$urlId) {
                $addedUrl = $urlModel->addUrl($target_url);
                $urlId = $addedUrl['url_id'];
            }

            // Создаем экземпляр модели оффера
            $offerModel = new OfferModel();

            // Вызываем метод создания нового оффера и передаем ему данные
            $offer = $offerModel->createOffer($offer_name, $advertiser_id, $cost_per_click, $urlId, $themes);

            $this->respondWithJson(['code' => $offer['status'], 'message' => $offer['message']]);
            return;
        } else {
            // Если был отправлен GET-запрос
            $controllerName = get_class($this);
            $controllerName = $this->resolveControllerName($controllerName);
            return $this->renderView('/advertiser/create.php', [
                'controllerName' => $controllerName,
                'actionName' => __FUNCTION__
            ]);
        }
    }

    public function changeOfferStatus()
    {
        if (!isset($_POST['recordId'])) {
            $this->pageNotFound();
        }

        $offerId = (int)$_POST['recordId'];
        $model = new OfferModel();
        $model->setId($offerId);

        $response = $model->changeStatus();

        if ($response) {
            $this->respondWithJson(['code' => 1, 'message' => 'Статус изменен']);
            die;
        } else {
            $this->respondWithJson(['code' => 0, 'message' => 'Ошбика смены статуса']);
            die;
        }
    }

// Метод для получения списка офферов с учетом пагинации и опционального advertiser_id
    public function getOffersWithPagination($perPage, $offset, $advertiserId = null)
    {
        // Создаем экземпляр подключения к базе данных
        $database = new \Database();
        $pdo = $database->getConnection();

        // Создаем начало SQL запроса
        $sql = "SELECT * FROM sf_offers";

        // Если передан advertiser_id, добавляем его в запрос
        if ($advertiserId !== null) {
            $sql .= " WHERE advertiser_id = :advertiserId";
        }

        // Добавляем LIMIT и OFFSET в запрос
        $sql .= " LIMIT :perPage OFFSET :offset";

        // Подготавливаем запрос
        $stmt = $pdo->prepare($sql);

        // Привязываем параметры к меткам
        $stmt->bindParam(':perPage', $perPage, \PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);

        // Если передан advertiser_id, связываем его с меткой
        if ($advertiserId !== null) {
            $stmt->bindParam(':advertiserId', $advertiserId, \PDO::PARAM_INT);
        }

        // Выполняем запрос
        $stmt->execute();

        // Получаем результат запроса в виде массива ассоциативных массивов (каждый элемент - оффер)
        $offers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Возвращаем список офферов
        return $offers;
    }

    public function view()
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
        if ($userId === 2) {
            header("Location: /webmaster");
            exit();
        }

        session_start();

        // Проверяем, был ли отправлен POST-запрос
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Обработка CSRF-токена
            // Проверка CSRF-токена
            if (!isset($_POST['_token']) || $_POST['_token'] !== $_SESSION['csrf_token']) {
                $this->pageNotFound();
            }

            $offer_id = (int)$_POST['id']; // int
            $offer_name = (string)htmlspecialchars($_POST['offerName']); // str
            $cost_per_click = (int)$_POST['costPerClick']; // int
            $target_url = filter_var((string)$_POST['targetUrl'], FILTER_VALIDATE_URL); // str
            $themes = (string)$_POST['themes']; // str

            // Проведем базовую валидацию (можно использовать дополнительные проверки)
            if (empty($offer_id) || empty($offer_name) || empty($cost_per_click) || empty($target_url) || empty($themes)) {
                // Обработка ошибки валидации

                $this->respondWithJson(['code' => 0, 'message' => 'Ошибка: Не все поля заполнены.']);
                die;
            }

            if ($target_url === false) {
                $this->respondWithJson(['code' => 0, 'message' => 'Ошибка: Неверный формат URL']);
                die;
            }

            $is_active = 0;
            if (isset($_POST['is_active'])) {
                $is_active = 1; // Предполагается, что темы передаются как массив
            }

            // Получаем ID рекламодателя из сессии
            $advertiser_id = $_SESSION['user_id'];

            $urlId = null;
            $urlModel = new TargetUrlModel();
            $urlId = $urlModel->getUrlId($target_url);
            if (!$urlId) {
                $addedUrl = $urlModel->addUrl($target_url);
                $urlId = $addedUrl['url_id'];
            }

            $offerModel = new OfferModel();
            $offer = $offerModel->updateOffer($offer_id, $advertiser_id, $offer_name, $cost_per_click, $urlId, $is_active, $themes);

            return $offer;
        }

        if (!isset($_GET['id'])) {
            $this->pageNotFound();
        }

        $id = (int)$_GET['id'];

        $advertiser_id = null;
        if (isset($_SESSION['user_id'])) {
            $advertiser_id = $_SESSION['user_id'];
        }

        $offerModel = new OfferModel();
        $offer = $offerModel->getOffer($id, $advertiser_id);

        // Если был отправлен GET-запрос
        $controllerName = get_class($this);
        $controllerName = $this->resolveControllerName($controllerName);
        return $this->renderView('/advertiser/view.php', [
            'controllerName' => $controllerName,
            'actionName' => __FUNCTION__,
            'offer' => $offer, // Передаем $offer в представление
        ]);
    }

    // Метод контроллера для получения списка офферов с пагинацией
    public function getList()
    {
        $userId = $this->authModel->getRoleId();
        if ($userId === 2) {
            header("Location: /webmaster");
            exit();
        }

        $isUserActive = $this->authModel->checkIsActive();
        if (!$isUserActive) {
            header("Location: /logout");
            exit();
        }

        // Получаем значения параметров из запроса, если они заданы
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['perPage']) ? (int)$_GET['perPage'] : 10;

        // Получаем advertiserId из авторизации (например, из сессии)
        session_start();
        $advertiserId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

        // Вычисляем смещение для LIMIT в SQL запросе
        $offset = ($page - 1) * $perPage;

        // Получаем список офферов с пагинацией и опциональным advertiserId
        $offerModel = new OfferModel();
        $offers = $offerModel->getOffersWithPagination($perPage, $offset, $advertiserId);
        $response['data'] = $offers;
        $response['total'] = $offerModel->countOffers($advertiserId);

        $this->respondWithJson($response);
    }

    public function stats()
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

        $userId = $this->authModel->getUserId();
        if ($userId === 2) {
            header("Location: /webmaster/stats");
            exit();
        }

        session_start();

        // Если был отправлен GET-запрос
        $controllerName = get_class($this);
        $controllerName = $this->resolveControllerName($controllerName);
        return $this->renderView('/advertiser/stats.php', [
            'controllerName' => $controllerName,
            'actionName' => __FUNCTION__,
        ]);
    }

}

?>
