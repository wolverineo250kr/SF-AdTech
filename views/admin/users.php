<!-- Шаблон страницы -->

<?php ob_start(); ?>

<!-- Содержимое страницы -->


<?php if (isset($_SESSION['user_id'])): ?>
    <h2>Список рекламных кампаний</h2>
    <br/>
    <table class="table" id="userList"></table>

    <div class="container mt-4">
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center" id="pagination">
                <li class="page-item">
                    <a class="page-link" id="prevPageBtn" href="#" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <li class="page-item">
                    <a class="page-link" id="nextPageBtn" href="#" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

<?php endif; ?>

<?php $title = 'Кабинет рекламолдателя'; ?>
<?php $content = ob_get_clean(); ?>
