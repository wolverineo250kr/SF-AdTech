<!-- Шаблон страницы -->

<?php ob_start(); ?>
<!-- Содержимое страницы -->


<!-- registration.php -->

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <form action="/register" method="post" id="registrationForm">
                <div class="form-group">
                    <label for="username" class="form-label">Имя пользователя:<br/><small>латинские буквы, цифры и знак подчеркивания</small></label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="password">Пароль:</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="role">Роль:</label>
                    <select id="role" name="role" class="form-control">
                        <?php
                        $roles = [
                            1 => 'рекламодатель',
                            2 => 'веб-мастер',
                            3 => 'админ',
                        ];
                        ?>
                        <?php foreach ($roles as $index => $role): ?>
                            <option value="<?php echo $index; ?>"><?php echo $role; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <br/>
                <input type="submit" value="Регитсрция" class="btn btn-primary">
            </form>
            <br/>
            <br/>
        </div>
        <div class="col-md-6">
            <img src="\img\logo.jpg" width="300" height="300" class="img-fluid">
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
