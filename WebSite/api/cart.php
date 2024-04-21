// API для взаимодействия с корзиной между приложением и БД
<?php
// Установка заголовков CORS: Устанавливает заголовки для обеспечения Cross-Origin Resource Sharing (CORS). Это позволяет разрешить запросы с других источников, кроме источника, с которого была загружена веб-страница.
// Включение заголовков запроса: Устанавливает разрешенные методы запросов (OPTIONS, GET, POST, PUT, DELETE) и другие заголовки, необходимые для взаимодействия с ресурсом.
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
// Подключение необходимых файлов и классов: Включает файлы DatabaseConnector.php, cart.php и sendJson.php, которые содержат необходимые классы и функции для работы с базой данных и отправки JSON-ответов.
include_once '../src/System/DatabaseConnector.php';
include_once '../Service/cart.php';
include_once '../Service/sendJson.php';
// Обработка POST-запросов: Проверяет тип запроса (POST) и обрабатывает его содержимое в зависимости от переданного значения параметра method.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $method = $_GET['method'];
    switch ($method) {
// Обработка запроса на создание элемента корзины (create): Создает новую запись в корзине на основе данных, переданных в теле запроса JSON. Проверяет наличие обязательных полей и создает новую запись в базе данных.
        case 'create':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->userid) ||
                    !isset($data->priceid) ||
                    !isset($data->quantity) ||
                    empty(trim($data->userid)) ||
                    empty(trim($data->priceid)) ||
                    empty(trim($data->quantity))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['user', 'priceid', 'quantity']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $cart = new Cart($db);

                    $cart->PriceID = trim($data->priceid);
                    $cart->UserID = trim($data->userid);
                    $cart->Quantity = trim($data->quantity);
                    $result = $cart->Create();

                    $cart_arr = array();
                    $cart_arr["records"] = array();
                    sendJson(200, '', $cart_arr);
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
// Обработка запроса на удаление элемента корзины (delete): Удаляет элемент корзины по переданным идентификаторам пользователя и элемента.
        case 'delete':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->userid) ||
                    !isset($data->id) ||
                    empty(trim($data->userid)) ||
                    empty(trim($data->id))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['user', 'ID']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $cart = new Cart($db);

                    $cart->UserID = trim($data->userid);
                    $cart->ID = trim($data->id);
                    $result = $cart->Delete();

                    $cart_arr = array();
                    sendJson(200, '', $cart_arr);
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
// Обработка запроса на обновление элемента корзины (update): Обновляет данные элемента корзины по переданным идентификатору элемента и количеству.
        case 'update':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->id) ||
                    !isset($data->quantity) ||
                    empty(trim($data->id)) ||
                    empty(trim($data->quantity))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['id', 'address']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $cart = new Cart($db);

                    $cart->ID = trim($data->id);
                    $cart->Quantity = trim($data->quantity);
                    $result = $cart->Update();

                    $cart_arr = array();
                    $cart_arr["records"] = array();
                    sendJson(200, '', $cart_arr);
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
// Обработка запроса на получение всех элементов корзины пользователя (getall): Получает все элементы корзины для указанного пользователя. Возвращает массив объектов корзины в формате JSON.
        case "getall":
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->token) ||
                    empty(trim($data->token))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['user']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $cart = new Cart($db);

                    $cart->UserID = trim($data->token);
                    $result = $cart->GetAllForUser();
                    $num = $result->rowCount();

                    $cart_arr = array();
                    $cart_arr["records"] = array();
                    if ($num > 0) {
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                            // extract($row);
                            $cart_item = array(
                                "Image" => $row["ImageData"],
                                "ID" => $row["ID"],
                                "Catalog" => $row["Catalog"],
                                "Name" => $row["Name"],
                                "Description" => $row["Description"],
                                "Ingredients" => $row["Ingredients"],
                                "NutritionalValue" => $row["NutritionalValue"],
                                "ProductID" => $row["ProductID"],
                                "PriceID" => $row["PriceID"],
                                "PriceName" => $row["PriceName"],
                                "Price" => $row["Price"],
                                "Quantity" => $row["Quantity"],
                                "UserID" => $row["UserID"],
                                "CreationDate " >= $row["CreationDate"],
                            );
                            array_push($cart_arr["records"], $cart_item);
                        }
                        sendJson(200, '', $cart_arr);
                    } else {
                        sendJson(200, '',  $cart_arr);
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
        default:
            sendJson(405, 'Invalid Request Method. HTTP method should be POST');
            break;
    }
}
// Обработка ошибок: Обрабатывает исключения и возвращает соответствующие коды состояния HTTP и сообщения об ошибке в случае возникновения ошибок во время обработки запроса.
sendJson(405, 'Invalid Request Method. HTTP method should be POST');
