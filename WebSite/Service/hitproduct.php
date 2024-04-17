// Этот код на PHP определяет класс HitProduct, который представляет объект для работы с базой данных для таблицы hitproduct
<?php
require_once 'base.php';
class HitProduct
{

    private $conn;
    private $table_name = "hitproduct";

    public $ID;
    public $ProductID;
    public $CreationDate;
// Конструктор класса, который принимает объект базы данных $db и инициализирует соответствующее свойство $conn
    public function __construct($db)
    {
        $this->conn = $db;
    }
    // Выполняет вставку новой записи в таблицу "hitproduct" с указанными данными.    
    // Генерирует уникальный идентификатор (ID) с помощью функции GUID().
    // Записывает дату создания (CreationDate) в текущий момент времени.
    // Выполняет запрос с использованием подготовленного выражения и передает данные через массив.
    // Возвращает true, если запрос выполнен успешно, иначе false.
    function Create()
    {
        $query = "INSERT INTO " . $this->table_name . "(ID,
        ProductID,
        CreationDate) VALUES
        (:ID,:ProductID,:CreationDate);";
        $q = $this->conn->prepare($query);
        $data = [
            'ID' => GUID(),
            'ProductID' => $this->ProductID,
            'CreationDate' => date("Y-m-d H:i:s"),
        ];

        if ($q->execute($data)) return true;
        return false;
    }
    // Удаляет запись из таблицы "hitproduct" по указанному ID.
    // Использует подготовленное выражение для безопасной передачи данных в запрос.
    // Возвращает true, если удаление выполнено успешно, иначе false.
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
    // Выполняет выборку данных из различных таблиц базы данных, чтобы получить полную информацию о продуктах.
    // Возвращает результат выполнения запроса в виде объекта PDOStatement, который содержит выбранные строки.
    function GetAll()
    {
        $query = "SELECT p.ID,
        p.CatalogID,
        c.Name as Catalog,
        p.Name,
        p.Description,
        p.Ingredients,
        p.NutritionalValue,
        p.CreationDate,
        pp.ImageData,
        IFNULL((SELECT CONCAT(price.Name,';',price.Description,';',price.Price) FROM productprice price where price.ProductID = p.ID AND price.Status = 1 ORDER BY price.CreationDate ASC LIMIT 0,1),0) as Price1,
        IFNULL((SELECT price.ID FROM productprice price where price.ProductID = p.ID AND price.Status = 1 ORDER BY price.CreationDate ASC LIMIT 0,1),0) as Price1ID,
        IFNULL((SELECT CONCAT(price.Name,';',price.Description,';',price.Price) FROM productprice price where price.ProductID = p.ID AND price.Status = 1 ORDER BY price.CreationDate DESC LIMIT 0,1),0) as Price2,
        IFNULL((SELECT price.ID FROM productprice price where price.ProductID = p.ID AND price.Status = 1 ORDER BY price.CreationDate DESC LIMIT 0,1),0) as Price2ID,
        p.Status FROM product p " .
            " INNER JOIN catalog c on p.CatalogID = c.ID " .
            " LEFT OUTER JOIN productpicture pp on (p.ID = pp.ProductID AND pp.First = 1) ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
