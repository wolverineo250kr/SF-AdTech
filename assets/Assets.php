<?php
namespace Assets;

class Assets
{
    public static function generateCssLink($controller, $action)
    {
        $filePath = "assets/$controller/css/$action.css";
        if (file_exists($filePath)) {
            return "<link rel='stylesheet' href='/$filePath'>" . PHP_EOL;
        } else {
            return '';
        }
    }

    public static function generateJsLink($controller, $action)
    {
        $filePath = "assets/$controller/js/$action.js";

        if (file_exists($filePath)) {
            return "<script src='/$filePath'></script>" . PHP_EOL;
        } else {
            return '';
        }
    }

// Подключение общих CSS файлов
    public static function includeCommonCss()
    {
        $cssPath = __DIR__ . '/../web/assets/common/css/';

        $cssFiles = scandir($cssPath);
        foreach ($cssFiles as $cssFile) {
            if ($cssFile !== '.' && $cssFile !== '..') {
                // Выводим тег link для CSS файла
                echo '<link rel="stylesheet" href="/assets/common/css/' . $cssFile . '">' . PHP_EOL;
            }
        }
    }

// Подключение общих JS файлов
    public static function includeCommonJs()
    {
        $jsPath = __DIR__ . '/../web/assets/common/js/';
        $jsFiles = scandir($jsPath);
        foreach ($jsFiles as $jsFile) {
            if ($jsFile !== '.' && $jsFile !== '..') {
                // Выводим тег script для JS файла
                echo '<script src="/assets/common/js/' . $jsFile . '"></script>' . PHP_EOL;
            }
        }
    }
}