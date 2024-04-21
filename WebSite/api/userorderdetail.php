// API для взаимодействия с деталями историй заказов пользователя между приложением и БД
<?php
// Установка заголовков CORS: Устанавливаются заголовки для обеспечения кросс-доменных запросов (CORS). Это позволяет другим доменам отправлять запросы к этому серверу.
// Включение поддержки методов HTTP: Устанавливаются заголовки, определяющие поддержку различных HTTP-методов (OPTIONS, GET, POST, PUT, DELETE).
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
// Подключение необходимых файлов и классов: Включаются файлы с определениями классов для работы с базой данных и сервисными функциями.
include_once '../src/System/DatabaseConnector.php';
include_once '../Service/userorderdetail.php';
include_once '../Service/cart.php';
include_once '../Service/sendJson.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $method = $_GET['method'];
    switch ($method) {
// Создание заказа: Если запрос имеет метод create, создается новый заказ на основе данных, полученных из тела запроса. 
// Данные валидируются, затем создается экземпляр класса UserOrderDetail и вызывается метод Create(). Результат операции возвращается в виде JSON.
        case 'create':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->OrderID) ||
                    !isset($data->ProductID) ||
                    !isset($data->Quantity) ||
                    !isset($data->Price) ||
                    empty(trim($data->OrderID)) ||
                    empty(trim($data->ProductID)) ||
                    empty(trim($data->Quantity)) ||
                    empty(trim($data->Price))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => [
                        'OrderID',
                        'ProductID',
                        'Quantity',
                        'Price'
                    ]]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $userorderdetail = new UserOrderDetail($db);

                    $userorderdetail->OrderID = trim($data->OrderID);
                    $userorderdetail->ProductID = trim($data->ProductID);
                    $userorderdetail->Quantity = trim($data->Quantity);
                    $userorderdetail->Price = trim($data->Price);
                    $result = $userorderdetail->Create();
                    if ($result === null) {
                        sendJson(200, 'Order cannot created!');
                    } else {
                        sendJson(200, '', [
                            'OrderID' => $result['ID']
                        ]);
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
// Создание всех заказов: Если запрос имеет метод createall, создаются несколько заказов на основе данных, полученных из тела запроса. 
// Данные валидируются, создается экземпляр класса UserOrderDetail, вызывается метод CreateAll(), а затем удаляются все товары из корзины. Результат операции возвращается в виде JSON.
        case 'createall':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->records)
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => [
                        'Records'
                    ]]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $userorderdetail = new UserOrderDetail($db);
                    $cart = new Cart($db);
                    $cart->UserID = $data->UserID;
                    $result = $userorderdetail->CreateAll($data->records);
                    if ($result === null) {
                        sendJson(200, 'Orders cannot created!');
                    } else {
                        $cart->DeleteAll();
                        sendJson(200, 'Orders created!');
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
// Получение всех товаров по заказу: Если запрос имеет метод getall, возвращаются все товары, относящиеся к определенному заказу. 
// Данные валидируются, создается экземпляр класса UserOrderDetail, вызывается метод GetAllByOrder(), данные форматируются и возвращаются в виде JSON.
        case 'getall':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->OrderID) ||
                    empty(trim($data->OrderID))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['OrderID']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $userorderdetail = new UserOrderDetail($db);

                    $userorderdetail->OrderID = trim($data->OrderID);
                    $result = $userorderdetail->GetAllByOrder();
                    $num = $result->rowCount();

                    $userorder_arr = array();
                    $userorder_arr["records"] = array();
                    if ($num > 0) {
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                            // extract($row);
                            $userorder_item = array(
                                "Image" => $row["ImageData"],
                                "ID" => $row["ID"],
                                "Catalog" => $row["Catalog"],
                                "Name" => $row["Name"],
                                "Description" => $row["Description"],
                                "Ingredients" => $row["Ingredients"],
                                "NutritionalValue" => $row["NutritionalValue"],
                                "ProductID" => $row["ProductID"],
                                "Price" => $row["Price"],
                                "Quantity" => $row["Quantity"],
                                "CreationDate " >= $row["CreationDate"],
                            );
                            array_push($userorder_arr["records"], $userorder_item);
                        }
                        sendJson(200, '', $userorder_arr);
                    } else {
                        sendJson(200, 'Unexcepted Error');
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
        default:
// Обработка ошибок: Если происходят ошибки в процессе выполнения запроса (например, неправильные данные, ошибка сервера и т. д.), отправляется соответствующий статус ошибки и сообщение об ошибке в виде JSON.
            sendJson(405, 'Invalid Request Method. HTTP method should be POST');
            break;
    }
}
//Завершение скрипта при некорректном методе запроса: Если метод запроса не POST, отправляется статус ошибки и сообщение об ошибке в виде JSON.
sendJson(405, 'Invalid Request Method. HTTP method should be POST');
