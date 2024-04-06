<?php
require_once 'base.php';

class Product
{
    private $conn;
    private $table_name = "product";

    public $ID;
    public $CatalogID;
    public $Name;
    public $Description;
    public $Ingredients;
    public $NutritionalValue;
    public $CreationDate;
    public $Filter;

    public function __construct($db)
    {
        $this->conn = $db;
    }
    function Create()
    {
        $this->Description = htmlspecialchars(strip_tags($this->Description));
        $this->Ingredients = htmlspecialchars(strip_tags($this->Ingredients));
        $this->NutritionalValue = htmlspecialchars(strip_tags($this->NutritionalValue));

        $query = "INSERT INTO " . $this->table_name . "(ID,
        CatalogID,
        Name,
        Description,
        Ingredients,
        NutritionalValue,
        Status,
        CreationDate) VALUES
        (:ID,:CatalogID,:Name,:Description,:Ingredients,:NutritionalValue,1,:CreationDate);";
        $q = $this->conn->prepare($query);
        $data = [
            'ID' => GUID(),
            'CatalogID' => $this->CatalogID,
            'Name' => $this->Name,
            'Description' => $this->Description,
            'Ingredients' => $this->Ingredients,
            'NutritionalValue' => $this->NutritionalValue,
            'CreationDate' => date("Y-m-d H:i:s"),
        ];

        $q->execute($data);
        $query = "SELECT ID FROM " . $this->table_name .
            " ORDER By CreationDate DESC LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    function Delete()
    {
        $query = "UPDATE " . $this->table_name .
            " SET Status=0 WHERE ID = :ID;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ID', $this->ID);
        if ($stmt->execute()) return true;
        return false;
        //return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    function Active()
    {
        $query = "UPDATE " . $this->table_name .
            " SET Status=1 WHERE ID = :ID;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ID', $this->ID);
        if ($stmt->execute()) return true;
        return false;
        //return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    function Update()
    {
        $this->Description = htmlspecialchars(strip_tags($this->Description));
        $this->Ingredients = htmlspecialchars(strip_tags($this->Ingredients));
        $this->NutritionalValue = htmlspecialchars(strip_tags($this->NutritionalValue));

        $query = "UPDATE " . $this->table_name .
            " SET 
            CatalogID = :CatalogID,
            Name = :Name,
            Description = :Description,
            Ingredients = :Ingredients,
            NutritionalValue = :NutritionalValue
            WHERE ID = :ID;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ID', $this->ID);
        $stmt->bindParam(':CatalogID', $this->CatalogID);
        $stmt->bindParam(':Name', $this->Name);
        $stmt->bindParam(':Description', $this->Description);
        $stmt->bindParam(':Ingredients', $this->Ingredients);
        $stmt->bindParam(':NutritionalValue', $this->NutritionalValue);
        if ($stmt->execute()) return true;
        return false;
        //return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    function GetAll()
    {
        $this->Filter = htmlspecialchars(strip_tags($this->Filter));
        $query = "SELECT p.ID,
        p.CatalogID,
        c.Name as Catalog,
        p.Name,
        p.Description,
        p.Ingredients,
        p.NutritionalValue,
        p.CreationDate,
        pp.ImageData,
        p.Status FROM " . $this->table_name . " p " .
            " INNER JOIN catalog c on p.CatalogID = c.ID " .
            " LEFT OUTER JOIN productpicture pp on (p.ID = pp.ProductID AND pp.First = 1) " .
            " WHERE Concat(p.Name,p.Description,p.Ingredients,p.NutritionalValue) like :Filter;";
        $data = [
            'Filter' => '%' . $this->Filter . '%'
        ];
        $stmt = $this->conn->prepare($query);
        $stmt->execute($data);
        return $stmt;
    }
    function GetProductById()
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
            " LEFT OUTER JOIN productpicture pp on (p.ID = pp.ProductID AND pp.First = 1) " .
            " WHERE p.ID = :ID";
        $data = [
            'ID' => $this->ID
        ];
        $stmt = $this->conn->prepare($query);
        $stmt->execute($data);
        return $stmt;
    }
    function GetProductsByCatalogId()
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
            " LEFT OUTER JOIN productpicture pp on (p.ID = pp.ProductID AND pp.First = 1) " .
            " WHERE p.CatalogID = :CatalogID";
        $data = [
            'CatalogID' => $this->CatalogID
        ];
        $stmt = $this->conn->prepare($query);
        $stmt->execute($data);
        return $stmt;
    }
}
