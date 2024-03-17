<?php
namespace Models;

// OfferModel.php
use Database;


class OfferModel
{
    private $response = [
        'status' => 0,
        'message' => 'Неизвесная ошибка'
    ];

    // offer id
    private $id;
    private $advertiser_id;
    private $perPage;
    private $offset;

    /*
     * Система определяет комиссию (например, 20%) за свои услуги.
     * Таким образом, веб-мастер за привлечение клиентов, получит 0.8*N рублей, а система заработает 0.2*N рублей.
     * */
    private $cutCoefficient = '0.8';

    // Метод для получения списка доступных офферов для веб-мастера
    public static function getAvailableOffersForWebmaster($webmaster_id)
    {
        global $db;

        $query = "SELECT * FROM offers WHERE is_active = 1"; // Предполагается, что оффер активен
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Метод для подписки веб-мастера на оффер
    public static function subscribeToOffer($webmaster_id, $offer_id, $cost_per_click)
    {
        global $db;

        // Проверяем, существует ли уже подписка веб-мастера на этот оффер
        $query = "SELECT * FROM webmasters_offers WHERE webmaster_id = :webmaster_id AND offer_id = :offer_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':webmaster_id', $webmaster_id);
        $stmt->bindParam(':offer_id', $offer_id);
        $stmt->execute();
        $existing_subscription = $stmt->fetch(PDO::FETCH_ASSOC);

        // Если подписка уже существует, обновляем стоимость перехода
        if ($existing_subscription) {
            $query = "UPDATE webmasters_offers SET cost_per_click = :cost_per_click WHERE webmaster_id = :webmaster_id AND offer_id = :offer_id";
        } else {
            // Иначе создаем новую подписку
            $query = "INSERT INTO webmasters_offers (webmaster_id, offer_id, cost_per_click) VALUES (:webmaster_id, :offer_id, :cost_per_click)";
        }

        // Выполняем запрос
        $stmt = $db->prepare($query);
        $stmt->bindParam(':webmaster_id', $webmaster_id);
        $stmt->bindParam(':offer_id', $offer_id);
        $stmt->bindParam(':cost_per_click', $cost_per_click);
        return $stmt->execute();
    }


    public function createOffer($offer_name, $advertiser_id, $cost_per_click, $target_url, $themes)
    {
        try {
            // Создаем экземпляр подключения к базе данных
            $database = new Database();
            $pdo = $database->getConnection();

            // Подготовленный запрос для вставки новой записи в таблицу sf_offers
            $stmt = $pdo->prepare("INSERT INTO sf_offers (advertiser_id, name, cost_per_click, url_id, is_active, theme, timestamp) VALUES (:advertiser_id, :name, :cost_per_click, :url_id, 1, :theme, NOW())");

            // Выполняем запрос с передачей параметров
            $stmt->execute([
                'advertiser_id' => $advertiser_id,
                'name' => $offer_name,
                'cost_per_click' => $cost_per_click,
                'url_id' => $target_url,
                'theme' => $themes,
            ]);

            // Проверяем количество добавленных строк
            $rowCount = $stmt->rowCount();
            if ($rowCount > 0) {
                $this->response['status'] = 1;
                $this->response['message'] = 'Оффер успешно создан!';

                return $this->response;
            }
        } catch (\PDOException $e) {
            $this->response['status'] = 0;
            // Проверка, возникла ли ошибка из-за дублирования имени пользователя
            if ($e->getCode() === '23000' && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $this->response['message'] = 'Такая запись уже есть.';
                return $this->response;
            }

            $this->response['message'] = $e->getMessage();

            return $this->response;
        }
    }

    public function subscribeOffer($userId, $offerId)
    {
        // Создаем экземпляр подключения к базе данных
        $database = new \Database();
        $pdo = $database->getConnection();

        // Проверяем наличие записи с is_active = 0 для указанного пользователя и оффера
        $sql = "SELECT COUNT(*) AS count 
            FROM sf_offers_to_webmaster 
            WHERE offer_id = :offerId 
                AND webmaster_id = :userId 
                AND is_active = 0";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':offerId', $offerId, \PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            // Если запись с is_active = 0 существует, обновляем ее на is_active = 1
            $sqlUpdate = "UPDATE sf_offers_to_webmaster 
                      SET is_active = 1 
                      WHERE offer_id = :offerId 
                          AND webmaster_id = :userId 
                          AND is_active = 0";
            $stmtUpdate = $pdo->prepare($sqlUpdate);
            $stmtUpdate->bindParam(':offerId', $offerId, \PDO::PARAM_INT);
            $stmtUpdate->bindParam(':userId', $userId, \PDO::PARAM_INT);
            $stmtUpdate->execute();
        } else {
            // Продолжаем существующую логику добавления новой подписки
            $sqlInsert = "INSERT INTO sf_offers_to_webmaster (offer_id, webmaster_id) VALUES (:offerId, :userId)";
            $stmtInsert = $pdo->prepare($sqlInsert);
            $stmtInsert->bindParam(':offerId', $offerId, \PDO::PARAM_INT);
            $stmtInsert->bindParam(':userId', $userId, \PDO::PARAM_INT);
            $stmtInsert->execute();
        }

        return true; // Успешное выполнение операции
    }

