<?php
require_once 'base.php';

class Catalog
{
    private $conn;
    private $table_name = "catalog";

    public $ID;
    public $Name;

    public function __construct($db)
    {
        $this->conn = $db;
    }
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
    function GetAll()
    {
        $query = "SELECT ID,Name FROM " . $this->table_name . " ORDER BY Name ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    function Get()
    {
        $query = "SELECT ID,Name FROM " . $this->table_name . " WHERE ID=:ID";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ID', $this->ID);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
