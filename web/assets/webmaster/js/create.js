document.getElementById('createOffer').addEventListener('submit', function (event) {
    event.preventDefault(); // Отменяем стандартное действие отправки формы

    var formData = new FormData(this); // Создаем объект FormData для сбора данных формы

    var xhr = new XMLHttpRequest(); // Создаем объект XMLHttpRequest для отправки запроса

    xhr.open('POST', '/offers/create', true); // Конфигурируем запрос (метод, адрес, асинхронный)

    xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 300) {
            var response = JSON.parse(xhr.responseText);

            alert(response.message);
        } else {
            if (response.code === 0) {
                alert('Ошибка: Сервер недоступен.');
            } else {
                alert('Ошибка: ' + xhr.statusText);
            }
        }
    };

    xhr.onerror = function () {
        alert(xhr.statusText); // Выводим сообщение об ошибке в консоль
        // Дополнительные действия при ошибке
    };

    xhr.send(formData); // Отправляем запрос с данными формы
});