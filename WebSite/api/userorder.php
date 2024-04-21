// API для взаимодействия с историей заказов пользователя между приложением и БД
<?php
// Настройка заголовков для CORS (Cross-Origin Resource Sharing): 
// Заголовки Access-Control-Allow-Origin, Access-Control-Allow-Methods, Access-Control-Max-Age и Access-Control-Allow-Headers устанавливаются для обеспечения доступа к ресурсам из другого источника. 
// Это позволяет обрабатывать запросы из разных источников.
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
// Подключение необходимых файлов и классов: Файлы DatabaseConnector.php, userorder.php и sendJson.php включаются с помощью оператора include_once. 
// Вероятно, они содержат код для подключения к базе данных, работы с заказами пользователей и отправки JSON-ответов соответственно.
include_once '../src/System/DatabaseConnector.php';
include_once '../Service/userorder.php';
include_once '../Service/sendJson.php';
// Обработка запроса методом POST: Код проверяет, был ли запрос выполнен методом POST. Если да, то выполняется следующее:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
// Определение метода обработки: Параметр method передается в запросе GET и определяет, какую операцию нужно выполнить (например, создание заказа или получение всех заказов пользователя).
    $method = $_GET['method'];
    switch ($method) {
// Обработка операции создания заказа (create): Если method равен 'create', данные из тела запроса JSON декодируются и проверяются на наличие обязательных полей. 
// Затем создается новый объект заказа пользователя (UserOrder) с полученными данными. Если заказ успешно создан, возвращается JSON-ответ с HTTP-кодом 200 и информацией о созданном заказе.
        case 'create':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->UserID) ||
                    !isset($data->Price) ||
                    empty(trim($data->UserID)) ||
                    empty(trim($data->Price))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['UserID', 'Price']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $userorder = new UserOrder($db);

                    $userorder->UserID = trim($data->UserID);
                    $userorder->Price = trim($data->Price);
                    $result = $userorder->Create();
                    if ($result === null) {
                        sendJson(200, 'Order cannot created!');
                    } else {
                        $pp_arr = array();
                        $pp_arr["records"] = array();
                        $pp_item = array(
                            "ID" => $result['ID']
                        );
                        array_push($pp_arr["records"], $pp_item);
                        sendJson(200, '', $pp_arr);
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
// Обработка операции получения всех заказов пользователя (getall): Если method равен 'getall', также происходит проверка наличия обязательного поля UserID. 
// Затем выполняется запрос к базе данных для получения всех заказов пользователя с указанным ID. Результаты запроса форматируются в массив JSON и возвращаются в ответе. Если заказов нет, возвращается пустой массив.
        case 'getall':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->UserID) ||
                    empty(trim($data->UserID))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['UserID']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $userorder = new UserOrder($db);

                    $userorder->UserID = trim($data->UserID);
                    $result = $userorder->GetAllByUser();
                    $num = $result->rowCount();
    
                    $userorder_arr = array();
                    $userorder_arr["records"] = array();
                    if ($num > 0) {
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                            $userorder_item = array(
                                "ID" => $row['ID'],
                                "UserID" => $row['UserID'],
                                "Price" => $row['Price'],
                                "CreationDate" => $row['CreationDate'],
                                "Address" => $row['Address'],
                                "Status" => $row['Status'],
                                "Driver" => $row['Driver']
                            );
                            array_push($userorder_arr["records"], $userorder_item);
                        }
                        sendJson(200, '', $userorder_arr);
                    } else {
                        sendJson(200, '', $userorder_arr);
                    }
                }
            } catch (\Throwable $th) {
// Обработка ошибок: В случае возникновения ошибок при выполнении операций (например, ошибок базы данных), возвращается соответствующий JSON-ответ с кодом ошибки 500 (Internal Server Error).
                sendJson(500, 'Internal Server Error');
            }
            break;
        default:
// Возврат ответа при недопустимом методе: Если запрос не выполнен методом POST, возвращается JSON-ответ с кодом ошибки 405 (Method Not Allowed).
            sendJson(405, 'Invalid Request Method. HTTP method should be POST');
            break;
    }
}
sendJson(405, 'Invalid Request Method. HTTP method should be POST');
