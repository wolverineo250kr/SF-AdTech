<?php
use Helpers\UrlHelper;

// Проверка наличия идентификатора пользователя в сессии
$isUserLoggedIn = isset($_SESSION['user_id']);
?>

<!--  макет (header.php) -->

<header class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3 mb-4 border-bottom">
    <div class="col-md-4 mb-2 mb-md-0">
        <a href="/" class="d-inline-flex link-body-emphasis text-decoration-none">
            <img src="\img\logo.jpg" width="32" height="32">
        </a>

        <?php if ($isUserLoggedIn): ?>
            <small>Вы (<?= $_SESSION['username'] ?>) <?= $_SESSION['role_name'] ?></small>
        <?php endif; ?>
    </div>

    <ul class="nav col-12 col-md-auto mb-2 justify-content-center mb-md-0">
        <?php if ($isUserLoggedIn): ?>
            <?php if ((int)$_SESSION['role_id'] === 1): ?>
                <li><a href="/offers" class="<?= UrlHelper::getUrlRoute() === '/offers' ? 'active' : '' ?> nav-link px-2 link-secondary">Офферы</a></li>
                <li><a href="/offers/create" class="<?= UrlHelper::getUrlRoute() === '/offers/create' ? 'active' : '' ?> nav-link px-2 link-secondary">Создать оффер</a></li>
                <li><a href="/advertiser/stats" class="<?= UrlHelper::getUrlRoute() === '/advertiser/stats' ? 'active' : '' ?> nav-link px-2 link-secondary">Статистика</a></li>
            <?php elseif ((int)$_SESSION['role_id'] === 2): ?>
                <li><a href="/webmaster/offers-of-mine" class="<?= UrlHelper::getUrlRoute() === '/webmaster/offers-of-mine' ? 'active' : '' ?> nav-link px-2 link-secondary">Мои офферы</a></li>
                <li><a href="/webmaster/offers" class="<?= UrlHelper::getUrlRoute() === '/webmaster/offers' ? 'active' : '' ?> nav-link px-2 link-secondary">Доступные офферы</a></li>
                <li><a href="/webmaster/stats" class="<?= UrlHelper::getUrlRoute() === '/webmaster/stats' ? 'active' : '' ?> nav-link px-2 link-secondary">Статистика</a></li>
            <?php elseif ((int)$_SESSION['role_id'] === 3): ?>
                <li><a href="/admin/users" class="<?= UrlHelper::getUrlRoute() === '/admin/users' ? 'active' : '' ?> nav-link px-2 link-secondary">Пользователи</a></li>
                <li><a href="/admin/create" class="<?= UrlHelper::getUrlRoute() === '/admin/create' ? 'active' : '' ?> nav-link px-2 link-secondary">Создать</a></li>
                <li><a href="/admin/stats" class="<?= UrlHelper::getUrlRoute() === '/admin/stats' ? 'active' : '' ?> nav-link px-2 link-secondary">Доход</a></li>
                <li><a href="/admin/offer-stats" class="<?= UrlHelper::getUrlRoute() === '/admin/offer-stats' ? 'active' : '' ?> nav-link px-2 link-secondary">Выданные ссылки</a></li>
                <li><a href="/admin/stat-redirects" class="<?= UrlHelper::getUrlRoute() === '/admin/stat-redirects' ? 'active' : '' ?> nav-link px-2 link-secondary">Переходы</a></li>
            <?php endif; ?>
        <?php endif; ?>
    </ul>

    <div class="col-md-2 text-right">
        <?php if ($isUserLoggedIn): ?>
            <a class="btn btn-primary" href="/logout">Выйти</a>
        <?php else: ?>
            <a class="btn btn-outline-primary me-2" href="/login">Login</a>
        <?php endif; ?>
    </div>
</header>
