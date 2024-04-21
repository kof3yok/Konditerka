// API для взаимодействия с ценами товаров между приложением и БД
<?php
// Установка заголовков CORS: Устанавливает заголовки CORS (Cross-Origin Resource Sharing), которые позволяют браузеру выполнить запрос к серверу с другого источника.
// Обработка запросов различных методов: Принимает запросы с методами OPTIONS, GET, POST, PUT, DELETE.
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
// Подключение необходимых файлов и классов: Включает файлы DatabaseConnector.php, productprice.php и sendJson.php, которые содержат необходимые классы и функции для работы с базой данных и отправки JSON-ответов.
include_once '../src/System/DatabaseConnector.php';
include_once '../Service/productprice.php';
include_once '../Service/sendJson.php';
// Обработка POST-запросов: В зависимости от параметра method, определенного в запросе, выполняет соответствующие действия. Параметр method передается через GET-параметр.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $method = $_GET['method'];
    switch ($method) {
// Обработка создания новой записи: Проверяет наличие всех обязательных полей в JSON-данных, полученных из тела запроса. Если все поля присутствуют, создает новую запись в базе данных с помощью метода Create() класса ProductPrice.
        case 'create':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->ProductID) ||
                    !isset($data->Name) ||
                    !isset($data->Description) ||
                    !isset($data->Price) ||
                    !isset($data->Status) ||
                    empty(trim($data->ProductID)) ||
                    empty(trim($data->Name)) ||
                    empty(trim($data->Description)) ||
                    empty(trim($data->Price)) ||
                    empty(trim($data->Status))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => [
                        'ProductID',
                        'Name',
                        'Description',
                        'Price',
                        'Status'
                    ]]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $pp = new ProductPrice($db);

                    $pp->ProductID = trim($data->ProductID);
                    $pp->Name = trim($data->Name);
                    $pp->Description = trim($data->$Description);
                    $pp->Price = trim($data->Price);
                    $pp->Status = trim($data->Status);
                    $result = $pp->Create();
                    if ($result === null) {
                        sendJson(200, 'Product Price cannot created!');
                    } else {
                        sendJson(200, '', [
                            'ProductPriceID' => $result['ID']
                        ]);
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
// Обработка удаления записи: Проверяет наличие всех обязательных полей в JSON-данных, полученных из тела запроса. Если все поля присутствуют, удаляет запись из базы данных с помощью метода Delete() класса ProductPrice.
        case 'delete':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->ProductID) ||
                    !isset($data->ID) ||
                    empty(trim($data->ProductID)) ||
                    empty(trim($data->ID))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['ProductID', 'ID']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $pp = new ProductPrice($db);

                    $pp->ProductID = trim($data->ProductID);
                    $pp->ID = trim($data->id);
                    $result = $pp->Delete();
                    if ($result === true) {
                        sendJson(200, 'Product Price is deleted!');
                    } else {
                        sendJson(200, 'Unexcepted Error');
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
// Обработка обновления записи: Проверяет наличие всех обязательных полей в JSON-данных, полученных из тела запроса. Если все поля присутствуют, обновляет запись в базе данных с помощью метода Update() класса ProductPrice.
        case 'update':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->ID) ||
                    !isset($data->Name) ||
                    !isset($data->Description) ||
                    !isset($data->Price) ||
                    !isset($data->Status) ||
                    empty(trim($data->ID)) ||
                    empty(trim($data->Name)) ||
                    empty(trim($data->Description)) ||
                    empty(trim($data->Price)) ||
                    empty(trim($data->Status))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => [
                        'ID',
                        'Name',
                        'Description',
                        'Price',
                        'Status'
                    ]]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $pp = new ProductPrice($db);

                    $pp->ID = trim($data->ID);
                    $pp->Name = trim($data->Name);
                    $pp->Description = trim($data->Description);
                    $pp->Price = trim($data->Price);
                    $pp->Status = trim($data->Status);
                    $result = $pp->Update();
                    if ($result === true) {
                        sendJson(200, 'Product Price is updated!');
                    } else {
                        sendJson(200, 'Unexcepted Error');
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
// Обработка активации и деактивации записи: Проверяет наличие всех обязательных полей в JSON-данных, полученных из тела запроса. 
// Если все поля присутствуют, активирует или деактивирует запись в базе данных с помощью методов Active() и Passive() класса ProductPrice.
        case 'active':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->ID) ||
                    empty(trim($data->ID))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['ID']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $pp = new ProductPrice($db);

                    $pp->ID = trim($data->ID);
                    $result = $pp->Active();
                    if ($result === true) {
                        sendJson(200, 'Product Picture is actived!');
                    } else {
                        sendJson(200, 'Unexcepted Error');
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
        case 'passive':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->ID) ||
                    empty(trim($data->ID))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['ID']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $pp = new ProductPrice($db);

                    $pp->ID = trim($data->ID);
                    $result = $pp->Passive();
                    if ($result === true) {
                        sendJson(200, 'Product Picture is passived!');
                    } else {
                        sendJson(200, 'Unexcepted Error');
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
// Обработка запроса на получение всех записей по ID продукта: Проверяет наличие всех обязательных полей в JSON-данных, полученных из тела запроса. 
// Если все поля присутствуют, получает все записи из базы данных по ID продукта с помощью метода GetAllForProduct() класса ProductPrice и возвращает их в формате JSON.
        case 'getallbyproductid':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->ProductID) ||
                    empty(trim($data->ProductID))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['ProductID']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $pp = new ProductPrice($db);

                    $pp->ProductID = trim($data->ProductID);
                    $result = $pp->GetAllForProduct();
                    $num = $result->rowCount();
// Отправка JSON-ответов: Функция sendJson() используется для отправки ответов в формате JSON с указанными HTTP-статусами и данными
                    if ($num > 0) {
                        $pp_arr = array();
                        $pp_arr["records"] = array();
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                            $pp_item = array(
                                "ID" =>   $row['ID'],
                                "ProductID" => $row['ProductID'],
                                "Name" => $row['Name'],
                                "Description" => $row['Description'],
                                "Price" => $row['Price'],
                                "Status" => $row['Status'],
                                "CreationDate" => $row['CreationDate'],
                            );
                            array_push($pp_arr["records"], $pp_item);
                        }
                        sendJson(200, '', $pp_arr);
                    } else {
                        sendJson(200, 'Unexcepted Error');
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
sendJson(405, 'Invalid Request Method. HTTP method should be POST');
