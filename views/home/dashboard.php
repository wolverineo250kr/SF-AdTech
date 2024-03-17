<!-- Шаблон страницы -->

<?php ob_start(); ?>

<!-- Содержимое страницы -->

<?php if (isset($_SESSION['user_id'])): ?>
   роль <?php echo $_SESSION['role_id']; ?>
<?php endif; ?>

<?php $title = 'Дашборд'; ?>
<?php $content = ob_get_clean(); ?>
