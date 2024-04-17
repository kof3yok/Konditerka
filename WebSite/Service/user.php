// Этот код реализует класс User, который представляет собой модель пользователя в базе данных.
<?php
require_once 'base.php';

class User
{
    private $conn;
    private $table_name = "user";

    public $ID;
    public $username;
    public $password;
    public $email;
    public $phone;
    // Устанавливает соединение с базой данных и сохраняет его в переменной $conn.
    public function __construct($db)
    {
        $this->conn = $db;
    }
    // Обновляет данные пользователя в базе данных.
    // Принимает новый пароль и электронную почту для обновления.
    // Защищает входные данные от XSS-атак и SQL-инъекций.
    // Хэширует пароль перед сохранением в базу данных с использованием функции password_hash.
    function Update()
    {
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->password = htmlspecialchars(strip_tags($this->password));

        $query = "UPDATE " . $this->table_name .
            " SET Password =:password,
            EMail =:email
            WHERE ID = :ID;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':ID', $this->ID);
        $stmt->bindValue(':email', $this->email);
        $stmt->bindValue(':password', password_hash($this->password, PASSWORD_DEFAULT));
        if ($stmt->execute()) return true;
        return false;
        //return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Изменяет пароль пользователя в базе данных.
    // Принимает новый пароль для изменения.
    // Защищает входные данные от XSS-атак и SQL-инъекций.
    // Хэширует новый пароль перед сохранением в базу данных с использованием функции password_hash.
    function ChangePwd()
    {
        $this->password = htmlspecialchars(strip_tags($this->password));

        $query = "UPDATE " . $this->table_name .
            " SET Password =:password
            WHERE ID = :ID;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':ID', $this->ID);
        $stmt->bindValue(':password', password_hash($this->password, PASSWORD_DEFAULT));
        if ($stmt->execute()) return true;
        return false;
        //return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Возвращает количество записей в таблице пользователей.
    function GetAll()
    {
        $query = "SELECT 
        Count(1) as Count FROM " . $this->table_name . " ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Возвращает список пользователей, отфильтрованных по заданному критерию (фильтру).
    // Использует оператор LIKE для поиска совпадений в именах пользователей, электронной почте и телефонных номерах.
    function GetAllByFilter($filter)
    {
        $query = "SELECT 
                ID,
                Username,
                CreationDate,
                EMail,
                Phone
         FROM " . $this->table_name . " 
         WHERE Concat(Username,EMail,Phone) like :Filter
         ";
        $data = [
            'Filter' => '%' . $filter . '%'
        ];
        $stmt = $this->conn->prepare($query);
        $stmt->execute($data);
        return $stmt;
    }
    // Возвращает данные пользователя по его идентификатору (ID).
    function GetByID()
    {
        $query = "SELECT 
                ID,
                Username,
                CreationDate,
                EMail,
                Phone
         FROM " . $this->table_name . " 
         WHERE ID = :UserID
         ";
        $data = [
            'UserID' => $this->ID
        ];
        $stmt = $this->conn->prepare($query);
        $stmt->execute($data);
        return $stmt;
    }
    // Возвращает данные пользователя по его имени пользователя (username).
    function GetByUsername()
    {
        $query = "SELECT 
                ID,
                Username,
                CreationDate,
                EMail,
                Phone
         FROM " . $this->table_name . " 
         WHERE Username = :Username
         ";
        $data = [
            'Username' => $this->username
        ];
        $stmt = $this->conn->prepare($query);
        $stmt->execute($data);
        return $stmt;
    }
}
