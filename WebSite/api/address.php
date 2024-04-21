// API для взаимодействия с адресами между приложением и БД
<?php
// Настройка заголовков CORS: Устанавливаются заголовки CORS (Cross-Origin Resource Sharing) для обеспечения безопасного взаимодействия между клиентом и сервером. Это позволяет разрешить запросы с других доменов.
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
// Подключение необходимых файлов и классов: Включаются файлы для работы с базой данных (DatabaseConnector.php) и сервис для работы с адресами (address.php) и отправки JSON-ответов (sendJson.php).
include_once '../src/System/DatabaseConnector.php';
include_once '../Service/address.php';
include_once '../Service/sendJson.php';
// Обработка запросов методом POST: Код проверяет, был ли запрос выполнен методом POST. Если да, то он извлекает значение параметра method из строки запроса и выполняет соответствующие действия в зависимости от этого параметра.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $method = $_GET['method'];
    switch ($method) {
// Создание адреса: Если значение method равно 'create', то происходит попытка создания нового адреса на основе данных, полученных из тела запроса. 
// Если обязательные поля не были переданы или их значения пусты, отправляется соответствующий JSON-ответ с кодом ошибки 422.
        case 'create':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->userid) ||
                    !isset($data->address) ||
                    empty(trim($data->userid)) ||
                    empty(trim($data->address))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['user', 'address']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $address = new Address($db);

                    $address->UserID = trim($data->userid);
                    $address->Address = trim($data->address);
                    $result = $address->Create();
                    if ($result === null) {
                        sendJson(200, 'Address cannot created!');
                    } else {
                        sendJson(200, '', [
                            'AddressID' => $result['ID']
                        ]);
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
// Удаление адреса: Если значение method равно 'delete', то происходит попытка удаления адреса на основе данных, полученных из тела запроса. 
// Если обязательные поля не были переданы или их значения пусты, отправляется соответствующий JSON-ответ с кодом ошибки 422.
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
                    $address = new Address($db);

                    $address->UserID = trim($data->userid);
                    $address->ID = trim($data->id);
                    $result = $address->Delete();
                    if ($result === true) {
                        sendJson(200, 'Address is deleted!');
                    } else {
                        sendJson(200, 'Unexcepted Error');
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
// Обновление адреса: Если значение method равно 'update', то происходит попытка обновления адреса на основе данных, полученных из тела запроса. 
// Если обязательные поля не были переданы или их значения пусты, отправляется соответствующий JSON-ответ с кодом ошибки 422.
        case 'update':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->id) ||
                    !isset($data->address) ||
                    empty(trim($data->id)) ||
                    empty(trim($data->address))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['id', 'address']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $address = new Address($db);

                    $address->ID = trim($data->id);
                    $address->Address = trim($data->address);
                    $result = $address->Update();
                    if ($result === true) {
                        sendJson(200, 'Address is updated!');
                    } else {
                        sendJson(200, 'Unexcepted Error');
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
// Отправка JSON-ответа в случае ошибок: Если при выполнении операций возникли исключения или непредвиденные ошибки, сервер отправляет JSON-ответ с соответствующим кодом состояния HTTP (500 для внутренней серверной ошибки) и сообщением об ошибке.
        default:
            sendJson(405, 'Invalid Request Method. HTTP method should be POST');
            break;
    }
}
// Обработка недопустимых запросов: Если запрос не выполнен методом POST или параметр method не определен, сервер отправляет JSON-ответ с кодом состояния HTTP 405 (недопустимый метод запроса) и сообщением об ошибке.
sendJson(405, 'Invalid Request Method. HTTP method should be POST');
