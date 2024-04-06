<?php
require_once 'base.php';

class User
{
    private $conn;
    private $table_name = "user";

    public $ID;
    public $username;
    public $password;
    public $email;
    public $phone;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    function Update()
    {
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->password = htmlspecialchars(strip_tags($this->password));

        $query = "UPDATE " . $this->table_name .
            " SET Password =:password,
            EMail =:email
            WHERE ID = :ID;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':ID', $this->ID);
        $stmt->bindValue(':email', $this->email);
        $stmt->bindValue(':password', password_hash($this->password, PASSWORD_DEFAULT));
        if ($stmt->execute()) return true;
        return false;
        //return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    function ChangePwd()
    {
        $this->password = htmlspecialchars(strip_tags($this->password));

        $query = "UPDATE " . $this->table_name .
            " SET Password =:password
            WHERE ID = :ID;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':ID', $this->ID);
        $stmt->bindValue(':password', password_hash($this->password, PASSWORD_DEFAULT));
        if ($stmt->execute()) return true;
        return false;
        //return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    function GetAll()
    {
        $query = "SELECT 
        Count(1) as Count FROM " . $this->table_name . " ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    function GetAllByFilter($filter)
    {
        $query = "SELECT 
                ID,
                Username,
                CreationDate,
                EMail,
                Phone
         FROM " . $this->table_name . " 
         WHERE Concat(Username,EMail,Phone) like :Filter
         ";
        $data = [
            'Filter' => '%' . $filter . '%'
        ];
        $stmt = $this->conn->prepare($query);
        $stmt->execute($data);
        return $stmt;
    }
    function GetByID()
    {
        $query = "SELECT 
                ID,
                Username,
                CreationDate,
                EMail,
                Phone
         FROM " . $this->table_name . " 
         WHERE ID = :UserID
         ";
        $data = [
            'UserID' => $this->ID
        ];
        $stmt = $this->conn->prepare($query);
        $stmt->execute($data);
        return $stmt;
    }
    function GetByUsername()
    {
        $query = "SELECT 
                ID,
                Username,
                CreationDate,
                EMail,
                Phone
         FROM " . $this->table_name . " 
         WHERE Username = :Username
         ";
        $data = [
            'Username' => $this->username
        ];
        $stmt = $this->conn->prepare($query);
        $stmt->execute($data);
        return $stmt;
    }
}
