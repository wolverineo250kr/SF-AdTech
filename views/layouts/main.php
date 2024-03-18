<?php
// main.php
use Assets\Assets;

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
        <p>JavaScript отключен в вашем браузере. Для полной функциональности этого сайта вам нужно включить JavaScript.
            Вот <a href="https://www.enable-javascript.com/" target="_blank">инструкции</a>, как включить JavaScript в
            вашем веб-браузере.</p>
    </noscript>
    <link rel="apple-touch-icon" sizes="180x180" href="/img/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/img/favicons/favicon-16x16.png">
    <link rel="manifest" href="/img/favicons/site.webmanifest">
    <link rel="mask-icon" href="/img/favicons/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
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
