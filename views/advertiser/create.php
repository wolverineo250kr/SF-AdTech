<!-- Шаблон страницы -->

<?php ob_start(); ?>

<!-- Содержимое страницы -->

<?php if (isset($_SESSION['user_id'])): ?>
    <form method="POST" id="createOffer">
        <div class="form-group">
            <label for="offerName">Название оффера</label>
            <input type="text" class="form-control" id="offerName" name="offerName" required>
        </div>
        <div class="form-group">
            <label for="costPerClick">Стоимость за клик</label>
            <input type="number" class="form-control" id="costPerClick" name="costPerClick" required>
        </div>
        <div class="form-group">
            <label for="targetUrl">Целевой URL</label>
            <input type="url" class="form-control" id="targetUrl" name="targetUrl" required>
        </div>
        <div class="form-group">
            <label for="themes">Тема сайта</label>
            <input type="text" class="form-control" id="themes" name="themes" required>
        </div>
        <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <button type="submit" class="btn btn-primary">Создать оффер</button>
    </form>

<?php endif; ?>

<?php $title = 'Кабинет рекламодателя'; ?>
<?php $content = ob_get_clean(); ?>
