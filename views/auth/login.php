<!-- Шаблон страницы -->

<?php ob_start(); ?>

<!-- Содержимое страницы -->
<h1 class="text-center mt-5">SF-AdTech</h1>
<p class="text-center">Трекер трафика</p>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <form action="/process-login" method="post" id="authForm">
                <div class="mb-3">
                    <label for="username" class="form-label">Логин:</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Пароль:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <button type="submit" class="btn btn-primary">Войти</button>
            </form>
            <small><a href="/register">Регистрация</a></small>
        </div>
        <div class="col-md-6">
            <img src="\img\logo.jpg" class="img-fluid" alt="Логотип" width="300" height="300">
        </div>
    </div>
</div>

<?php $title = 'Авторизация'; ?>
<?php $content = ob_get_clean(); ?>