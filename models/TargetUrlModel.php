<?php
namespace Models;
// TargetUrlModel.php
use Database;

class TargetUrlModel {

    public function addUrl($targetUrl)
    {
        // Создаем экземпляр подключения к базе данных
        $database = new \Database();
        $pdo = $database->getConnection();

        try {
            // Подготовленный запрос для вставки нового URL
            $stmt = $pdo->prepare("
            INSERT INTO sf_target_urls (url)
            VALUES (:url)
        ");

            // Выполняем запрос с передачей параметра URL
            $stmt->execute([
                ':url' => $targetUrl,
            ]);

            // Получаем идентификатор последней вставленной записи
            $url_id = $pdo->lastInsertId();

            // Возвращаем успех и идентификатор созданного URL
            return ['success' => true, 'url_id' => $url_id];
        } catch (\PDOException $e) {
            // Обработка ошибки базы данных
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getUrlId($url) {
        // Создаем экземпляр подключения к базе данных
        $database = new \Database();
        $pdo = $database->getConnection();

        try {
            // Подготовленный запрос для получения id по url
            $stmt = $pdo->prepare("
            SELECT id FROM sf_target_urls WHERE url = :url
        ");

            // Выполняем запрос с передачей параметра URL
            $stmt->execute([
                ':url' => $url,
            ]);

            // Получаем результат запроса
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            // Возвращаем id или null, если url не найден
            return $result ? $result['id'] : null;
        } catch (\PDOException $e) {
            // Обработка ошибки базы данных
            return null;
        }
    }
}
?>
