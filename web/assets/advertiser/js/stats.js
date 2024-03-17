document.addEventListener('DOMContentLoaded', function () {
    // Получаем все элементы input с атрибутом name="datetimes"
    var inputs = document.querySelectorAll('input[name="datetimes"]');

    // Итерируем по каждому найденному элементу input
    inputs.forEach(function(input) {
        // Инициализируем daterangepicker для каждого элемента input
        $(input).daterangepicker();

        // Добавляем обработчик события change для каждого элемента input
        input.addEventListener('change', function () {
            console.log(this.value);
        });
    });
});
