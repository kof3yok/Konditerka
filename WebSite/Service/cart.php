// Этот фрагмент кода на PHP определяет класс Cart, который представляет корзину покупок пользователя в базе данных.
<?php
require_once 'base.php';

class Cart
{
    private $conn;
    private $table_name = "cart";

    public $ID;
    public $PriceID;
    public $UserID;
    public $Quantity;
    public $CreationDate;

    public function __construct($db)
    {
        $this->conn = $db;
    }
    // Этот метод предназначен для добавления нового товара в корзину. 
    //Он сначала проверяет, существует ли уже запись о данном товаре для данного пользователя в базе данных.
    //Если запись не найдена, то добавляет новую запись о товаре в корзину. Если запись уже существует, то обновляет количество данного товара в корзине.
    function Create() 
    {
        $query = "SELECT ID,Quantity FROM " . $this->table_name . "
        WHERE PriceID=:PriceID AND UserID=:UserID;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':PriceID', $this->PriceID);
        $stmt->bindParam(':UserID', $this->UserID);
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            $query = "INSERT INTO " . $this->table_name . " (
            ID,
            PriceID,
            UserID,
            Quantity,
            CreationDate) VALUES
        (:ID,:PriceID,:UserID,:quantity,:creationdate);";
            $q = $this->conn->prepare($query);
            $data = [
                'ID' => GUID(),
                'PriceID' => $this->PriceID,
                'UserID' => $this->UserID,
                'quantity' => $this->Quantity,
                'creationdate' => date("Y-m-d H:i:s"),
            ];
            $q->execute($data);
        }else{
            $result = $stmt;
            $row = $result->fetch(PDO::FETCH_ASSOC);
            $this->ID=$row['ID'];
            $this->Quantity=$row['Quantity'];
            $this->Quantity=$this->Quantity+1;
            $this->Update();
        }
        return $this->GetAllForUser();
    }
    // Этот метод возвращает все товары в корзине для определенного пользователя. 
    //Он выполняет SQL-запрос, чтобы получить данные о товарах в корзине, включая информацию о самих товарах (название, описание, цена и т.д.).
    function GetAllForUser()
    {
        $query = "SELECT ppp.ImageData, c.ID,
        ct.Name as Catalog,
        p.Name,
        p.Description,
        p.Ingredients,
        p.NutritionalValue,
        p.ID as ProductID,
        c.PriceID,
        pp.Price,
        pp.Name as PriceName,
        c.UserID,
        c.Quantity,
        c.CreationDate FROM " . $this->table_name . " c 
        INNER JOIN productprice pp on c.PriceID = pp.ID
        INNER JOIN product p on pp.ProductID = p.ID
        INNER JOIN catalog ct on p.CatalogID = ct.ID 
        LEFT OUTER JOIN productpicture ppp on (p.ID = ppp.ProductID AND ppp.First = 1) 
        WHERE UserID=:UserID";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':UserID', $this->UserID);
        $stmt->execute();
        return $stmt;
    }
    // Этот метод удаляет определенный товар из корзины пользователя. 
    function Delete()
    {
        $query = "DELETE FROM " . $this->table_name .
            " WHERE ID = :ID AND UserID = :UserID;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ID', $this->ID);
        $stmt->bindParam(':UserID', $this->UserID);
        $stmt->execute();
    }
    // Этот метод удаляет все товары из корзины пользователя.
    function DeleteAll()
    {
        $query = "DELETE FROM " . $this->table_name .
            " WHERE UserID = :UserID;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':UserID', $this->UserID);
        $stmt->execute();
    }
    // Этот метод обновляет количество определенного товара в корзине. 
    function Update()
    {
        $query = "UPDATE " . $this->table_name .
            " SET Quantity =:quantity
            WHERE ID = :ID;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ID', $this->ID);
        $stmt->bindParam(':quantity', $this->Quantity);
        $stmt->execute();
    }
}
