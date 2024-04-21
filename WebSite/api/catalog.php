// // API для взаимодействия с категориями товаров между приложением и БД
<?php
// Установка заголовков CORS: Устанавливает заголовки CORS (Cross-Origin Resource Sharing), чтобы разрешить доступ к ресурсам из разных источников. 
// Это позволяет скриптам на одном домене взаимодействовать с ресурсами на другом домене без нарушения политики безопасности браузера.
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
// Подключение необходимых файлов и классов: Включает файлы DatabaseConnector.php, catalog.php и sendJson.php, которые, вероятно, содержат определения классов и функций, необходимых для работы с базой данных и отправки JSON-ответов.
include_once '../src/System/DatabaseConnector.php';
include_once '../Service/catalog.php';
include_once '../Service/sendJson.php';
// Обработка запросов методом POST: Если запрос был выполнен методом POST, код проверяет значение параметра method в URL-адресе запроса и в зависимости от этого выполняет одно из следующих действий: создание, удаление, обновление или получение всех записей каталога.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $method = $_GET['method'];
    switch ($method) {
// create: Создает новый каталог. Проверяет наличие всех обязательных полей (в данном случае только name). Если поля не заполнены, возвращает ошибку 422. После этого создает каталог и возвращает его ID.
        case 'create':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->name) ||
                    empty(trim($data->name))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['name']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $catalog = new Catalog($db);

                    $catalog->Name = trim($data->name);
                    $result = $catalog->Create();
                    if ($result === null) {
                        sendJson(200, 'Catalog cannot created!');
                    } else {
                        sendJson(200, '', [
                            'catalogID' => $result['ID']
                        ]);
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
// delete: Удаляет каталог по указанному ID. Проверяет наличие обязательного поля id. Если поле не указано, возвращает ошибку 422.
        case 'delete':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->id) ||
                    empty(trim($data->id))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['user', 'ID']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $catalog = new Catalog($db);

                    $catalog->ID = trim($data->id);
                    $result = $catalog->Delete();
                    if ($result === true) {
                        sendJson(200, 'Catalog is deleted!');
                    } else {
                        sendJson(200, 'Unexcepted Error');
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
// update: Обновляет информацию о каталоге по указанному ID. Проверяет наличие обязательных полей id и name. Если поля не указаны, возвращает ошибку 422.
        case 'update':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->id) ||
                    !isset($data->name) ||
                    empty(trim($data->id)) ||
                    empty(trim($data->name))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['id', 'name']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $catalog = new Catalog($db);

                    $catalog->ID = trim($data->id);
                    $catalog->Name = trim($data->name);
                    $result = $catalog->Update();
                    if ($result === true) {
                        sendJson(200, 'Catalog is updated!');
                    } else {
                        sendJson(200, 'Unexcepted Error');
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
// getall: Получает все каталоги. Возвращает JSON-список всех каталогов товаров.
        case 'getall':
            try {
                $data = json_decode(file_get_contents('php://input'));

                $database = new Database();
                $db = $database->getConnection();
                $catalog = new Catalog($db);

                $result = $catalog->GetAll();
                $num = $result->rowCount();

                if ($num > 0) {
                    $catalog_arr = array();
                    $catalog_arr["records"] = array();
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        // extract($row);
                        $catalog_item = array(
                            "ID" => $row['ID'],
                            "Name" => $row['Name']
                        );
                        array_push($catalog_arr["records"], $catalog_item);
                    }
                    sendJson(200, '', $catalog_arr);
                } else {
                    sendJson(200, 'Unexcepted Error');
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
        default:
// Обработка GET-запросов: В настоящем коде нет обработки GET-запросов, поэтому возвращается ошибка 405.
// Отправка JSON-ответов: Функция sendJson используется для отправки JSON-ответов с соответствующим кодом состояния и данными.
            sendJson(405, 'Invalid Request Method. HTTP method should be POST');
            break;
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
}
// Обработка ошибок: Если происходит исключение (например, ошибка в работе с базой данных), возвращается соответствующий код состояния и сообщение об ошибке.
sendJson(405, 'Invalid Request Method. HTTP method should be POST');
