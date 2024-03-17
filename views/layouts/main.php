<?php
// main.php

require_once $_SERVER['DOCUMENT_ROOT'] . '/../assets/assets.php';

// Определите текущий контроллер и экшен
$controller = $data["controllerName"]; // Замените на актуальное значение
$action = $data["actionName"]; // Замените на актуальное значение

// Генерируем ссылки на CSS и JS файлы
$cssLink = Assets::generateCssLink($controller, $action);
$jsLink = Assets::generateJsLink($controller, $action);
?>
<!-- Основной макет (layout.php) -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <!-- Внешние стили, скрипты и прочее -->
    <!-- Подключаем общие CSS и JS файлы -->
    <?php Assets::includeCommonCss(); ?>
    <?php Assets::includeCommonJs(); ?>
    <!-- Подключаем CSS файл для текущего контроллера и экшена -->
    <?php echo $cssLink; ?>
    <noscript>
        <p>JavaScript отключен в вашем браузере. Для полной функциональности этого сайта вам нужно включить JavaScript. Вот <a href="https://www.enable-javascript.com/" target="_blank">инструкции</a>, как включить JavaScript в вашем веб-браузере.</p>
    </noscript>
</head>
<body>
<div class="main">
    <div class="container">
        <!-- Header -->
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/../views/layouts/header.php'; ?>

        <!-- Содержимое страницы -->
        <?php echo $content; ?>

        <!-- Footer -->
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/../views/layouts/footer.php'; ?>
    </div>
</div>
<!-- Подключаем JS файл для текущего контроллера и экшена -->
<?php echo $jsLink; ?>
</body>
</html>
