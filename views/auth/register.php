<!-- Шаблон страницы -->

<?php ob_start(); ?>

<!-- Содержимое страницы -->

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <form action="/register" method="post" id="registrationForm">
                <div class="mb-3">
                    <label for="username" class="form-label">Имя пользователя:<br/><small>латинские буквы, цифры и знак подчеркивания</small></label>

                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Пароль:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Роль:</label>
                    <select id="role" name="role" class="form-control">
                        <?php
                        $roles = [
                            1 => 'рекламодатель',
                            2 => 'веб-мастер',
                        ];
                        ?>
                        <?php foreach ($roles as $index => $role): ?>
                            <option value="<?php echo $index; ?>"><?php echo $role; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
            </form>
            <small><a href="/login">Войти</a></small>
        </div>
        <div class="col-md-6">
            <img src="\img\logo.jpg" class="img-fluid" alt="Логотип">
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
