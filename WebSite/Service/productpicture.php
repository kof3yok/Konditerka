// Этот код представляет класс ProductPicture, который предназначен для работы с изображениями товаров в базе данных
<?php
require_once 'base.php';

class ProductPicture
{
    private $conn;
    private $table_name = "productpicture";

    public $ID;
    public $ProductID;
    public $ImageData;
    public $First;
    public $Status;
    public $CreationDate;
    // Устанавливает соединение с базой данных, переданное в качестве аргумента.
    public function __construct($db)
    {
        $this->conn = $db;
    }
    // Создает новую запись в таблице productpicture с данными о продукте и его изображении.
    // Использует подготовленное SQL-выражение для безопасной вставки данных в таблицу.
    // После вставки возвращает идентификатор новой записи.
    function Create()
    {
        $query = "INSERT INTO " . $this->table_name . " (
            ID,
            ProductID,
            ImageData,
            First,
            Status,
            CreationDate) VALUES 
        (:ID,:ProductID,:ImageData,:First,1,:CreationDate);";
        $q = $this->conn->prepare($query);
        // $data = [
        //     'ID' => GUID(),
        //     'ProductID' => $this->ProductID,
        //     'ImageData' => htmlspecialchars(strip_tags($this->ImageData)),
        //     'First' => $this->First,
        //     'Status' => $this->Status,
        //     'CreationDate' => date("Y-m-d H:i:s"),
        // ];
        $q->bindParam(':ID', GUID());
        $q->bindParam(':ProductID', $this->ProductID);
        $q->bindParam(':ImageData', base64_encode($this->ImageData), PDO::PARAM_LOB);
        $q->bindParam(':First', $this->First);
        // $q->bindParam(':Status',$this->Status);
        $q->bindParam(':CreationDate', date("Y-m-d H:i:s"));
        $q->execute();
        $query = "SELECT ID FROM " . $this->table_name .
            " WHERE ProductID = :ProductID ORDER By CreationDate DESC LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ProductID', $this->ProductID);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Получает все записи изображений для определенного продукта.
    // Использует подготовленное SQL-выражение для безопасного получения данных из таблицы.
    function GetAllForProduct()
    {
        $query = "SELECT ID, ProductID, ImageData, First, Status, CreationDate FROM " . $this->table_name .
            " WHERE ProductID = :ProductID";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ProductID', $this->ProductID);
        $stmt->execute();
        return $stmt;
    }
    // Удаляет запись об изображении товара по заданному идентификатору изображения и идентификатору продукта.
    function Delete()
    {
        $query = "DELETE FROM " . $this->table_name .
            " WHERE ID = :ID AND ProductID = :ProductID;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ID', $this->ID);
        $stmt->bindParam(':ProductID', $this->ProductID);
        if ($stmt->execute()) return true;
        return false;
    }
    // Обновляет поле First, чтобы установить одно изображение товара как первичное (первое).
    // Сначала устанавливает First = 0 для всех изображений данного продукта, затем устанавливает First = 1 для указанного изображения.
    function UpdateFirst()
    {
        $query = "UPDATE " . $this->table_name .
            " SET First =0
            WHERE ProductID = :ProductID;" . " UPDATE " . $this->table_name .
            " SET First =1
            WHERE ID = :ID;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ID', $this->ID);
        $stmt->bindParam(':ProductID', $this->ProductID);
        // $stmt->bindParam(':First', $this->First);
        if ($stmt->execute()) return true;
        return false;
    }
    // Активирует (устанавливает статус = 1) указанное изображение товара.
    function Active()
    {
        $query = "UPDATE " . $this->table_name .
            " SET Status =1
            WHERE ID = :ID;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ID', $this->ID);
        if ($stmt->execute()) return true;
        return false;
    }
    // Деактивирует (устанавливает статус = 0) указанное изображение товара.
    function Passive()
    {
        $query = "UPDATE " . $this->table_name .
            " SET Status =0
            WHERE ID = :ID;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ID', $this->ID);
        if ($stmt->execute()) return true;
        return false;
    }
}
