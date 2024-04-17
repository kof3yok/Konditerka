// Этот код - это класс ProductPrice, который представляет собой модель для работы с ценами на товары в базе данных
<?php
require_once 'base.php';

class ProductPrice
{
    private $conn;
    private $table_name = "productprice";

    public $ID;
    public $ProductID;
    public $Name;
    public $Description;
    public $Price;
    public $Status;
    public $CreationDate;

    public function __construct($db)
    {
        $this->conn = $db;
    }
    // Этот метод создает новую запись цены продукта в базе данных. 
    // Он принимает данные о продукте (ID, название, описание, цена) и вставляет их в соответствующую таблицу. После вставки возвращает ID только что созданной записи.
    function Create()
    {
        $this->Description = htmlspecialchars(strip_tags($this->Description));
        $query = "INSERT INTO " . $this->table_name . "(
            ID,
            ProductID,
            Name,
            Description,
            Price,
            Status,
            CreationDate) VALUES
        (:ID,:ProductID,:Name,:Description,:Price,1,:CreationDate);";
        $q = $this->conn->prepare($query);
        $data = [
            'ID' => GUID(),
            'ProductID' => $this->ProductID,
            'Name' => $this->Name,
            'Description' => $this->Description,
            'Price' => $this->Price,
            'CreationDate' => date("Y-m-d H:i:s"),
        ];
        $q->execute($data);
        $query = "SELECT ID FROM " . $this->table_name .
            " WHERE ProductID = :ProductID ORDER By CreationDate DESC LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ProductID', $this->ProductID);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Этот метод получает информацию о цене продукта из базы данных по указанному ID.
    function Get()
    {
        $query = "SELECT ID,
        ProductID,
        Name,
        Description,
        Price,
        Status,
        CreationDate FROM " . $this->table_name .
            " WHERE ID = :ID";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ID', $this->ID);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Этот метод получает все цены, связанные с определенным продуктом, из базы данных.
    function GetAllForProduct()
    {
        $query = "SELECT ID,
        ProductID,
        Name,
        Description,
        Price,
        Status,
        CreationDate FROM " . $this->table_name .
            " WHERE ProductID = :ProductID";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ProductID', $this->ProductID);
        $stmt->execute();
        return $stmt;
    }
    // Этот метод удаляет запись о цене продукта из базы данных по указанному ID.
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
    // Этот метод обновляет информацию о цене продукта в базе данных по указанному ID.
    function Update()
    {
        $query = "UPDATE " . $this->table_name .
            " SET 
            Name = :Name,
            Description = :Description,
            Price = :Price
            WHERE ID = :ID;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ID', $this->ID);
        $stmt->bindParam(':Name', $this->Name);
        $stmt->bindParam(':Description', $this->Description);
        $stmt->bindParam(':Price', $this->Price);
        if ($stmt->execute()) return true;
        return false;
    }
    // Этот метод активирует запись о цене продукта в базе данных по указанному ID, устанавливая статус активной.
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
    // Этот метод деактивирует запись о цене продукта в базе данных по указанному ID, устанавливая статус неактивной.
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
