<?php
require_once 'base.php';

class UserOrderDetail
{
    private $conn;
    private $table_name = "userorderdetail";

    public $ID;
    public $OrderID;
    public $ProductID;
    public $Quantity;
    public $Price;
    public $CreationDate;

    public function __construct($db)
    {
        $this->conn = $db;
    }
    function Create()
    {
        $query = "INSERT INTO " . $this->table_name . "(
            ID,
            OrderID,
            ProductID,
            Quantity,
            Price,
            CreationDate) VALUES
        (:ID,:OrderID,:ProductID,:Quantity,:Price,:CreationDate);";
        $q = $this->conn->prepare($query);
        $data = [
            'ID' => GUID(),
            'OrderID' => $this->OrderID,
            'ProductID' => $this->ProductID,
            'Quantity' => $this->Quantity,
            'Price' => $this->Price,
            'CreationDate' => date("Y-m-d H:i:s"),
        ];
        $q->execute($data);
        $query = "SELECT ID FROM " . $this->table_name .
            " WHERE OrderID = :OrderID ORDER By CreationDate DESC LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':OrderID', $this->OrderID);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    function CreateAll($OrderDetailList)
    {
        try {
            foreach ($OrderDetailList as $value) {
                $query = "INSERT INTO " . $this->table_name . "(
            ID,
            OrderID,
            ProductID,
            Quantity,
            Price,
            CreationDate) VALUES
            (:ID,:OrderID,:ProductID,:Quantity,:Price,:CreationDate);";
                $q = $this->conn->prepare($query);
                $data = [
                    'ID' => GUID(),
                    'OrderID' => $value->OrderID,
                    'ProductID' => $value->ProductID,
                    'Quantity' => $value->Quantity,
                    'Price' => $value->Price,
                    'CreationDate' => date("Y-m-d H:i:s"),
                ];
                $q->execute($data);
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
    function GetAllByOrder()
    {
        $query = "SELECT 
        o.ID,
        o.OrderID,
        c.Name as Catalog,
        o.ProductID,
        p.Name,
        p.Description,
        p.Ingredients,
        p.NutritionalValue,
        p.ID as ProductID,
        pp.ImageData,
        o.Quantity,
        o.Price,
        o.CreationDate FROM " . $this->table_name . " o " .
            " INNER JOIN Product p on o.ProductID = p.ID " .
            " INNER JOIN Catalog c on p.CatalogID = c.ID " .
            " LEFT OUTER JOIN productpicture pp on (p.ID = pp.ProductID AND pp.First = 1) " .
            " WHERE OrderID = :OrderID;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':OrderID', $this->OrderID);
        $stmt->execute();
        return $stmt;
    }
    function GetAllByProduct()
    {
        $query = "SELECT 
        Count(DISTINCT o.OrderID) as Count,
        IFNULL(SUM(o.Price*o.Quantity),0) as Sum 
        FROM " . $this->table_name . " o " .
            " WHERE o.ProductID = :ProductID;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ProductID', $this->ProductID);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // function Update()
    // {
    //     $this->Address = htmlspecialchars(strip_tags($this->Address));

    //     $query = "UPDATE " . $this->table_name .
    //         " SET Address =:address
    //         WHERE ID = :ID;";

    //     $stmt = $this->conn->prepare($query);
    //     $stmt->bindParam(':ID', $this->ID);
    //     $stmt->bindParam(':address', $this->Address);
    //     if ($stmt->execute()) return true;
    //     return false;
    // }
}
