// API для взаимодействия с данными пользователя между приложением и БД
<?php
// Настройка заголовков для CORS (Cross-Origin Resource Sharing): 
// Заголовки Access-Control-Allow-Origin, Access-Control-Allow-Methods, Access-Control-Max-Age и Access-Control-Allow-Headers устанавливаются для обеспечения корректной работы междуразделенных запросов.
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
// Подключение необходимых файлов и сервисов: Включаются файлы для подключения к базе данных (DatabaseConnector.php) и сервисы пользователя (user.php) и отправки JSON-ответов (sendJson.php).
include_once '../src/System/DatabaseConnector.php';
include_once '../Service/user.php';
include_once '../Service/sendJson.php';
// Обработка запроса методом POST: Если метод запроса - POST, то код проверяет значение параметра method, переданного в URL-адресе. В зависимости от значения method, выполняется определенное действие.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $method = $_GET['method'];
    switch ($method) {
// Обновление данных пользователя: Если метод POST и параметр method равен 'update', то код пытается обновить данные пользователя. 
// Для этого он получает данные из тела запроса, проверяет их наличие и валидность, затем создает объект пользователя, устанавливает значения его свойств и вызывает метод Update() для обновления данных в базе. 
// В зависимости от результата операции, возвращается соответствующий JSON-ответ.
        case 'update':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->username) ||
                    !isset($data->password) ||
                    !isset($data->email) ||
                    !isset($data->phone) ||
                    !isset($data->ID) ||
                    empty(trim($data->username)) ||
                    empty(trim($data->password)) ||
                    empty(trim($data->email)) ||
                    empty(trim($data->phone)) ||
                    empty(trim($data->ID))
                ) {
// Отправка JSON-ответа: Функция sendJson() используется для отправки JSON-ответа с заданным кодом состояния, сообщением и дополнительными данными.
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['username', 'password', 'email', 'ID']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $user = new User($db);
// Обработка ошибок: Если происходит исключение или запрос не содержит правильного метода или параметра method, возвращается соответствующий JSON-ответ об ошибке.
                    $user->username = trim($data->username);
                    $user->password = trim($data->password);
                    $user->email = trim($data->email);
                    $user->phone = trim($data->phone);
                    $user->ID = trim($data->ID);
                    $result = $user->Update();
                    if ($result === true) {
                        sendJson(200, 'User updated!');
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