    public function subscribeCheck($userId, $offerId)
    {
        // Создаем экземпляр подключения к базе данных
        $database = new \Database();
        $pdo = $database->getConnection();

        // Подготавливаем SQL запрос для проверки подписки
        $sql = "SELECT COUNT(*) AS count 
        FROM sf_offers_to_webmaster 
        WHERE offer_id = :offerId 
            AND webmaster_id = :userId 
            AND is_active = 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':offerId', $offerId, \PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Проверяем, была ли найдена подписка
        if ($result['count'] > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function updateOffer($offerId, $advertiserId, $offerName, $costPerClick, $urlId, $isActive, $theme)
    {
        // Создаем экземпляр подключения к базе данных
        $database = new \Database();
        $pdo = $database->getConnection();

        // Проверяем, принадлежит ли оффер указанному рекламодателю
        $sql = "SELECT * FROM sf_offers WHERE id = :offerId AND advertiser_id = :advertiserId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':offerId', $offerId, \PDO::PARAM_INT);
        $stmt->bindParam(':advertiserId', $advertiserId, \PDO::PARAM_INT);
        $stmt->execute();
        $offer = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Если оффер принадлежит указанному рекламодателю, выполняем обновление
        if ($offer) {
            $sql = "UPDATE sf_offers SET name = :offerName, cost_per_click = :costPerClick, url_id = :url_id, is_active = :isActive, theme = :theme WHERE id = :offerId";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':offerName', $offerName, \PDO::PARAM_STR);
            $stmt->bindParam(':costPerClick', $costPerClick, \PDO::PARAM_INT);
            $stmt->bindParam(':url_id', $urlId, \PDO::PARAM_INT);
            $stmt->bindParam(':isActive', $isActive, \PDO::PARAM_INT);
            $stmt->bindParam(':offerId', $offerId, \PDO::PARAM_INT);
            $stmt->bindParam(':theme', $theme, \PDO::PARAM_STR); // Добавляем параметр для темы

            if ($stmt->execute()) {
                // Обновление выполнено успешно
                echo json_encode(['code' => 1, 'message' => 'Сохранено'], JSON_UNESCAPED_UNICODE);
            } else {
                // Произошла ошибка при выполнении SQL запроса
                // Возможно, вам нужно выполнить дополнительные действия для обработки ошибки
                echo json_encode(['code' => 0, 'message' => 'Ошибка сохранения'], JSON_UNESCAPED_UNICODE);
            }
        } else {
            // Оффер не принадлежит указанному рекламодателю, возвращаем false
            echo json_encode(['code' => 0, 'message' => 'Ошибка сохранения'], JSON_UNESCAPED_UNICODE);
        }
    }

    public function unsubscribeOffer($userId, $offerId)
    {
        // Создаем экземпляр подключения к базе данных
        $database = new \Database();
        $pdo = $database->getConnection();

        // Подготавливаем SQL запрос для обновления статуса подписки
        $sql = "UPDATE sf_offers_to_webmaster 
            SET is_active = 0 
            WHERE offer_id = :offerId 
            AND webmaster_id = :userId";

        // Подготавливаем запрос
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':offerId', $offerId, \PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);

        // Выполняем запрос
        if ($stmt->execute()) {
            return true; // Успешно установлен is_active = 0
        } else {
            return false; // Произошла ошибка при выполнении запроса
        }
    }

