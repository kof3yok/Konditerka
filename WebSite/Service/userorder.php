<?php
require_once 'base.php';
require_once 'mail.php';

class UserOrder
{
    private $conn;
    private $table_name = "userorder";

    public $ID;
    public $UserID;
    public $Price;
    public $Driver;
    public $Status;
    public $CreationDate;
    public $Address;

    public function __construct($db)
    {
        $this->conn = $db;
    }
    function Create()
    {
        $query = "INSERT INTO " . $this->table_name . "(
            ID,
            UserID,
            Price,
            CreationDate,Address) VALUES
        (:ID,:UserID,:Price,:CreationDate,:Address);";
        $q = $this->conn->prepare($query);
        $data = [
            'ID' => GUID(),
            'UserID' => $this->UserID,
            'Price' => $this->Price,
            'Address' => $this->Address,
            'CreationDate' => date("Y-m-d H:i:s"),
        ];
        $q->execute($data);
        $query = "SELECT ID FROM " . $this->table_name .
            " WHERE UserID = :UserID ORDER By CreationDate DESC LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':UserID', $this->UserID);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    function GetAllByUser()
    {
        $query = "SELECT 
        ID,
        UserID,
        Price,
        Address,
        Driver,
        Status,
        CreationDate FROM " . $this->table_name .
            " WHERE UserID = :UserID
            ORDER BY CreationDate DESC;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':UserID', $this->UserID);
        $stmt->execute();
        return $stmt;
    }
    function GetLast10()
    {
        $query = "SELECT 
        o.ID,
        o.UserID,
        u.Username,
        o.Price,
        o.Address,
        (SELECT SUM(uod.Quantity) FROM userorderdetail uod WHERE o.ID = uod.OrderID) as Quantity,
        o.Status,
        o.CreationDate FROM " . $this->table_name . ' o ' .
            " INNER JOIN user u on o.UserID = u.ID " .
            // " INNER JOIN userorderdetail uod on o.ID = uod.OrderID ".
            " WHERE o.Status IN (0,1)" . // 0 Waiting ,1=Accepted ,2=Rejected ,3=Sent
            " ORDER BY o.CreationDate DESC Limit 0,10";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    function GetAllForDashboard()
    {
        $query = "SELECT 
        Count(1) as Count,
        IFNULL(SUM(o.Price),0) as Price FROM " . $this->table_name .
            " o WHERE o.Status =3 ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    function GetAll($filter)
    {
        $query = "SELECT 
        o.ID,
        o.UserID,
        u.Username,
        o.Price,
        o.Address,
        (SELECT SUM(uod.Quantity) FROM userorderdetail uod WHERE o.ID = uod.OrderID) as Quantity,
        o.Status,
        o.CreationDate FROM " . $this->table_name . ' o ' .
            " INNER JOIN user u on o.UserID = u.ID " .
            " WHERE Concat(u.Username,o.ID) like :Filter " .
            " ORDER BY o.CreationDate DESC";
        $data = [
            'Filter' => '%' . $filter . '%'
        ];
        $stmt = $this->conn->prepare($query);
        $stmt->execute($data);
        return $stmt;
    }
    function Get()
    {
        $query = "SELECT 
        o.ID,
        o.UserID,
        o.Price,
        o.Status,
        o.Driver,
        o.Address as Address,
        o.CreationDate FROM " . $this->table_name . ' o ' .
            " WHERE o.ID = :ID ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ID', $this->ID);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    function UpdateStatus()
    {
        $query = "UPDATE " . $this->table_name .
            " SET Status = :Status" .
            " WHERE ID = :ID";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':Status', $this->Status);
        $stmt->bindParam(':ID', $this->ID);
        if ($stmt->execute()) {
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
            o.CreationDate FROM userorderdetail o " .
                " INNER JOIN Product p on o.ProductID = p.ID " .
                " INNER JOIN Catalog c on p.CatalogID = c.ID " .
                " LEFT OUTER JOIN productpicture pp on (p.ID = pp.ProductID AND pp.First = 1) " .
                " WHERE OrderID = :OrderID;";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':OrderID', $this->ID);
            $stmt->execute();
            $orderdetail = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $all_item = array(
                    "ID" =>   $row['ID'],
                    "OrderID" => $row['OrderID'],
                    "Catalog" => $row['Catalog'],
                    "ProductID" => $row['ProductID'],
                    "Name" => $row['Name'],
                    "Quantity" => $row['Quantity'],
                    "Price" => $row['Price'],
                    "Total" => $row['Price'] * $row['Quantity'],
                    "ImageData" => $row['ImageData'],
                    "CreationDate" => $row['CreationDate'],
                );
                array_push($orderdetail, $all_item);
            }

            $order = $this->Get();
            $query = "SELECT EMail FROM User WHERE ID = :UserID";
            $data = [
                'UserID' => $order["UserID"]
            ];
            $stmt = $this->conn->prepare($query);
            $stmt->execute($data);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            $mail = new RenewPassword($this->conn);
            $mail->SendPreparingEMail($user["EMail"], $orderdetail, $this->Status,$this->Driver);
            return true;
        }
        return false;
    }
    function Sent()
    {
        $query = "UPDATE " . $this->table_name .
            " SET Status = :Status," .
            " Driver = :Driver" .
            " WHERE ID = :ID";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':Driver', $this->Driver);
        $stmt->bindParam(':Status', $this->Status);
        $stmt->bindParam(':ID', $this->ID);
        if ($stmt->execute()) {
            $this->UpdateStatus();
            return true;
        }
        return false;
    }
}
