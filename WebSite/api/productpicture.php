// API для взаимодействия с изображением товаров между приложением и БД
<?php
// Установка заголовков CORS: Устанавливает заголовки Access-Control-Allow-Origin, Content-Type, Access-Control-Allow-Methods, Access-Control-Max-Age и Access-Control-Allow-Headers 
// для обеспечения корректной работы запросов между разными источниками (Cross-Origin Resource Sharing, CORS).
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
// Подключение необходимых файлов и классов: 
// Включает файлы DatabaseConnector.php, productpicture.php и sendJson.php, которые содержат классы и функции для работы с базой данных, изображениями продуктов и отправки JSON-ответов соответственно.
include_once '../src/System/DatabaseConnector.php';
include_once '../Service/productpicture.php';
include_once '../Service/sendJson.php';
// Обработка запроса методом POST: Проверяет метод запроса. Если это POST-запрос, то код обрабатывает различные действия в зависимости от переданного параметра method.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $method = $_GET['method'];
    switch ($method) {
// Создание новой записи: Если метод POST и method равен 'create', то код создает новую запись из полученных данных, проверяя их наличие и корректность, а затем вызывает метод Create() для создания записи в базе данных. 
// В случае успеха возвращает ID созданной записи.
        case 'create':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->ProductID) ||
                    !isset($data->ImageData) ||
                    !isset($data->First) ||
                    !isset($data->Status) ||
                    empty(trim($data->ProductID)) ||
                    empty(trim($data->ImageData)) ||
                    empty(trim($data->First)) ||
                    empty(trim($data->Status))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => [
                        'ProductID',
                        'ImageData',
                        'First',
                        'Status'
                    ]]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $pp = new ProductPicture($db);

                    $pp->ProductID = trim($data->ProductID);
                    $pp->ImageData = trim($data->ImageData);
                    $pp->First = trim($data->First);
                    $pp->Status = trim($data->Status);
                    $result = $pp->Create();
                    if ($result === null) {
                        sendJson(200, 'Product Picture cannot created!');
                    } else {
                        sendJson(200, '', [
                            'ProductPictureID' => $result['ID']
                        ]);
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
// Удаление записи: Если метод POST и method равен 'delete', то код удаляет запись из базы данных с помощью метода Delete(). Возвращает сообщение об успешном удалении или ошибке.
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
                    $pp = new ProductPicture($db);

                    $pp->ProductID = trim($data->ProductID);
                    $pp->ID = trim($data->id);
                    $result = $pp->Delete();
                    if ($result === true) {
                        sendJson(200, 'Product Picture is deleted!');
                    } else {
                        sendJson(200, 'Unexcepted Error');
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
// Обновление данных записи: Если метод POST и method равен 'update', то код обновляет данные записи, проверяя их наличие и корректность, а затем вызывает метод UpdateFirst(). Возвращает сообщение об успешном обновлении или ошибке.
        case 'update':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->ID) ||
                    !isset($data->First) ||
                    empty(trim($data->ID)) ||
                    empty(trim($data->First))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['ID', 'First']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $pp = new ProductPicture($db);

                    $pp->ID = trim($data->ID);
                    $pp->First = trim($data->First);
                    $result = $pp->UpdateFirst();
                    if ($result === true) {
                        sendJson(200, 'Product Picture is updated!');
                    } else {
                        sendJson(200, 'Unexcepted Error');
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
// Активация записи: Если метод POST и method равен 'active', то код активирует запись вызовом метода Active(). Возвращает сообщение об успешной активации или ошибке.
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
                    $pp = new ProductPicture($db);

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
// Деактивация записи: Если метод POST и method равен 'passive', то код деактивирует запись вызовом метода Passive(). Возвращает сообщение об успешной деактивации или ошибке.
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
                    $pp = new ProductPicture($db);

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
// Получение всех записей по ID продукта: Если метод POST и method равен 'getallbyproductid', то код получает все записи из базы данных, относящиеся к определенному продукту, с помощью метода GetAllForProduct(). 
// Возвращает массив записей в формате JSON.
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
                    $pp = new ProductPicture($db);

                    $pp->ProductID = trim($data->ProductID);
                    $result = $pp->GetAllForProduct();
                    $num = $result->rowCount();
// Отправка JSON-ответов: Функция sendJson() используется для отправки ответов в формате JSON с соответствующими HTTP-статусами. 
// Если происходит ошибка, возвращается код состояния 500 ("Внутренняя ошибка сервера"), в противном случае возвращается код состояния 200 ("Успех").
                if ($num > 0) {
                    $pp_arr = array();
                    $pp_arr["records"] = array();
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        $pp_item = array(
                            "ID" =>   $row['ID'],
                            "ProductID" => $row['ProductID'],
                            "ImageData" => $row['ImageData'],
                            "First" => $row['First'],
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