    public function getAll()
    {
        // Создаем экземпляр подключения к базе данных
        $database = new \Database();
        $pdo = $database->getConnection();

        $sql = "SELECT o.*, u.username, tu.url
        FROM sf_offers o
        JOIN sf_users u ON o.advertiser_id = u.id
        LEFT JOIN sf_target_urls tu ON o.url_id = tu.id
        LIMIT :perPage OFFSET :offset";

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

    // Метод для получения списка офферов с учетом пагинации и опционального advertiser_id
    public function getOffer($id, $advertiserId = null)
    {
        // Создаем экземпляр подключения к базе данных
        $database = new \Database();
        $pdo = $database->getConnection();

// Создаем SQL запрос для получения данных оффера и его URL
        $sql = "SELECT o.*, u.url AS url FROM sf_offers o 
        LEFT JOIN sf_target_urls u ON o.url_id = u.id 
        WHERE o.id = :id";

// Если передан advertiser_id, добавляем его в запрос
        if ($advertiserId !== null) {
            $sql .= " AND o.advertiser_id = :advertiserId";
        }

// Подготавливаем запрос
        $stmt = $pdo->prepare($sql);

// Связываем параметр :id с его значением
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);

// Если передан advertiser_id, связываем его с меткой
        if ($advertiserId !== null) {
            $stmt->bindParam(':advertiserId', $advertiserId, \PDO::PARAM_INT);
        }

// Выполняем запрос
        $stmt->execute();

// Получаем результат запроса
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

// Возвращаем результат
        return $result;
    }


    // Метод для получения списка офферов с учетом пагинации и опционального advertiser_id
    public function getOffersWithPagination($perPage, $offset, $advertiserId = null)
    {
        // Создаем экземпляр подключения к базе данных
        $database = new \Database();
        $pdo = $database->getConnection();

// Создаем SQL запрос для выборки офферов с количеством подписок на каждый оффер
        $sql = "SELECT o.*, u.url, 
           (SELECT COUNT(*) 
            FROM sf_offers_to_webmaster 
            WHERE offer_id = o.id AND is_active = 1) AS subscriptions_count 
    FROM sf_offers o 
    LEFT JOIN sf_target_urls u ON o.url_id = u.id";

// Если передан advertiser_id, добавляем его в запрос
        if ($advertiserId !== null) {
            $sql .= " WHERE o.advertiser_id = :advertiserId";
        }

// Добавляем LIMIT и OFFSET в запрос
        $sql .= " LIMIT :perPage OFFSET :offset";

// Подготавливаем запрос
        $stmt = $pdo->prepare($sql);

// Привязываем параметры к меткам
        $stmt->bindParam(':perPage', $perPage, \PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);

// Если передан advertiser_id, связываем его с меткой
        if ($advertiserId !== null) {
            $stmt->bindParam(':advertiserId', $advertiserId, \PDO::PARAM_INT);
        }

// Выполняем запрос
        $stmt->execute();

// Получаем результат запроса в виде массива ассоциативных массивов (каждый элемент - оффер)
        $offers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

// Возвращаем список офферов
        return $offers;

    }

    public function getOffersWithPaginationWebmasterSubscribed($perPage, $offset, $webmasterId)
    {
        // Создаем экземпляр подключения к базе данных
        $database = new \Database();
        $pdo = $database->getConnection();

        // Создаем SQL запрос для выборки офферов, на которые веб-мастер подписан
        $sql = "SELECT o.*, u.url, (o.cost_per_click * " . $this->getCutCoefficient() . ") AS discounted_cost_per_click, ow.id AS offer_relation
        FROM sf_offers o 
        LEFT JOIN sf_target_urls u ON o.url_id = u.id 
        INNER JOIN sf_offers_to_webmaster ow ON o.id = ow.offer_id
        WHERE ow.webmaster_id = :webmasterId AND o.is_active = 1 AND ow.is_active = 1
        LIMIT :perPage OFFSET :offset";

        // Подготавливаем запрос
        $stmt = $pdo->prepare($sql);

        // Привязываем параметры к меткам
        $stmt->bindParam(':webmasterId', $webmasterId, \PDO::PARAM_INT);
        $stmt->bindParam(':perPage', $perPage, \PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);

        // Выполняем запрос
        $stmt->execute();

        // Получаем результат запроса в виде массива ассоциативных массивов (каждый элемент - оффер)
        $offers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Возвращаем список офферов
        return $offers;
    }

    public function getOffersWithPaginationWebmaster($perPage, $offset, $webmasterId)
    {
        // Создаем экземпляр подключения к базе данных
        $database = new \Database();
        $pdo = $database->getConnection();

        // Создаем SQL запрос для выборки активных офферов, на которые веб-мастер еще не подписан
        $sql = "SELECT o.*, u.url, (o.cost_per_click * " . $this->getCutCoefficient() . ") AS discounted_cost_per_click
        FROM sf_offers o 
        LEFT JOIN sf_target_urls u ON o.url_id = u.id 
        LEFT JOIN sf_offers_to_webmaster ow ON o.id = ow.offer_id AND ow.webmaster_id = :webmasterId
        WHERE (o.is_active = 1 AND (ow.id IS NULL OR ow.is_active = 0))
        LIMIT :perPage OFFSET :offset";

        // Подготавливаем запрос
        $stmt = $pdo->prepare($sql);

        // Привязываем параметры к меткам
        $stmt->bindParam(':webmasterId', $webmasterId, \PDO::PARAM_INT);
        $stmt->bindParam(':perPage', $perPage, \PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);

        // Выполняем запрос
        $stmt->execute();

        // Получаем результат запроса в виде массива ассоциативных массивов (каждый элемент - оффер)
        $offers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Возвращаем список офферов
        return $offers;
    }

