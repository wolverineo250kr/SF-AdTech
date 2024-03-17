<?php ob_start(); ?>
<!-- Содержимое страницы -->

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <form action="/register" method="post" id="registrationForm">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($data['user']['id']); ?>">

                <div class="mb-3">
                    <label for="username" class="form-label">Username:</label>
                    <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($data['user']['username']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($data['user']['email']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password:</label>
                    <input type="password" id="password" name="password" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Role:</label>
                    <select id="role" name="role" class="form-select">
                        <?php
                        $roles = [
                            1 => 'рекламодатель',
                            2 => 'веб-мастер',
                            3 => 'админ',
                        ];
                        foreach ($roles as $index => $role): ?>
                            <option value="<?php echo $index; ?>" <?php if ($index == $data['user']['role_id']) echo 'selected'; ?>><?php echo $role; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-check mb-3">
                    <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="checkbox" id="is_active" name="is_active" class="form-check-input" <?php echo ($data['user']['is_active'] == 1) ? 'checked' : ''; ?>>
                    <label for="is_active" class="form-check-label">активность</label>
                </div>

                <noscript>
                    <p>JavaScript is disabled. For full functionality of this site it is necessary to enable JavaScript.
                        Here are the <a href="https://www.enable-javascript.com/" target="_blank">
                            instructions how to enable JavaScript in your web browser</a>.</p>
                </noscript>

                <button type="submit" class="btn btn-primary">Register</button>
            </form>
        </div>
        <div class="col-md-6">
            <img src="\img\logo.jpg" class="img-fluid" alt="Logo">
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<small><a href="/login">войти</a></small>
