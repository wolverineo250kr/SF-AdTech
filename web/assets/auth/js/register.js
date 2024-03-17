document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('registrationForm').addEventListener('submit', function (event) {
        event.preventDefault(); // Отменяем стандартное действие отправки формы

        var formData = new FormData(this); // Создаем объект FormData для сбора данных формы

        var xhr = new XMLHttpRequest(); // Создаем объект XMLHttpRequest для отправки запроса

        xhr.open('POST', '/register', true); // Конфигурируем запрос (метод, адрес, асинхронный)

        xhr.onload = function () {
            if (xhr.status >= 200 && xhr.status < 300) {
                var response = JSON.parse(xhr.responseText);
                alert(response.message);
                window.location.href = '/login';
            } else {
                if (xhr.status === 0) {
                    alert('Ошибка: Сервер недоступен.');
                } else {
                    alert('Ошибка: ' + xhr.statusText);
                }
            }
        };

        xhr.onerror = function () {
            console.error(xhr.statusText); // Выводим сообщение об ошибке в консоль
            // Дополнительные действия при ошибке
        };

        xhr.send(formData); // Отправляем запрос с данными формы
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const usernameInput = document.querySelector('#username');

    usernameInput.addEventListener('input', function(event) {
        const value = event.target.value;
        const filteredValue = value.replace(/[^\w]/gi, ''); // Фильтруем только латинские буквы, цифры и знак подчеркивания
        event.target.value = filteredValue;
    });
});