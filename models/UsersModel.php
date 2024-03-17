<?php
namespace Models;

// UsersModel.php
use Database;
use Interfaces\UserModelInterface;


class UsersModel implements UserModelInterface
{
    private $response = [
        'status' => 0,
        'message' => 'Неизвесная ошибка'
    ];

    // offer id
    private $id;
    private $perPage = 10;
    private $offset = 1;

    private $username;
    private $role_id;
    private $email;
    private $password;
    private $status;

    /*
     * Система определяет комиссию (например, 20%) за свои услуги.
     * Таким образом, веб-мастер за привлечение клиентов, получит 0.8*N рублей, а система заработает 0.2*N рублей.
     * */
    private $cutCoefficient = '0.2';

    // Метод для получения списка офферов с учетом пагинации и опционального advertiser_id
    public function getUsersWithPagination()
    {
        // Создаем экземпляр подключения к базе данных
        $database = new \Database();
        $pdo = $database->getConnection();

        // Создаем SQL запрос для выборки пользователей с их ролями с учетом пагинации
        $sql = "SELECT u.*, r.name as role_name 
            FROM sf_users u
            LEFT JOIN sf_roles r ON u.role_id = r.id";

        // Добавляем LIMIT и OFFSET в запрос
        $sql .= " LIMIT :perPage OFFSET :offset";

        // Подготавливаем запрос
        $stmt = $pdo->prepare($sql);

        // Привязываем параметры к меткам
        $stmt->bindParam(':perPage', $this->perPage, \PDO::PARAM_INT);
        $stmt->bindParam(':offset', $this->offset, \PDO::PARAM_INT);

        // Выполняем запрос
        $stmt->execute();

        // Получаем результат запроса в виде массива ассоциативных массивов (каждый элемент - пользователь с его ролью)
        $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Возвращаем список пользователей
        return $users;
    }

    // Метод для получения списка офферов с учетом пагинации и опционального advertiser_id
    public function countUsers()
    {
        // Создаем экземпляр подключения к базе данных
        $database = new \Database();
        $pdo = $database->getConnection();

        // Создаем SQL запрос для подсчета количества офферов
        $sql = "SELECT COUNT(*) AS total FROM sf_users";

        // Подготавливаем запрос
        $stmt = $pdo->prepare($sql);

        // Выполняем запрос
        $stmt->execute();

        // Получаем результат запроса
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Возвращаем количество офферов
        return $result['total'];
    }

    public function updateUser()
    {
        // Создаем экземпляр подключения к базе данных
        $database = new \Database();
        $pdo = $database->getConnection();

        // Подготавливаем SQL запрос для обновления информации о пользователе
        $sql = "UPDATE sf_users SET username = :username, role_id = :role_id, email = :email";

        // Если пароль указан, добавляем его в запрос
        if (!empty($this->password)) {
            $sql .= ", password = :password";
        }

        // Добавляем обновление статуса
        $sql .= ", is_active = :status";

        $sql .= " WHERE id = :id";

        // Подготавливаем запрос
        $stmt = $pdo->prepare($sql);

        // Привязываем значения к параметрам запроса
        $stmt->bindParam(':username', $this->username, \PDO::PARAM_STR);
        $stmt->bindParam(':role_id', $this->role_id, \PDO::PARAM_INT);
        $stmt->bindParam(':email', $this->email, \PDO::PARAM_STR);

        // Если пароль указан, связываем его с параметром запроса
        if (!empty($this->password)) {
            $stmt->bindParam(':password', $this->password, \PDO::PARAM_STR);
        }

        // Связываем значение статуса с параметром запроса
        $stmt->bindParam(':status', $this->status, \PDO::PARAM_INT);

        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);

        // Выполняем запрос
        if ($stmt->execute()) {
            return true; // Успешно обновлено
        } else {
            return false; // Ошибка при обновлении
        }
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

    public function changeStatus()
    {
        // Создаем экземпляр подключения к базе данных
        $database = new Database();
        $pdo = $database->getConnection();

        // Получаем текущий статус is_active для указанного пользователя
        $stmt = $pdo->prepare("SELECT is_active FROM sf_users WHERE id = :userId");
        $stmt->bindParam(':userId', $this->id, \PDO::PARAM_INT);
        $stmt->execute();
        $currentStatus = $stmt->fetchColumn();

        // Определяем новое значение статуса is_active
        $newStatus = ($currentStatus == 0) ? 1 : 0;

        // Обновляем статус is_active в базе данных
        $updateStmt = $pdo->prepare("UPDATE sf_users SET is_active = :newStatus WHERE id = :userId");
        $updateStmt->bindParam(':newStatus', $newStatus, \PDO::PARAM_INT);
        $updateStmt->bindParam(':userId', $this->id, \PDO::PARAM_INT);

        if ($updateStmt->execute()) {
            return true; // Успешно обновлено
        } else {
            return false; // Ошибка при обновлении
        }
    }

    public function getUser()
    {
        // Создаем экземпляр подключения к базе данных
        $database = new Database();
        $pdo = $database->getConnection();

        // Подготавливаем SQL запрос для получения пользователя по его ID
        $sql = "SELECT id, username, email, role_id, is_active FROM sf_users WHERE id = :userId";

        // Подготавливаем запрос
        $stmt = $pdo->prepare($sql);

        // Привязываем параметр к метке
        $stmt->bindParam(':userId', $this->id, \PDO::PARAM_INT);

        // Выполняем запрос
        $stmt->execute();

        // Получаем результат запроса в виде ассоциативного массива (данные пользователя)
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $user;
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

    public function setUsername($username)
    {
        $this->username = (string)$username;
    }

    public function setStatus($is_active)
    {
        $this->status = (string)$is_active === 'on' ? 1 : 0;
    }

    public function setRoleId($role_id)
    {
        $this->role_id = (string)$role_id;
    }

    public function setEmail($email)
    {
        $this->email = (string)$email;
    }

    public function setPassword($password)
    {
        $this->password = password_hash((string)$password, PASSWORD_DEFAULT); // Хеширование пароля;
    }
}

?>
