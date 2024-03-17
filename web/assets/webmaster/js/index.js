// Создание нового объекта XMLHttpRequest
let xhr = new XMLHttpRequest();

// Переменная для хранения текущей страницы
var currentPage = 1;
let perPage = 4;

// Функция для загрузки данных офферов для указанной страницы
function loadOffers(page) {
    // Отправить запрос на сервер с указанием номера страницы
    xhr.open("GET", "/webmaster/get-list?page=" + page + "&perPage=" + perPage, true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let response = JSON.parse(xhr.responseText);
            let offers = response.data;
            let totalItems = response.total;
            currentPage = page;
            renderPageButtons(totalItems, perPage);
            // Обновляем пагинацию


            // Отображаем данные офферов на странице
            document.getElementById("offerList").innerHTML = renderOffers(offers);

            handleSubscribeButtons();
        }
    };
    xhr.send();
}

function renderPageButtons(totalItems, perPage) {
    // Получение первого <li> элемента
    let firstLi = document.querySelector("#pagination > .page-item:first-child");

// Получение последнего <li> элемента
    let lastLi = document.querySelector("#pagination > .page-item:last-child");

// Определите общее количество страниц
    let totalPages = Math.ceil(totalItems / perPage);

// Найдите контейнер для пагинации
    let paginationContainer = document.getElementById("pagination");

// Очистите контейнер перед добавлением новых элементов
    paginationContainer.innerHTML = "";

// Цикл для создания элементов пагинации
    for (let i = 1; i <= totalPages; i++) {
        // Создание элемента li
        let listItem = document.createElement("li");
        listItem.classList.add("page-item");

        // Создание элемента a для ссылки
        let link = document.createElement("a");
        link.classList.add("page-link");
        link.classList.add("one-of");
        link.setAttribute("data-attr-page", i);
        link.setAttribute("href", "#");
        link.textContent = i;

        // Добавление обработчика события для каждой ссылки
        link.addEventListener("click", function (event) {
            event.preventDefault();
            loadOffers(i);
        });

        // Добавление ссылки в элемент li
        listItem.appendChild(link);

        // Если текущая страница, добавьте класс 'active' к элементу li
        if (i == currentPage) {
            link.classList.add("active");
        }

        // Добавление элемента li в контейнер пагинации
        paginationContainer.appendChild(listItem);
    }

// Добавление элементов firstLi и lastLi в контейнер пагинации
    paginationContainer.insertAdjacentElement('afterbegin', firstLi);
    paginationContainer.insertAdjacentElement('beforeend', lastLi);
}

// Функция для отображения данных офферов на странице в виде таблицы
function renderOffers(offers) {
    let html = "<table class='table'>";
    html += "<thead><tr><th scope='col'>ID</th><th scope='col'>Название</th><th scope='col'>Стоимость клика,  ₽</th><th scope='col'>Дата и время</th><th scope='col'>Подисаться</th></tr></thead>";
    html += "<tbody>";
    for (let i = 0; i < offers.length; i++) {
        html += "<tr>";
        html += "<td>" + offers[i].id + "</td>";
        html += "<td>" + offers[i].name + "</td>";
        html += "<td><b>" + offers[i].discounted_cost_per_click + " ₽</b></td>";
        html += "<td>" + offers[i].timestamp + "</td>";
        html += "<td><a class='subscribe' href='/webmaster/subscribe?id=" + offers[i].id + "'>+</a></td>";
        html += "</tr>";
    }
    html += "</tbody></table>";
    return html;
}

let pageLinks = document.querySelectorAll('.one-of');

// Добавляем обработчик события клика для каждого элемента
pageLinks.forEach(function (link) {
    link.addEventListener('click', function (event) {
        event.preventDefault();

        // Удаляем класс "active" у всех элементов с классом "one-of"
        pageLinks.forEach(function (pageLink) {
            pageLink.classList.remove('active');
        });

        // Добавляем класс "active" только элементу, на который был совершен клик
        link.classList.add('active');

        // Получаем номер страницы из атрибута data-attr-page и загружаем соответствующую страницу офферов
        let page = link.getAttribute('data-attr-page');

        loadOffers(page);
    });
});

// Обработчик нажатия на кнопку "Предыдущая страница"
document.getElementById("prevPageBtn").onclick = function () {
    if (currentPage > 1) {
        currentPage--;
        loadOffers(currentPage);
    }
};

// Обработчик нажатия на кнопку "Следующая страница"
document.getElementById("nextPageBtn").onclick = function () {
    currentPage++;
    loadOffers(currentPage);
};

// Загрузка данных офферов для начальной страницы (например, первой страницы)
loadOffers(currentPage);

function handleSubscribeButtons() {
    // Находим все элементы с классом subscribe внутри тега body
    var subscribeLinks = document.body.querySelectorAll('.subscribe');

    // Добавляем обработчик клика для каждой найденной ссылки
    subscribeLinks.forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.preventDefault(); // Отменяем стандартное действие ссылки

            // Отправляем асинхронный запрос на сервер
            var xhr = new XMLHttpRequest();
            xhr.open('POST', link.getAttribute('href'), true);
            xhr.onload = function () {
                if (xhr.status >= 200 && xhr.status < 300) {
                    var response = JSON.parse(xhr.responseText);
                    event.target.remove();
                    loadOffers(currentPage);
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
                // Если произошла ошибка сети
                console.error('Ошибка сети');
            };
            xhr.send(); // Отправляем запрос
        });
    });
}
