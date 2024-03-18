<?php
namespace Models;

// OfferToWebmasterModel.php
use Config\Database;

class OfferToWebmasterModel
{
    private $id;
    private $webmasterId;

    public function getData()
    {
        // Создаем экземпляр подключения к базе данных
        $database = new Database();
        $pdo = $database->getConnection();

        // Подготавливаем SQL запрос для получения URL
        $sql = "SELECT ow.*, ow.is_active AS is_active_main, o.*, u.url
        FROM sf_offers_to_webmaster ow
        INNER JOIN sf_offers o ON ow.offer_id = o.id
        INNER JOIN sf_target_urls u ON o.url_id = u.id
        WHERE ow.id = :id AND ow.webmaster_id = :webmasterId";

        // Подготавливаем запрос
        $stmt = $pdo->prepare($sql);

        // Привязываем параметры к меткам
        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);
        $stmt->bindParam(':webmasterId', $this->webmasterId, \PDO::PARAM_INT);

        // Выполняем запрос
        $stmt->execute();

        // Получаем результат запроса
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Если URL найден, возвращаем его
        if ($result) {
            return $result;
        } else {
            return null; // Или можно вернуть пустую строку, или другое значение по умолчанию
        }
    }

    public function setId($id)
    {
        $this->id = (int)$id;
    }

    public function setWebmasterId($webmasterId)
    {
        $this->webmasterId = (int)$webmasterId;
    }
}
 