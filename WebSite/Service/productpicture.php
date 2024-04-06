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

    public function __construct($db)
    {
        $this->conn = $db;
    }
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
    function GetAllForProduct()
    {
        $query = "SELECT ID, ProductID, ImageData, First, Status, CreationDate FROM " . $this->table_name .
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
