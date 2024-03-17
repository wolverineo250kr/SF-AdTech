document.getElementById('statsForm').addEventListener('submit', function (event) {
    event.preventDefault();
    var formData = new FormData(this);
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/advertiser/get-stats', true);
    xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 300) {
            // result содержит данные
            var result = JSON.parse(xhr.responseText);

            var totalIncome = 0; // Переменная для хранения общей суммы заработка

            // Получаем элемент таблицы, куда будем добавлять данные
            var tableBody = document.getElementById("statsTableBody");

            // Очищаем содержимое таблицы перед добавлением новых данных
            tableBody.innerHTML = "";

            // Проходим по массиву данных и добавляем каждую запись в таблицу
            result.forEach(function (row) {
                // Создаем новую строку в таблице
                var newRow = document.createElement("tr");

                // Добавляем ячейки в строку с данными из объекта row
                var dateCell = document.createElement("td");
                dateCell.textContent = row.date;
                newRow.appendChild(dateCell);

                var clickCountCell = document.createElement("td");
                clickCountCell.textContent = row.click_count;
                newRow.appendChild(clickCountCell);

                var totalIncomeCell = document.createElement("td");
                totalIncomeCell.textContent = row.total_income + ' ₽';
                newRow.appendChild(totalIncomeCell);

                // Добавляем строку с данными в тело таблицы
                tableBody.appendChild(newRow);

                // Прибавляем значение total_income к общей сумме
                totalIncome += parseFloat(row.total_income);
            });

            // Выводим общую сумму на странице
            document.getElementById("totalIncome").innerHTML = "всего потрачено <b>" + totalIncome.toFixed(2) + " ₽</b>";

        } else {
            console.error('Request failed: ' + xhr.statusText);
        }
    };

    xhr.onerror = function () {
        console.error('Network error');
    };
    xhr.send(formData);
});

document.addEventListener("DOMContentLoaded", function () {
    // Функция для загрузки списка офферов с сервера
    function loadOffers() {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "/advertiser/get-offers-list", true);
        xhr.onload = function () {
            if (xhr.status >= 200 && xhr.status < 300) {
                var response = JSON.parse(xhr.responseText);
                populateOfferList(response);
            } else {
                console.error("Failed to load offers: " + xhr.statusText);
            }
        };
        xhr.onerror = function () {
            console.error("Network error occurred");
        };
        xhr.send();
    }

// Функция для заполнения выпадающего списка офферами
    function populateOfferList(offers) {
        var selectElement = document.getElementById("offerId");
        // Очистка текущих опций списка
        selectElement.innerHTML = "";

        // Создание общего промта
        var promptOption = document.createElement("option");
        promptOption.value = ""; // Значение пустое, так как это общий промт
        promptOption.textContent = "Выберите оффер"; // Текст общего промта
        selectElement.appendChild(promptOption);

        // Добавление опций для каждого оффера
        offers.forEach(function (offer) {
            var option = document.createElement("option");
            option.value = offer.id;
            option.textContent = offer.name; // Предполагается, что у оффера есть свойство name
            selectElement.appendChild(option);
        });
    }

    // Загрузка списка офферов при загрузке страницы
    loadOffers();
});