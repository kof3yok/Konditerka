// API для взаимодействия с товарами между приложением и БД
<?php
// Установка заголовков CORS (Cross-Origin Resource Sharing): Этот блок кода устанавливает необходимые заголовки для поддержки CORS, что позволяет выполнение запросов из разных источников.
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
// Подключение необходимых файлов и классов: Код подключает файлы DatabaseConnector.php, hitproduct.php и sendJson.php, которые, вероятно, содержат определения классов для работы с базой данных и обработки запросов.
include_once '../src/System/DatabaseConnector.php';
include_once '../Service/hitproduct.php';
include_once '../Service/sendJson.php';
// Обработка запросов: Код определяет метод HTTP-запроса и в зависимости от этого метода и параметра method в запросе выполняет соответствующие действия:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $method = $_GET['method'];
    switch ($method) {
// Создание записи: Если метод запроса - POST и параметр method равен 'create', создается новая запись с использованием данных из тела запроса. 
// Если все необходимые поля заполнены, создается новая запись в базе данных и возвращается ответ со статусом 200 и данными о созданной записи. 
// В противном случае возвращается сообщение об ошибке и статус 422 (непроцессируемая сущность).
        case 'create':
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
                    $hitproduct = new HitProduct($db);

                    $hitproduct->ProductID = trim($data->ProductID);
                    $result = $hitproduct->Create();
                    if ($result === null) {
                        sendJson(200, 'HitProduct cannot created!');
                    } else {
                        sendJson(200, '', [
                            'ProductID' => $result['ID']
                        ]);
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
// Удаление записи: Если метод запроса - POST и параметр method равен 'delete', удаляется запись с идентификатором, переданным в теле запроса. 
// Если удаление прошло успешно, возвращается ответ со статусом 200 и сообщением об успешном удалении. В противном случае возвращается сообщение об ошибке и статус 200.
        case 'delete':
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
                    $hitproduct = new HitProduct($db);

                    $hitproduct->ID = trim($data->id);
                    $result = $hitproduct->Delete();
                    if ($result === true) {
                        sendJson(200, 'HitProduct is deleted!');
                    } else {
                        sendJson(200, 'Unexcepted Error');
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
// Получение всех записей: Если метод запроса - POST и параметр method равен 'getall', получаются все записи из базы данных и возвращаются в формате JSON. 
// Если записи успешно получены, возвращается ответ со статусом 200 и данными о записях. В противном случае возвращается сообщение об ошибке и статус 200.
        case 'getall':
            try {
                $data = json_decode(file_get_contents('php://input'));

                $database = new Database();
                $db = $database->getConnection();
                $hitproduct = new HitProduct($db);

                $result = $hitproduct->GetAll();
                $num = $result->rowCount();

                if ($num > 0) {
                    $catalog_arr = array();
                    $catalog_arr["records"] = array();
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        // extract($row);
                        $catalog_item = array(
                            "ID" => $row['ID'],
                            "CatalogID" => $row['CatalogID'],
                            "Catalog" => $row['Catalog'],
                            "Name" => $row['Name'],
                            "Description" => $row['Description'],
                            "Ingredients" => $row['Ingredients'],
                            "NutritionalValue" => $row['NutritionalValue'],
                            "Price1" => $row['Price1'],
                            "Price1ID" => $row['Price1ID'],
                            "Price2" => $row['Price2'],
                            "Price2ID" => $row['Price2ID'],
                            "Image" => $row['ImageData'],
                        );
                        array_push($catalog_arr["records"], $catalog_item);
                    }
                    sendJson(200, '', $catalog_arr);
                } else {
                    sendJson(200, 'Unexcepted Error');
                }
            } catch (\Throwable $th) {
            // Обработка исключений: В случае возникновения исключения во время выполнения кода, возвращается сообщение об ошибке и статус 500 (внутренняя ошибка сервера).
                sendJson(500, 'Internal Server Error');
            }
            break;
        default:
            sendJson(405, 'Invalid Request Method. HTTP method should be POST');
            break;
    }
}
sendJson(405, 'Invalid Request Method. HTTP method should be POST');
