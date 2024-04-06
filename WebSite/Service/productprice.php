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
