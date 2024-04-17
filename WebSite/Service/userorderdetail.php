// Этот код - часть PHP-класса UserOrderDetail, который, вероятно, отвечает за работу с деталями заказов пользователей в некотором приложении или системе.
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
    // Устанавливает соединение с базой данных и присваивает его переменной $conn.
    public function __construct($db)
    {
        $this->conn = $db;
    }
    // Добавляет новую запись в таблицу userorderdetail.
    // Генерирует уникальный идентификатор для записи с помощью функции GUID().
    // Использует подготовленный запрос для вставки данных в таблицу.
    // Возвращает идентификатор только что созданной записи.
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
    // Похож на Create, но принимает список объектов и создает записи для каждого из них.
    // Обрабатывает возможные исключения, которые могут возникнуть в процессе выполнения операций вставки.
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
    // Получает все детали заказа для конкретного заказа.
    // Использует JOIN для объединения таблиц userorderdetail, Product и Catalog, чтобы получить дополнительные данные о продукте и каталоге.
    // Возвращает результат выполнения запроса.
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
    // Получает общее количество заказов и общую сумму для определенного продукта.
    // Возвращает результат выполнения запроса.
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
}
