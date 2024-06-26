// Этот код - это класс PHP для работы с адресами в базе данных
<?php
require_once 'base.php';

class Address
{
    private $conn; // Связь с БД
    private $table_name = "address"; // Выбор таблицы
    
    // Колонки выбора таблицы 
    public $ID; 
    public $UserID;
    public $Address;
    public $CreationDate;

    public function __construct($db)
    {
        $this->conn = $db;
    }
    // Создание нового адреса
    function Create() 
    {
        $this->Address = htmlspecialchars(strip_tags($this->Address));

        $query = "INSERT INTO " . $this->table_name . "(ID,
        UserID,
        Address,
        CreationDate) VALUES
        (:ID,:UserID,:address,:creationdate);";
        $q = $this->conn->prepare($query);
        $data = [
            'ID' => GUID(),
            'UserID' => $this->UserID,
            'address' => $this->Address,
            'creationdate' => date("Y-m-d H:i:s"),
        ];
        $q->execute($data);
        $query = "SELECT ID FROM " . $this->table_name .
            " WHERE UserID = :UserID ORDER By CreationDate DESC LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':UserID', $this->UserID);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Удаление нового адреса
    function Delete()
    {
        $query = "DELETE FROM " . $this->table_name .
            " WHERE ID = :ID AND UserID = :UserID;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ID', $this->ID);
        $stmt->bindParam(':UserID', $this->UserID);
        if ($stmt->execute()) return true;
        return false;
        //return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Обновление нового адреса
    function Update()
    {
        $this->Address = htmlspecialchars(strip_tags($this->Address));

        $query = "UPDATE " . $this->table_name .
            " SET Address =:address
            WHERE ID = :ID;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ID', $this->ID);
        $stmt->bindParam(':address', $this->Address);
        if ($stmt->execute()) return true;
        return false;
        //return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Создание/обновление адреса
    function CreateOrUpdate()
    {
        $this->Address = htmlspecialchars(strip_tags($this->Address));

        $query = "SELECT ID FROM " . $this->table_name .
            " WHERE  UserID= :UserID AND Address= :address LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':UserID', $this->UserID);
        $stmt->bindParam(':address', $this->Address);
        $stmt->execute();
        $numAddr = $stmt->rowCount();
        if ($numAddr > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->ID = $row["ID"];
            $this->Update();
        } else {
            $this->Create();
        }
    }
    // Получение последнегоо актуального адреса пользователя
    function GetByUserID()
    {
        $query = "SELECT Address FROM " . $this->table_name . " WHERE UserID = :UserID ORDER By CreationDate DESC LIMIT 0,1";
        $data = [
            'UserID' => $this->UserID
        ];
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':UserID', $this->UserID);
        $stmt->execute();
        return $stmt;
    }
}
