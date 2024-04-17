// Этот код является частью веб-приложения на PHP и представляет собой обработчик POST-запросов, связанных с операциями над ценами товаров.
// Проверяется метод запроса (POST) и наличие определенных ключей в массиве $_POST.
// Далее подключается файл с классом DatabaseConnector, который устанавливает соединение с базой данных, и файл с классом productprice, который, скорее всего, содержит логику работы с ценами продуктов.
// Создается экземпляр класса ProductPrice с переданным объектом базы данных.
// В зависимости от параметров в $_POST объект ProductPrice инициализируется различными данными, такими как ID, ProductID, Name, Description, Price.
// Затем, в зависимости от параметров в $_POST, вызываются различные методы этого объекта: Update(), Create(), Passive(), Active(). Вероятно, методы Update(), Create(), Passive(), Active() выполняют соответственно обновление, создание, отключение и активацию цен продуктов.
// В конце каждого блока происходит перенаправление пользователя на страницу productedit.php с передачей параметра id равного ProductID.

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_update'])) {

    include_once '../../src/System/DatabaseConnector.php';
    include_once '../../Service/productprice.php';
    $database = new Database();
    $db = $database->getConnection();
    $pp = new ProductPrice($db);
    $pp->ID = $_POST["ID"];
    $pp->ProductID = $_POST["ProductID"];
    $pp->Name = $_POST["Name"];
    $pp->Description = $_POST["Description"];
    $pp->Price = $_POST["Price"];
    if ($pp->ID != 0) {
        $pp->Update();
    } else {
        $result = $pp->Create();
        $pp->ID = $result["ID"];
    }
    header('Location: ../productedit.php?id=' . $pp->ProductID);
} else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['price_passive'])) {

    include_once '../../src/System/DatabaseConnector.php';
    include_once '../../Service/productprice.php';
    $database = new Database();
    $db = $database->getConnection();
    $pp = new ProductPrice($db);
    $pp->ID = $_POST["ID"];
    $pp->ProductID = $_POST["ProductID"];
    if ($pp->ID != 0) {
        $pp->Passive();
    } else {
    }
    header('Location: ../productedit.php?id=' . $pp->ProductID);
} else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['price_active'])) {

    include_once '../../src/System/DatabaseConnector.php';
    include_once '../../Service/productprice.php';
    $database = new Database();
    $db = $database->getConnection();
    $pp = new ProductPrice($db);
    $pp->ID = $_POST["ID"];
    $pp->ProductID = $_POST["ProductID"];
    if ($pp->ID != 0) {
        $pp->Active();
    } else {
    }
    header('Location: ../productedit.php?id=' . $pp->ProductID);
}
// В коде есть еще одна функция GetPrice($id), которая используется для получения информации о цене продукта по его ID.
function GetPrice($id)
{
    require_once '../src/System/DatabaseConnector.php';
    require_once '../Service/productprice.php';
    $database = new Database();
    $db = $database->getConnection();
    $pp = new ProductPrice($db);
    $pp->ID = $id;
    return $pp->Get();
}
