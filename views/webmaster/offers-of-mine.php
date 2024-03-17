<?php ob_start(); ?>

<!-- Содержимое страницы -->
<?php $title = 'Мои подписки'; ?>
<?php if (isset($_SESSION['user_id'])): ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col">
                <h2><?php echo $title; ?></h2>
                <br/>
                <table class="table" id="offerList"></table>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col">
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
        </div>
    </div>
<?php endif; ?>
<?php $content = ob_get_clean(); ?>
