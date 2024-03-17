<?php

namespace Controllers;

use Controllers\MainController;
use Models\AuthModel;
use Models\OfferClickLogModel;
use Models\OfferModel;

class StatsController extends MainController
{
    private $authModel;

    public function __construct(AuthModel $authModel)
    {
        $this->authModel = $authModel;
    }

    public function webmaster()
    {
        $isLoggedIn = $this->authModel->checkAuthentication();
        if (!$isLoggedIn) {
            header("Location: /login");
            exit();
        }

        $roleId = $this->authModel->getRoleId();

        if ($roleId === 1) {
            header("Location: /stats/advertiser");
            exit();
        }

        $controllerName = get_class($this);
        $controllerName = $this->resolveControllerName($controllerName);
        return $this->renderView('/stats/webmaster.php', [
            'controllerName' => $controllerName,
            'actionName' => __FUNCTION__
        ]);
    }

    public function webmasterGetStat()
    {
        $isLoggedIn = $this->authModel->checkAuthentication();
        if (!$isLoggedIn) {
            $this->pageNotFound();
        }

        if (!isset($_POST['_token']) || $_POST['_token'] !== $_SESSION['csrf_token']) {
            $this->pageNotFound();
        }

        $modelLog = new OfferClickLogModel();
        if ($_POST["offerId"]) {
            $modelLog->setOfferId($_POST["offerId"]);
        }

        $userId = $this->authModel->getUserId();

        $modelLog->setWebmasterId($userId);
        $modelLog->setPeriod($_POST["period"]);
        $result = $modelLog->getStatsForPeriod();

        $this->respondWithJson($result);
    }

    public function getOffersListWebmaster()
    {
        $isLoggedIn = $this->authModel->checkAuthentication();
        if (!$isLoggedIn) {
            $this->pageNotFound();
        }

        $roleId = $this->authModel->getRoleId();
        if ($roleId === 1) {
            $this->pageNotFound();
        }

        $userId = $this->authModel->getUserId();
        $model = new OfferModel();
        $result = $model->getOffersWebmaster($userId);

        $this->respondWithJson($result);
    }

    public function advertiser()
    {
        $isLoggedIn = $this->authModel->checkAuthentication();
        if (!$isLoggedIn) {
            header("Location: /login");
            exit();
        }

        $roleId = $this->authModel->getRoleId();

        if ($roleId === 2) {
            header("Location: /stats/webmaster");
            exit();
        }

        $controllerName = get_class($this);
        $controllerName = $this->resolveControllerName($controllerName);
        return $this->renderView('/stats/advertiser.php', [
            'controllerName' => $controllerName,
            'actionName' => __FUNCTION__
        ]);
    }

    public function advertiserGetStat()
    {
        $isLoggedIn = $this->authModel->checkAuthentication();
        if (!$isLoggedIn) {
            $this->pageNotFound();
        }

        if (!isset($_POST['_token']) || $_POST['_token'] !== $_SESSION['csrf_token']) {
            $this->pageNotFound();
        }

        $modelLog = new OfferClickLogModel();
        if ($_POST["offerId"]) {
            $modelLog->setOfferId($_POST["offerId"]);
        } else {
            $model = new OfferModel();
            $model->setAdvertiserId($this->authModel->getUserId());
            $modelLog->setOfferIds($model->getOffersIds());
        }
        $modelLog->setPeriod($_POST["period"]);

        $result = $modelLog->getStatsForPeriod();

        $this->respondWithJson($result);
    }

    public function getOffersListAdvertiser()
    {
        $isLoggedIn = $this->authModel->checkAuthentication();
        if (!$isLoggedIn) {
            $this->pageNotFound();
        }

        $roleId = $this->authModel->getRoleId();
        if ($roleId === 2) {
            $this->pageNotFound();
        }

        $userId = $this->authModel->getUserId();
        $model = new OfferModel();
        $result = $model->getOffersAdvertiser($userId);

        $this->respondWithJson($result);
    }
}