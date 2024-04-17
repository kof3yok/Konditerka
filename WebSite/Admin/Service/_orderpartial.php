// Этот PHP-код представляет собой обработчик запросов, связанных с заказами пользователей.
// Сначала он проверяет, был ли отправлен POST-запрос, и проверяет, какая кнопка была нажата в форме (accept, reject, driver, delivered).
// Для каждой кнопки он включает файлы с классами DatabaseConnector и UserOrder, затем создает объект класса UserOrder, передает соответствующие данные (ID заказа и статус) и вызывает соответствующий метод (UpdateStatus() или Sent()).
// После успешного выполнения операции он перенаправляет пользователя на страницу orderedit.php с идентификатором измененного заказа.
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accept'])) {

    include_once '../../src/System/DatabaseConnector.php';
    include_once '../../Service/userorder.php';
    $database = new Database();
    $db = $database->getConnection();
    $uo = new UserOrder($db);
    $uo->ID = $_POST["ID"];
    $uo->Status = 1;
    $uo->UpdateStatus();
    header('Location: ../orderedit.php?id=' . $uo->ID);
} else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reject'])) {

    include_once '../../src/System/DatabaseConnector.php';
    include_once '../../Service/userorder.php';
    $database = new Database();
    $db = $database->getConnection();
    $uo = new UserOrder($db);
    $uo->ID = $_POST["ID"];
    $uo->Status = 4;
    $uo->UpdateStatus();
    header('Location: ../orderedit.php?id=' . $uo->ID);
} else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['driver'])) {

    include_once '../../src/System/DatabaseConnector.php';
    include_once '../../Service/userorder.php';
    $database = new Database();
    $db = $database->getConnection();
    $uo = new UserOrder($db);
    $uo->ID = $_POST["ID"];
    $uo->Status = 2;
    $uo->Driver = $_POST['Driver'];
    $uo->Sent();
    header('Location: ../orderedit.php?id=' . $uo->ID);
} else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delivered'])) {

    include_once '../../src/System/DatabaseConnector.php';
    include_once '../../Service/userorder.php';
    $database = new Database();
    $db = $database->getConnection();
    $uo = new UserOrder($db);
    $uo->ID = $_POST["ID"];
    $uo->Status = 3;
    $uo->UpdateStatus();
    header('Location: ../orderedit.php?id=' . $uo->ID);
}
// Get($id): Получает данные конкретного заказа и его деталей по его идентификатору. 
// Эта функция использует объекты классов DatabaseConnector и UserOrderDetail для получения информации о заказе и его деталях.
function Get($id)
{
    // Get user input
    require_once '../src/System/DatabaseConnector.php';
    require_once '../Service/userorder.php';
    require_once '../Service/userorderdetail.php';
    $database = new Database();
    $db = $database->getConnection();
    $uo = new UserOrder($db);
    $uod = new UserOrderDetail($db);
    $uo->ID = $id;
    $order = $uo->Get();
    $uod->OrderID = $id;
    $orderdetail = $uod->GetAllByOrder();
    $result = array();
    $result['order'] = $order;
    $result["orderdetail"] = array();
    $num = $orderdetail->rowCount();
    if ($num > 0) {
        while ($row = $orderdetail->fetch(PDO::FETCH_ASSOC)) {
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
            array_push($result["orderdetail"], $all_item);
        }
    } else {
        $result["orderdetail"] = null;
    }
    return $result;
}
// Получает все заказы пользователей с учетом фильтра (если он указан). 
// Эта функция использует объект класса DatabaseConnector для получения доступа к базе данных и объект класса UserOrder для выполнения запроса на получение всех заказов.
function GetAll($filter)
{
    // Get user input
    require_once '../src/System/DatabaseConnector.php';
    require_once '../Service/userorder.php';
    $database = new Database();
    $db = $database->getConnection();
    $uo = new UserOrder($db);
    return $uo->GetAll($filter);
}
