<?php
namespace Models;

// OfferClickLogModel.php
use Database;
use Models\OfferModel;

class OfferClickLogModel
{
    private $webmaster_id;
    private $offer_id;
    private $url_id;
    private $redirect_date;
    private $price_taken;
    private $ip;
    private $is_redirected;
    private $is_admin;

    private $offerIds;

    private $period;

    private $perPage;
    private $offset;

    public function writeLog()
    {
        // Создаем экземпляр подключения к базе данных
        $database = new \Database();
        $pdo = $database->getConnection();

        // Подготавливаем SQL-запрос для вставки записи
        $sql = "INSERT INTO sf_redirect_logs (webmaster_id, offer_id, url_id, price_taken, redirected, ip) 
            VALUES (:webmaster_id, :offer_id, :url_id, :price_taken, :redirected, :ip)";
        $stmt = $pdo->prepare($sql);

        // Привязываем значения к параметрам запроса
        $stmt->bindParam(':webmaster_id', $this->webmaster_id, \PDO::PARAM_INT);
        $stmt->bindParam(':offer_id', $this->offer_id, \PDO::PARAM_INT);
        $stmt->bindParam(':url_id', $this->url_id, \PDO::PARAM_INT);
        $stmt->bindParam(':price_taken', $this->price_taken, \PDO::PARAM_STR);
        $stmt->bindParam(':redirected', $this->is_redirected, \PDO::PARAM_STR);
        $stmt->bindParam(':ip', $this->ip, \PDO::PARAM_STR);

        // Выполняем запрос
        if ($stmt->execute()) {
            return true; // Запись успешно добавлена
        } else {
            return false; // Произошла ошибка при выполнении запроса
        }
    }

    public function getStatsForPeriod()
    {
        // Создаем экземпляр подключения к базе данных
        $database = new Database();
        $pdo = $database->getConnection();

        // Подготавливаем SQL запрос для получения статистики
        $sql = "SELECT DATE_FORMAT(redirect_date, '%Y-%m-%d') AS date, 
            COUNT(*) AS click_count";

        // Если webmaster_id указан, добавляем комиссию к запросу
        if ($this->webmaster_id !== null) {
            $offerModel = new OfferModel();
            $offerModel->setCutCoefficient('0.8');
            $sql .= ", ROUND(SUM(price_taken * " . $offerModel->getCutCoefficient() . "), 2) AS total_income";
        } else if ($this->is_admin !== null) {
            $offerModel = new OfferModel();
            $offerModel->setCutCoefficient('0.2');
            $sql .= ", ROUND(SUM(price_taken * " . $offerModel->getCutCoefficient() . "), 2) AS total_income";
        } else {
            $sql .= ", SUM(price_taken) AS total_income";
        }

        $sql .= " FROM sf_redirect_logs";

        // Если указан offer_id, добавляем его в условие WHERE
        if ($this->offer_id !== null) {
            $sql .= " WHERE offer_id = :offerId";
        }

        // Если указан offer_id, добавляем его в условие WHERE
        if (!empty($this->offerIds)) {
            $sql .= " WHERE offer_id IN (" . implode(",", $this->offerIds) . ")";
        }

        // Если webmaster_id указан, добавляем его в условие WHERE
        if ($this->webmaster_id !== null) {
            $sql .= ($this->offer_id !== null) ? " AND" : " WHERE";
            $sql .= " webmaster_id = :webmasterId";
        }

        // Добавляем фильтр для redirected = 1
        $sql .= ((strpos($sql, 'WHERE') === false) ? " WHERE" : " AND") . " redirected = 1";

        $sql .= " GROUP BY " . $this->period;

        // Подготавливаем запрос
        $stmt = $pdo->prepare($sql);

        // Если указан offer_id, связываем его с меткой
        if ($this->offer_id !== null) {
            $stmt->bindParam(':offerId', $this->offer_id, \PDO::PARAM_INT);
        }

        // Если webmaster_id указан, связываем его с меткой
        if ($this->webmaster_id !== null) {
            $stmt->bindParam(':webmasterId', $this->webmaster_id, \PDO::PARAM_INT);
        }

        // Выполняем запрос
        $stmt->execute();

        // Получаем результат запроса в виде ассоциативного массива
        $stats = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $stats;
    }

