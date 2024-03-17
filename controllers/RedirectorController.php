<?php

namespace Controllers;

use Controllers\MainController;
use Helpers\UrlHelper;
use Models\OfferClickLogModel;
use Models\OfferToWebmasterModel;

class RedirectorController extends MainController
{
    public function index()
    {
        if (!isset($_GET['id'])) {
            $this->pageNotFound();
        }

        $id = (string)$_GET['id'];

        $ids = UrlHelper::decryptData($id);

        if (!$ids) {
            $this->pageNotFound();
        }

        $model = new OfferToWebmasterModel();
        $model->setId(explode('--', $ids)[0]);
        $model->setWebmasterId(explode('--', $ids)[1]);
        $url = $model->getData();

        $redirected = 1;
        if (!$url['url']) {
            $redirected = 0;
        }

        if ($url['is_active'] == 0) {
            $redirected = 0;
        }

        if ($url['is_active_main'] == 0) {
            $redirected = 0;
        }

        $modelLog = new OfferClickLogModel();
        $modelLog->setOfferId($url["offer_id"]);
        $modelLog->setWebmasterId($url["webmaster_id"]);
        $modelLog->setUrlId($url["url_id"]);
        $modelLog->setPriceTaken($url["cost_per_click"]);
        $modelLog->setIp($_SERVER['REMOTE_ADDR']);
        $modelLog->setRedirected($redirected);
        $modelLog->writeLog();

        if (!$url['url'] || $redirected === 0) {
            $this->pageNotFound();
        }

        header("HTTP/1.1 301 Moved Permanently");
        header("Location: " . $url['url']);
        exit();
    }
}