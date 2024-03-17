<?php
// Файл: config/routes.php

// Импортируем классы контроллеров
use controllers\HomeController;
use controllers\AdvertiserController;
use controllers\WebmasterController;
use controllers\AuthController;
use controllers\OfferController;
use controllers\RedirectorController;
use controllers\StatsController;
use controllers\AdminController;

// Определение маршрутов с использованием ассоциативного массива
$routes = [
    '/' => [HomeController::class, 'index'], // Главная страница
    '/dashboard' => [HomeController::class, 'dashboard'], // dashboard
    '/logout' => [AuthController::class, 'logout'], // Выход
    '/advertiser' => [AdvertiserController::class, 'index'], // Страница для рекламодателя
    '/webmaster/offers-of-mine' => [WebmasterController::class, 'offersOfMine'], // Страница для веб-мастера
    '/webmaster/offers' => [WebmasterController::class, 'index'], // Страница для веб-мастера
    '/webmaster/get-list' => [WebmasterController::class, 'getList'], // Страница для веб-мастера
    '/webmaster/get-list-subscribed' => [WebmasterController::class, 'getListSubscribed'], // Страница для веб-мастера
    '/webmaster/stats' => [StatsController::class, 'webmaster'], // Статистика для веб-мастера (Посмотреть доходы и кол-во переходов по offer-у в разрезах)
    '/webmaster/get-stats' => [StatsController::class, 'webmasterGetStat'],
    '/webmaster/get-offers-list' => [StatsController::class, 'getOffersListWebmaster'],
    '/advertiser/stats' => [StatsController::class, 'advertiser'], // Статистика для рекламодателя (Посмотреть доходы и кол-во переходов по offer-у в разрезах)
    '/advertiser/get-stats' => [StatsController::class, 'advertiserGetStat'], // Статистика для рекламодателя (Посмотреть доходы и кол-во переходов по offer-у в разрезах)
    '/advertiser/change-offer-status' => [AdvertiserController::class, 'changeOfferStatus'], // Включение / выключение офера
    '/advertiser/get-offers-list' => [StatsController::class, 'getOffersListAdvertiser'], // Статистика для рекламодателя (Посмотреть доходы и кол-во переходов по offer-у в разрезах)
    '/webmaster/unsubscribe' => [WebmasterController::class, 'unsubscribe'], // Страница для веб-мастера
    '/webmaster' => [WebmasterController::class, 'index'], // Страница для веб-мастера
    '/register' => [AuthController::class, 'register'], // Страница регистрации
    '/login' => [AuthController::class, 'login'], // Страница входа
    '/process-login' => [AuthController::class, 'processLogin'], // процесс аторизации
    '/offers' => [AdvertiserController::class, 'index'], // Страница просмотра офферов
    '/offers/create' => [AdvertiserController::class, 'create'], // Страница создания оффера
    '/offers/get-list' => [AdvertiserController::class, 'getList'], // Получение списка оферов
    '/webmaster/subscribe' => [WebmasterController::class, 'subscribe'], // Подписка на оффер
    '/offers/view' => [AdvertiserController::class, 'view'], // Подписка на оффер
    '/offers/stats' => [AdvertiserController::class, 'stats'], // статистика
    '/redirect' => [RedirectorController::class, 'index'], // redirect

    '/admin/create' => [AdminController::class, 'create'], // авторизовывание на работу новых рекламодателей и веб-мастеров;
    '/admin/users-get-list' => [AdminController::class, 'getUserList'], // список пользовтаелей
    '/admin/users' => [AdminController::class, 'users'], // список пользовтаелей
    '/admin/stats' => [AdminController::class, 'stats'], // Контроль общего дохода системы;
    '/admin/change-user-status' => [AdminController::class, 'changeUserStatus'], // Включение / выключение юзера
    '/admin/get-offers-list' => [AdminController::class, 'getOffersListAdvertiser'], // Статистика для админа (Посмотреть доходы и кол-во переходов по offer-у в разрезах)
    '/admin/get-stats' => [AdminController::class, 'adminGetStat'], // Статистика для админа (Посмотреть доходы и кол-во переходов по offer-у в разрезах)
    '/admin/offer-stats' => [AdminController::class, 'statsOffers'], // Статистика по выданным ссылкам;
    '/admin/offer-full' => [AdminController::class, 'statsOffersPure'], // Статистика по выданным ссылкам hxr;
    '/admin/stat-redirects' => [AdminController::class, 'statsRedirects'],
    '/admin/stat-redirects-async' => [AdminController::class, 'statsRedirectsAsync'],
    '/admin/user-view' => [AdminController::class, 'userView'],
    '/admin/update-user-async' => [AdminController::class, 'updateUserAsync'],
];

// Функция для получения обработчика маршрута по его URL
function getHandler($url)
{
    global $routes;
    return isset($routes[$url]) ? $routes[$url] : null;
}

?>