<?php
spl_autoload_register(function ($class) {
    // Префикс пространства имен
    $prefix = 'Controllers\\';

    // Базовая директория для классов
    $base_dir = __DIR__ . '/../controllers/';

    // Сравниваем префикс пространства имен
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // Если не соответствует, переходим к следующему зарегистрированному автозагрузчику
        return;
    }

    // Получаем относительное имя класса
    $relative_class = substr($class, $len);

    // Заменяем префикс пространства имен на базовую директорию
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // Проверяем, существует ли файл
    if (file_exists($file)) {
        require $file;
    }
});

spl_autoload_register(function ($class) {
    // Префикс пространства имен
    $prefix = 'Helpers\\';

    // Базовая директория для классов
    $base_dir = __DIR__ . '/../helpers/';

    // Сравниваем префикс пространства имен
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // Если не соответствует, переходим к следующему зарегистрированному автозагрузчику
        return;
    }

    // Получаем относительное имя класса
    $relative_class = substr($class, $len);

    // Заменяем префикс пространства имен на базовую директорию
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // Проверяем, существует ли файл
    if (file_exists($file)) {
        require $file;
    }
});



spl_autoload_register(function ($class) {
    // Префикс пространства имен
    $prefix = 'Interfaces\\';

    // Базовая директория для классов
    $base_dir = __DIR__ . '/../interfaces/';

    // Сравниваем префикс пространства имен
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // Если не соответствует, переходим к следующему зарегистрированному автозагрузчику
        return;
    }

    // Получаем относительное имя класса
    $relative_class = substr($class, $len);

    // Заменяем префикс пространства имен на базовую директорию
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // Проверяем, существует ли файл
    if (file_exists($file)) {
        require $file;
    }
});

spl_autoload_register(function ($class) {
    // Префикс пространства имен
    $prefix = 'Models\\';

    // Базовая директория для классов
    $base_dir = __DIR__ . '/../models/';

    // Сравниваем префикс пространства имен
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // Если не соответствует, переходим к следующему зарегистрированному автозагрузчику
        return;
    }

    // Получаем относительное имя класса
    $relative_class = substr($class, $len);

    // Заменяем префикс пространства имен на базовую директорию
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // Проверяем, существует ли файл
    if (file_exists($file)) {
        require $file;
    }
});

