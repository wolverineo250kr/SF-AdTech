<!-- Шаблон страницы -->

<?php ob_start(); ?>

<!-- Содержимое страницы -->

<?php if (isset($_SESSION['user_id'])): ?>
    <h2>Статистика доходов/пехеходов. Веб мастер</h2>
    <br/>

    <div class="container">
        <form id="statsForm">
            <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="offerId">Подписанные оферы:</label>
                    <select class="form-control" id="offerId" name="offerId" required>
                        <!-- Опции для списка офферов будут добавлены сюда -->
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="period">срез за:</label>
                    <select class="form-control" id="period" name="period" required>
                        <option value="day">День</option>
                        <option value="month">Месяц</option>
                        <option value="year">Год</option>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="offerId">&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">Get Stats</button>
                </div>
            </div>
        </form>
        <div class="form-row">
            <div class="col-md-6">
                <span id="totalIncome"></span>
                <br/>
                <br/>
            </div>
        </div>
        <table class="table table-striped">
            <thead class="thead-dark">
            <tr>
                <th>Date</th>
                <th>Click Count</th>
                <th>Total Income</th>
            </tr>
            </thead>
            <tbody id="statsTableBody">

            </tbody>
        </table>

    </div>


<?php endif; ?>

<?php $title = 'Кабинет рекламолдателя'; ?>
<?php $content = ob_get_clean(); ?>