    public function getAll()
    {
        // Создаем экземпляр подключения к базе данных
        $database = new \Database();
        $pdo = $database->getConnection();

        $sql = "SELECT rl.*, u.username, tu.url, ofr.name AS advertiser_name, ofr.theme AS advertiser_theme
FROM sf_redirect_logs rl
JOIN sf_users u ON rl.webmaster_id = u.id
LEFT JOIN sf_target_urls tu ON rl.url_id = tu.id
LEFT JOIN sf_offers ofr ON rl.offer_id = ofr.id
LIMIT :perPage OFFSET :offset
";

        // Подготавливаем запрос
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':perPage', $this->perPage, \PDO::PARAM_INT);
        $stmt->bindParam(':offset', $this->offset, \PDO::PARAM_INT);

        // Выполняем запрос
        $stmt->execute();

        // Получаем результат запроса в виде массива ассоциативных массивов (каждый элемент - оффер)
        $offers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Возвращаем список офферов
        return $offers;
    }

    public function countAll()
    {
        // Создаем экземпляр подключения к базе данных
        $database = new \Database();
        $pdo = $database->getConnection();

        // Создаем SQL запрос для подсчета количества офферов
        $sql = "SELECT COUNT(*) AS total FROM sf_redirect_logs";

        // Подготавливаем запрос
        $stmt = $pdo->prepare($sql);

        // Выполняем запрос
        $stmt->execute();

        // Получаем результат запроса
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Возвращаем количество офферов
        return $result['total'];
    }

    public function countAllRedirected()
    {
        // Создаем экземпляр подключения к базе данных
        $database = new \Database();
        $pdo = $database->getConnection();

        // Создаем SQL запрос для подсчета количества записей с redirected = 1
        $sql = "SELECT COUNT(*) AS total FROM sf_redirect_logs WHERE redirected = 1";

        // Подготавливаем запрос
        $stmt = $pdo->prepare($sql);

        // Выполняем запрос
        $stmt->execute();

        // Получаем результат запроса
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Возвращаем количество записей с redirected = 1
        return $result['total'];
    }

    public function setOfferId($offerId)
    {
        $this->offer_id = (int)$offerId;
    }

    public function setPeriod($period)
    {
        switch ($period) {
            case 'day':
                $this->period = 'DATE(redirect_date)';
                break;
            case 'month':
                $this->period = 'YEAR(redirect_date), MONTH(redirect_date)';
                break;
            case 'year':
                $this->period = 'YEAR(redirect_date)';
                break;
            default:
                $this->period = 'DATE(redirect_date)';
        }
    }

    public function setWebmasterId($webmasterId)
    {
        $this->webmaster_id = (int)$webmasterId;
    }

    public function setUrlId($urlId)
    {
        $this->url_id = (int)$urlId;
    }

    public function setPriceTaken($priceTaken)
    {
        $this->price_taken = (string)$priceTaken;
    }

    public function setIsAdmin($value)
    {
        $this->is_admin = (int)$value;
    }

    public function setRedirected($isRedirect)
    {
        $this->is_redirected = (int)$isRedirect;
    }

    // Метод для установки IP адреса
    public function setIp($ip)
    {
        $this->ip = (string)$ip;
    }

    public function setPerPage($value)
    {
        $this->perPage = (int)$value;
    }

    public function setOffset($value)
    {
        $this->offset = (int)$value;
    }

    // Метод для установки offer_id
    public function setOfferIds(array $offerIds)
    {
        $this->offerIds = $offerIds;
    }

    // Метод для получения offer_id
    public function getOfferIds()
    {
        return $this->offerIds;
    }
}

?>
