<?php
require_once 'base.php';

class OrderAddress
{
    private $conn;
    private $table_name = "orderaddress";

    public $ID;
    public $OrderID;
    public $Address;
    public $CreationDate;

    public function __construct($db)
    {
        $this->conn = $db;
    }
    function Create()
    {
        $this->Address = htmlspecialchars(strip_tags($this->Address));

        $query = "INSERT INTO " . $this->table_name . "(
            ID,
            OrderID,
            Address,
            CreationDate) VALUES
        (:ID,:OrderID,:Address,:CreationDate);";
        $q = $this->conn->prepare($query);
        $data = [
            'ID' => GUID(),
            'OrderID' => $this->OrderID,
            'Address' => $this->Address,
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
    // function Delete()
    // {
    //     $query = "DELETE FROM " . $this->table_name .
    //         " WHERE ID = :ID AND UserID = :UserID;";

    //     $stmt = $this->conn->prepare($query);
    //     $stmt->bindParam(':ID', $this->ID);
    //     $stmt->bindParam(':UserID', $this->UserID);
    //     if ($stmt->execute()) return true;
    //     return false;
    // }
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
