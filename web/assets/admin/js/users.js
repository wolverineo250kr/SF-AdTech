// Создание нового объекта XMLHttpRequest
let xhr = new XMLHttpRequest();

// Переменная для хранения текущей страницы
var currentPage = 1;
let perPage = 4;

// Функция для загрузки данных офферов для указанной страницы
function loadOffers(page) {
    // Отправить запрос на сервер с указанием номера страницы
    xhr.open("POST", "/admin/users-get-list?page=" + page + "&perPage=" + perPage, true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let response = JSON.parse(xhr.responseText);
            let users = response.data;
            let totalItems = response.total;
            currentPage = page;
            renderPageButtons(totalItems, perPage);
            // Обновляем пагинацию


            // Отображаем данные офферов на странице
            document.getElementById("userList").innerHTML = renderUsers(users);

            handleToggles();
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
function renderUsers(users) {
    let html = "<table class='table'>";
    html += "<thead><tr><th scope='col'>ID</th><th scope='col'>логин</th><th scope='col' >активность</th><th scope='col'>роль</th><th scope='col' >email</th><th scope='col'>Дата и время</th><th scope='col'>&nbsp;</th></tr></thead>";
    html += "<tbody>";
    for (let i = 0; i < users.length; i++) {
        html += "<tr>";
        html += "<td>" + users[i].id + "</td>";
        html += "<td>" + users[i].username + "</td>";
        html += "<td><label class=\"toggle-switch\">\n" +
            "  <input data-record-id=" + users[i].id + " type=\"checkbox\" " + (users[i].is_active === '1' ? 'checked' : '') + " class=\"toggle-input\">\n" +
            "  <span class=\"toggle-slider\"></span>\n" +
            "</label></td>";
        html += "<td>" + users[i].role_name + "</td>";
        html += "<td>" + users[i].email + "</td>";
        html += "<td>" + users[i].timestamp + "</td>";
        html += "<td><a href='/admin/user-view?id=" + users[i].id + "'>✎</a></td>";
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

function handleToggles() {
    // Получаем все элементы .toggle-input
    const toggleInputs = document.querySelectorAll('.toggle-input');

    // Для каждого элемента .toggle-input добавляем обработчик события change
    toggleInputs.forEach(function (toggleInput) {
        toggleInput.addEventListener('change', function () {
            // Получаем значение атрибута data-record-id для текущего элемента
            const recordId = this.getAttribute('data-record-id');
            updateRecordStatus(recordId);

            // if (this.checked) {
            //     console.log('Переключатель включен для записи с ID:', recordId);
            //     // Ваш код, выполняемый при включении переключателя
            // } else {
            //     console.log('Переключатель выключен для записи с ID:', recordId);
            //     // Ваш код, выполняемый при выключении переключателя
            // }
        });
    });
}

// Флаг, указывающий, отправлен ли уже запрос
let isRequestSent = false;

function updateRecordStatus(recordId) {
    if (!isRequestSent) {
        // Устанавливаем флаг, что запрос отправлен
        isRequestSent = true;

        // Создаем объект FormData для передачи данных
        const formData = new FormData();
        formData.append('recordId', recordId);

        // Выполняем запрос на сервер
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/admin/change-user-status');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    // Обработка успешного ответа от сервера
                    let response = JSON.parse(xhr.responseText);
                    if (response.code == '0') {
                        alert(response.message);
                    }
                } else {
                    // Обработка ошибки
                    console.error('Ошибка при обновлении статуса записи:', xhr.status);
                }

                // Сбрасываем флаг после завершения запроса
                isRequestSent = false;
            }
        };

        // Отправляем запрос с данными formData
        xhr.send(formData);
    } else {
        console.log('Запрос уже отправлен и ожидает ответа от сервера.');
    }
}