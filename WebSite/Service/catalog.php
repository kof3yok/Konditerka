// Этот код определяет класс Catalog, который представляет собой простой способ взаимодействия с базой данных для управления записями в таблице catalog
<?php
require_once 'base.php';

class Catalog
{
    private $conn;
    private $table_name = "catalog";

    public $ID;
    public $Name;
// конструктор класса, который инициализирует соединение с базой данных при создании нового экземпляра класса.
// Он принимает соединение с базой данных в качестве параметра и сохраняет его в приватной переменной $conn
    public function __construct($db)
    {
        $this->conn = $db;
    }
    //Этот метод создает новую запись в таблице catalog. Он подготавливает SQL-запрос на вставку данных в таблицу, используя значения $ID и $Name, и выполняет этот запрос. 
    //Затем он выполняет дополнительный запрос для получения ID только что созданной записи, используя имя, и возвращает его
    function Create()
    {
        $query = "INSERT INTO " . $this->table_name . "(ID,
        Name) VALUES
        (:ID,:name);";
        $q = $this->conn->prepare($query);
        $data = [
            'ID' => GUID(),
            'name' => $this->Name
        ];
        $q->execute($data);
        $query = "SELECT ID FROM " . $this->table_name .
            " WHERE Name = :name LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $this->Name);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    //Этот метод удаляет запись из таблицы catalog на основе заданного $ID. Он подготавливает запрос на удаление записи с указанным ID и выполняет его. 
    //Возвращает true, если удаление прошло успешно, в противном случае - false.
    function Delete()
    {
        $query = "DELETE FROM " . $this->table_name .
            " WHERE ID = :ID;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ID', $this->ID);
        if ($stmt->execute()) return true;
        return false;
        //return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Этот метод обновляет существующую запись в таблице catalog на основе заданного $ID. Он подготавливает запрос на обновление имени записи и выполняет его. 
    // Возвращает true, если обновление прошло успешно, в противном случае - false.
    function Update()
    {
        $query = "UPDATE " . $this->table_name .
            " SET Name =:name
            WHERE ID = :ID;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ID', $this->ID);
        $stmt->bindParam(':name', $this->Name);
        if ($stmt->execute()) return true;
        return false;
        //return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Этот метод извлекает все записи из таблицы catalog. Он подготавливает запрос на выбор всех записей из таблицы и возвращает результат выполнения запроса
    function GetAll()
    {
        $query = "SELECT ID,Name FROM " . $this->table_name . " ORDER BY Name ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    // Этот метод получает конкретную запись из таблицы catalog на основе заданного $ID. Он подготавливает запрос на выбор записи с указанным ID и возвращает ее в виде ассоциативного массива
    function Get()
    {
        $query = "SELECT ID,Name FROM " . $this->table_name . " WHERE ID=:ID";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ID', $this->ID);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
