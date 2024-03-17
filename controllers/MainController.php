<?php
// MainController.php
namespace Controllers;

class MainController
{
    protected function renderView($view, $data)
    {
        // Путь к шаблонам представлений
        $viewPath = __DIR__ . '/../views/';

        // Проверяем, существует ли файл представления
        if (file_exists($viewPath . $view)) {
            // Загружаем и возвращаем содержимое файла представления
            ob_start();
            include $viewPath . $view;
            include $_SERVER['DOCUMENT_ROOT'] . '/../views/layouts/main.php';
        } else {
            // Если файл представления не найден, возвращаем пустую строку
            return '';
        }
    }

    protected function respondWithJson($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    protected function pageNotFound($meaasege = 'Страница не найдена')
    {
        http_response_code(404);
        echo $meaasege;
        exit();
    }

    protected function resolveControllerName($name)
    {
        // Находим позицию последнего символа '\'
        $lastBackslashPosition = strrpos($name, '\\');

        // Получаем подстроку, начиная с символа после последнего '\'
        $controllerShortName = substr($name, $lastBackslashPosition + 1);

        // Заменяем "Controller" на пустую строку
        $controllerShortName = str_replace('Controller', '', $controllerShortName);

        // Приводим результат к нижнему регистру
        return strtolower($controllerShortName);
    }
}

?>
