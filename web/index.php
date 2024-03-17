<?php
// index.php

// Подключаем автозагрузчик Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Подключаем файлы с настройками и классами проекта
require_once __DIR__ . '/../config/routes.php';
require_once __DIR__ . '/../config/autoload_classes.php';
require_once __DIR__ . '/../config/database.php';

// Копируем bootstrap.min.css, если он отсутствует
$bootstrapSource = __DIR__ . '/../vendor/twbs/bootstrap/dist/css/bootstrap.min.css';
$bootstrapDestination = __DIR__ . '/assets/common/css/bootstrap.min.css';
if (!file_exists($bootstrapDestination)) {
    try {
        copy($bootstrapSource, $bootstrapDestination);
    } catch (\Exception $e) {
        // Обработка ошибки копирования
    }
}

// Инициализируем сессию, если она не инициализирована
if (!isset($_SESSION)) {
    session_start();
}

// Генерируем CSRF-токен для запросов GET, если он отсутствует
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Получаем запрошенный URL и обрабатываем его
$request_url = $_SERVER['REQUEST_URI'];
$request_url = preg_replace('/\?.*/', '', $request_url); // Удаление строки запроса
$request_url = trim($request_url);

// Проверяем наличие соответствующего маршрута и обрабатываем его
if (array_key_exists($request_url, $routes)) {
    $route = $routes[$request_url];
    $controllerPath = $route[0];
    $methodName = $route[1];

    $authModel = new \Models\AuthModel();
    require_once __DIR__ . '/../' . $controllerPath . '.php';
    $controller = new $controllerPath($authModel);
    $controller->$methodName();
} else {
    // Если маршрут не найден, выводим страницу 404
    http_response_code(404);
    echo 'Страница не найдена';
}
?>
