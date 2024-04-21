// API для взаимодействия с данными об адресе заказа между приложением и БД
<?php
// Установка заголовков CORS: Устанавливаются заголовки CORS (Cross-Origin Resource Sharing) для обеспечения доступа к ресурсам с другого источника. 
// Это делается с помощью функции header() для разрешения доступа к ресурсу из любого источника (*), установки типа содержимого и разрешения различных методов запроса.
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
// Включение необходимых файлов и классов: Включаются файлы, содержащие классы для работы с базой данных и другие служебные классы для обработки данных и отправки JSON-ответов.
include_once '../src/System/DatabaseConnector.php';
include_once '../Service/orderaddress.php';
include_once '../Service/sendJson.php';
// Обработка POST-запроса: Проверяется метод запроса, и если он POST, то происходит обработка запроса. Данные из тела запроса декодируются из JSON в объект PHP.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $method = $_GET['method'];
    switch ($method) {
// Обработка запроса на создание адреса заказа: Если в запросе передан параметр method со значением create, выполняется создание адреса заказа. 
// Проверяется, чтобы были заполнены все обязательные поля (OrderID и Address). Если поля не заполнены, возвращается ответ с кодом 422 и сообщением об ошибке.
        case 'create':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->OrderID) ||
                    !isset($data->Address) ||
                    empty(trim($data->OrderID)) ||
                    empty(trim($data->Address))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['OrderID', 'Address']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $address = new OrderAddress($db);
// Выполнение создания адреса заказа: Создается соединение с базой данных, создается объект OrderAddress и вызывается метод Create() для создания нового адреса заказа. 
// Если создание успешно, возвращается ответ с кодом 200 и идентификатором нового адреса заказа. В противном случае возвращается ответ с кодом 200 и сообщением об ошибке.
                    $address->OrderID = trim($data->OrderID);
                    $address->Address = trim($data->Address);
                    $result = $address->Create();
                    if ($result === null) {
                        sendJson(200, 'Order Address cannot created!');
                    } else {
                        sendJson(200, '', [
                            'OrderAddressID' => $result['ID']
                        ]);
                    }
                }
            } catch (\Throwable $th) {
// Обработка исключений: В случае возникновения ошибки во время выполнения запроса, например, если не удается подключиться к базе данных, возвращается ответ с кодом 500 и сообщением об ошибке сервера.
                sendJson(500, 'Internal Server Error');
            }
            break;
        default:
// Обработка неправильных запросов: Если метод запроса не POST или не указан параметр method, возвращается ответ с кодом 405 и сообщением о неверном методе запроса.
            sendJson(405, 'Invalid Request Method. HTTP method should be POST');
            break;
    }
}
// Завершение скрипта: После обработки запроса в случае, если запрос был POST, скрипт завершается, чтобы избежать выполнения ненужного кода. Если запрос не был POST, возвращается ответ с кодом 405 и сообщением о неверном методе запроса.
sendJson(405, 'Invalid Request Method. HTTP method should be POST');
