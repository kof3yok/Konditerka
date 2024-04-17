// Этот код представляет класс OrderAddress, который предположительно используется для управления адресами заказов в базе данных
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
// Конструктор принимает объект базы данных     и устанавливает его в свойство $conn для последующего использования.
    public function __construct($db)
    {
        $this->conn = $db;
    }
// Метод Create() создает новую запись в таблице orderaddress с данными, указанными в свойствах объекта.
// Прежде чем вставить адрес в базу данных, он очищается от потенциально опасных символов с помощью htmlspecialchars(strip_tags($this->Address)).
// Генерируется уникальный идентификатор (GUID()) и текущая дата и время для поля ID и CreationDate соответственно.
// Вставленные данные затем выбираются обратно, чтобы вернуть ID новой записи.
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