    public function countOffersWebmasterSubscribed($webmasterId)
    {
        // Создаем экземпляр подключения к базе данных
        $database = new \Database();
        $pdo = $database->getConnection();

        // Создаем SQL запрос для подсчета количества офферов, на которые веб-мастер подписан
        $sql = "SELECT COUNT(o.id) AS total 
            FROM sf_offers o 
            INNER JOIN sf_offers_to_webmaster ow ON o.id = ow.offer_id
            WHERE ow.webmaster_id = :webmasterId 
             AND o.is_active = 1 
            AND ow.is_active = 1";

        // Подготавливаем запрос
        $stmt = $pdo->prepare($sql);

        // Привязываем параметры к меткам
        $stmt->bindParam(':webmasterId', $webmasterId, \PDO::PARAM_INT);

        // Выполняем запрос
        $stmt->execute();

        // Получаем результат запроса
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Возвращаем количество офферов
        return $result['total'];
    }

    // Метод для получения списка офферов с учетом пагинации и опционального advertiser_id
    public function countOffersWebmaster($webmasterId)
    {
        // Создаем экземпляр подключения к базе данных
        $database = new \Database();
        $pdo = $database->getConnection();

        // Создаем SQL запрос для подсчета количества офферов, на которые веб-мастер еще не подписан
        $sql = "SELECT COUNT(*) AS total 
        FROM sf_offers o 
        LEFT JOIN sf_target_urls u ON o.url_id = u.id 
        LEFT JOIN sf_offers_to_webmaster ow ON o.id = ow.offer_id AND ow.webmaster_id = :webmasterId
        WHERE (o.is_active = 1 AND (ow.id IS NULL OR ow.is_active = 0))";

        // Подготавливаем запрос
        $stmt = $pdo->prepare($sql);

        // Привязываем параметры к меткам
        $stmt->bindParam(':webmasterId', $webmasterId, \PDO::PARAM_INT);

        // Выполняем запрос
        $stmt->execute();

        // Получаем результат запроса
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Возвращаем количество офферов
        return $result['total'];
    }

    // Метод для получения списка офферов с учетом пагинации и опционального advertiser_id
    public function countOffers($advertiserId = null)
    {
        // Создаем экземпляр подключения к базе данных
        $database = new \Database();
        $pdo = $database->getConnection();

        // Создаем SQL запрос для подсчета количества офферов
        $sql = "SELECT COUNT(*) AS total FROM sf_offers";

        // Если передан advertiser_id, добавляем его в запрос
        if ($advertiserId !== null) {
            $sql .= " WHERE advertiser_id = :advertiserId";
        }

        // Подготавливаем запрос
        $stmt = $pdo->prepare($sql);

        // Если передан advertiser_id, связываем его с меткой
        if ($advertiserId !== null) {
            $stmt->bindParam(':advertiserId', $advertiserId, \PDO::PARAM_INT);
        }

        // Выполняем запрос
        $stmt->execute();

        // Получаем результат запроса
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Возвращаем количество офферов
        return $result['total'];
    }

    // Метод для получения списка офферов с учетом пагинации и опционального advertiser_id
    public function countOffersActive($advertiserId = null)
    {
        // Создаем экземпляр подключения к базе данных
        $database = new \Database();
        $pdo = $database->getConnection();

        // Создаем SQL запрос для подсчета количества офферов
        $sql = "SELECT COUNT(*) AS total FROM sf_offers WHERE is_active = 1";

        // Если передан advertiser_id, добавляем его в запрос
        if ($advertiserId !== null) {
            $sql .= " WHERE advertiser_id = :advertiserId";
        }

        // Подготавливаем запрос
        $stmt = $pdo->prepare($sql);

        // Если передан advertiser_id, связываем его с меткой
        if ($advertiserId !== null) {
            $stmt->bindParam(':advertiserId', $advertiserId, \PDO::PARAM_INT);
        }

        // Выполняем запрос
        $stmt->execute();

        // Получаем результат запроса
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Возвращаем количество офферов
        return $result['total'];
    }

