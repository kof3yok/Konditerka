// Этот код представляет класс Product, который предположительно используется для взаимодействия с базой данных для управления товарами.
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
// Конструктор класса, который принимает объект базы данных $db и устанавливает его в качестве свойства $conn для дальнейшего использования.
    public function __construct($db)
    {
        $this->conn = $db;
    }
    // Создает новый продукт в базе данных, используя данные объекта Product. 
    // Для этого подготавливается SQL-запрос на вставку (INSERT) данных в таблицу продуктов. После выполнения запроса возвращается идентификатор (ID) созданного продукта.
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
// Устанавливает статус продукта на неактивный (0) в базе данных, указывая, что продукт удален.
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
// Устанавливает статус продукта на активный (1) в базе данных.
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
    // Обновляет данные продукта в базе данных на основе предоставленных данных объекта Product.
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
    // Получает список всех продуктов из базы данных, учитывая фильтр, если он предоставлен.
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
    // Получает данные о продукте по его ID из базы данных. Включает информацию о продукте, каталоге, изображении (если есть) и цене (если есть).
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
    // Получает список продуктов из базы данных на основе ID каталога, указанного в объекте Product. Включает информацию о продукте, каталоге, изображении (если есть) и цене (если есть).
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
