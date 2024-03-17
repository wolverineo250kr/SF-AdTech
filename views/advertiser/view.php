<!-- Шаблон страницы -->

<?php ob_start(); ?>

<!-- Содержимое страницы -->
<h2>Изменение офера</h2>
<br/>
<?php if (isset($_SESSION['user_id'])): ?>
    <form method="POST" id="changeOffer">
        <div class="hidden">
            <label for="offerName">id</label>
            <input type="text" class="form-control" id="id" value="<?php echo $data["offer"]["id"] ?>"
                   name="id" required>
        </div>
        <div class="form-group">
            <label for="offerName">Offer Name</label>
            <input type="text" class="form-control" id="offerName" value="<?php echo $data["offer"]["name"] ?>"
                   name="offerName" required>
        </div>
        <div class="form-group">
            <label for="costPerClick">Cost Per Click</label>
            <input type="number" class="form-control" id="costPerClick"
                   value="<?php echo $data["offer"]["cost_per_click"] ?>" name="costPerClick" required>
        </div>
        <div class="form-group">
            <label for="targetUrl">Target URL</label>
            <input type="url" class="form-control" id="targetUrl" value="<?php echo $data["offer"]["url"] ?>"
                   name="targetUrl" required>
        </div>
        <div class="form-group">
            <label for="themes">Themes</label>
            <input type="text" class="form-control" id="themes" name="themes"
                   value="<?php echo $data["offer"]["theme"] ?>" required>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="<?php echo $data["offer"]['is_active'] ?>"
                   id="isActiveCheckbox" name="is_active" <?php echo $data["offer"]['is_active'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="isActiveCheckbox">
                активность
            </label>
        </div>
        <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <button type="submit" class="btn btn-primary">Create Offer</button>
    </form>

<?php endif; ?>

<?php $title = 'Редактирование оффера'; ?>
<?php $content = ob_get_clean(); ?>