    public function getOffersIds()
    {
// Создаем экземпляр подключения к базе данных
        $database = new Database();
        $pdo = $database->getConnection();

// Подготавливаем SQL запрос для получения всех id по advertiser_id
        $sql = "SELECT id FROM sf_offers WHERE advertiser_id = :advertiserId";

// Подготавливаем запрос
        $stmt = $pdo->prepare($sql);

// Привязываем значение advertiser_id к метке
        $stmt->bindParam(':advertiserId', $this->advertiser_id, \PDO::PARAM_INT);

// Выполняем запрос
        $stmt->execute();

// Получаем все id в виде одномерного массива
        $offerIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);

// Выводим результат
        return $offerIds;
    }

    // Метод для получения списка офферов с учетом пагинации и опционального advertiser_id
    public function getOffersWebmaster($webmasterId)
    {
        // Создаем экземпляр подключения к базе данных
        $database = new \Database();
        $pdo = $database->getConnection();

// Создаем SQL запрос для выборки идентификаторов и названий офферов, на которые веб-мастер подписан
        $sql = "SELECT o.id, o.name
        FROM sf_offers o 
        INNER JOIN sf_offers_to_webmaster ow ON o.id = ow.offer_id
        WHERE ow.webmaster_id = :webmasterId AND o.is_active = 1";

// Подготавливаем запрос
        $stmt = $pdo->prepare($sql);

// Привязываем параметры к меткам
        $stmt->bindParam(':webmasterId', $webmasterId, \PDO::PARAM_INT);

// Выполняем запрос
        $stmt->execute();

// Получаем результат запроса в виде массива ассоциативных массивов (каждый элемент содержит id и name оффера)
        $offers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

// Возвращаем список офферов
        return $offers;
    }

    // Метод для получения списка офферов с учетом пагинации и опционального advertiser_id
    public function getOffersAdvertiser($advertiserId = null)
    {
        // Создаем экземпляр подключения к базе данных
        $database = new \Database();
        $pdo = $database->getConnection();

        // Создаем SQL запрос с соединением двух таблиц
        $sql = "SELECT o.id, o.name FROM sf_offers o LEFT JOIN sf_target_urls u ON o.url_id = u.id";

        // Если передан advertiser_id, добавляем его в запрос
        if ($advertiserId !== null) {
            $sql .= " WHERE o.advertiser_id = :advertiserId";
        }

        // Подготавливаем запрос
        $stmt = $pdo->prepare($sql);

        // Если передан advertiser_id, связываем его с меткой
        if ($advertiserId !== null) {
            $stmt->bindParam(':advertiserId', $advertiserId, \PDO::PARAM_INT);
        }

        // Выполняем запрос
        $stmt->execute();

        // Получаем результат запроса в виде массива ассоциативных массивов (каждый элемент - оффер)
        $offers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Возвращаем список офферов
        return $offers;
    }

    public function getCutCoefficient(): string
    {
        return $this->cutCoefficient;
    }

    public function setCutCoefficient($value)
    {
        return $this->cutCoefficient = (string)$value;
    }

    public function changeStatus()
    {
        // Создаем экземпляр подключения к базе данных
        $database = new Database();
        $pdo = $database->getConnection();

        // Получаем текущий статус is_active для указанного оффера
        $stmt = $pdo->prepare("SELECT is_active FROM sf_offers WHERE id = :offerId");
        $stmt->bindParam(':offerId', $this->id, \PDO::PARAM_INT);
        $stmt->execute();
        $currentStatus = $stmt->fetchColumn();

        // Определяем новое значение статуса is_active
        $newStatus = ($currentStatus == 0) ? 1 : 0;

        // Обновляем статус is_active в базе данных
        $updateStmt = $pdo->prepare("UPDATE sf_offers SET is_active = :newStatus WHERE id = :offerId");
        $updateStmt->bindParam(':newStatus', $newStatus, \PDO::PARAM_INT);
        $updateStmt->bindParam(':offerId', $this->id, \PDO::PARAM_INT);

        if ($updateStmt->execute()) {
            return true; // Успешно обновлено
        } else {
            return false; // Ошибка при обновлении
        }
    }

    public function setAdvertiserId($value)
    {
        $this->advertiser_id = (int)$value;
    }

    public function setId($id)
    {
        $this->id = (int)$id;
    }

    public function setPerPage($value)
    {
        $this->perPage = (int)$value;
    }

    public function setOffset($value)
    {
        $this->offset = (int)$value;
    }

}

?>
