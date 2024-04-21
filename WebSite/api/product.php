// API для взаимодействия с товарами между приложением и БД
<?php
// Установка заголовков для CORS (Cross-Origin Resource Sharing): Эти заголовки позволяют ресурсам на одном домене запросить доступ к ресурсам на другом домене. 
// Это включает разрешение запросов из других источников, установку методов запросов, установку максимального времени жизни запроса и разрешение определенных заголовков.
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
// Включение необходимых файлов и классов: Включает файлы для подключения к базе данных и классы для работы с продуктами и отправки JSON.
include_once '../src/System/DatabaseConnector.php';
include_once '../Service/product.php';
include_once '../Service/sendJson.php';
// Обработка запросов методом POST: Если HTTP-запрос был выполнен методом POST, код определяет параметр method в запросе GET и в зависимости от этого выполняет различные операции.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $method = $_GET['method'];
    switch ($method) {
// Создание нового продукта (create): Если метод запроса - create, код пытается создать новый продукт в базе данных на основе данных, переданных через тело запроса в формате JSON. 
// При необходимости проверяются все необходимые поля, и возвращается соответствующий статус и сообщение.
        case 'create':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->CatalogID) ||
                    !isset($data->Name) ||
                    !isset($data->Description) ||
                    !isset($data->Ingredients) ||
                    !isset($data->NutritionalValue) ||
                    empty(trim($data->CatalogID)) ||
                    empty(trim($data->Name)) ||
                    empty(trim($data->Description)) ||
                    empty(trim($data->Ingredients)) ||
                    empty(trim($data->NutritionalValue))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => [
                        'CatalogID',
                        'Name',
                        'Description',
                        'Ingredients',
                        'NutritionalValue'
                    ]]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $product = new Product($db);

                    $product->CatalogID = trim($data->CatalogID);
                    $product->Name = trim($data->Name);
                    $product->Description = trim($data->Description);
                    $product->Ingredients = trim($data->Ingredients);
                    $product->NutritionalValue = trim($data->NutritionalValue);
                    $result = $product->Create();
                    if ($result === null) {
                        sendJson(200, 'Product cannot created!');
                    } else {
                        sendJson(200, '', [
                            'productID' => $result['ID']
                        ]);
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
// Удаление продукта (delete): Если метод запроса - delete, код пытается удалить продукт из базы данных на основе переданного ID. Возвращается соответствующий статус и сообщение об успешном или неудачном удалении.
        case 'delete':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->id) ||
                    empty(trim($data->id))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['ID']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $product = new product($db);

                    $product->ID = trim($data->id);
                    $result = $product->Delete();
                    if ($result === true) {
                        sendJson(200, 'Product is passived!');
                    } else {
                        sendJson(200, 'Unexcepted Error');
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
// Активация продукта (active): Если метод запроса - active, код пытается активировать продукт в базе данных на основе переданного ID. Возвращается соответствующий статус и сообщение об успешной или неудачной активации.
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
                    $product = new product($db);

                    $product->ID = trim($data->ID);
                    $result = $product->Active();
                    if ($result === true) {
                        sendJson(200, 'Product is actived!');
                    } else {
                        sendJson(200, 'Unexcepted Error');
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
// Обновление продукта (update): Если метод запроса - update, код пытается обновить информацию о продукте в базе данных на основе переданных данных. 
// При необходимости проверяются все необходимые поля, и возвращается соответствующий статус и сообщение.
        case 'update':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->CatalogID) ||
                    !isset($data->Name) ||
                    !isset($data->Description) ||
                    !isset($data->Ingredients) ||
                    !isset($data->NutritionalValue) ||
                    empty(trim($data->CatalogID)) ||
                    empty(trim($data->Name)) ||
                    empty(trim($data->Description)) ||
                    empty(trim($data->Ingredients)) ||
                    empty(trim($data->NutritionalValue))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => [
                        'ID',
                        'CatalogID',
                        'Name',
                        'Description',
                        'Ingredients',
                        'NutritionalValue'
                    ]]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $product = new Product($db);

                    $product->ID = trim($data->ID);
                    $product->CatalogID = trim($data->CatalogID);
                    $product->Name = trim($data->Name);
                    $product->Description = trim($data->Description);
                    $product->Ingredients = trim($data->Ingredients);
                    $product->NutritionalValue = trim($data->NutritionalValue);
                    $result = $product->Update();
                    if ($result === true) {
                        sendJson(200, 'Product is updated!');
                    } else {
                        sendJson(200, 'Unexcepted Error');
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
// Получение всех продуктов по ID каталога (getallbycatalogid): Если метод запроса - getallbycatalogid, код пытается получить все продукты из базы данных по указанному ID каталога. Полученные данные возвращаются в формате JSON.
        case 'getallbycatalogid':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->CatalogID) ||
                    empty(trim($data->CatalogID))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['CatalogID']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $pp = new Product($db);

                    $pp->CatalogID = trim($data->CatalogID);
                    $result = $pp->GetProductsByCatalogId();
                    $num = $result->rowCount();

                    if ($num > 0) {
                        $pp_arr = array();
                        $pp_arr["records"] = array();
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                            // extract($row);
                            $pp_item = array(
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
// Получение продукта по его ID (getbyid): Если метод запроса - getbyid, код пытается получить продукт из базы данных по указанному ID. Полученные данные возвращаются в формате JSON.
            case 'getbyid':
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
                        $pp = new Product($db);
    
                        $pp->ID = trim($data->ProductID);
                        $result = $pp->GetProductById();
                        $num = $result->rowCount();
    
                        if ($num > 0) {
                            $pp_arr = array();
                            $pp_arr["records"] = array();
                            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                // extract($row);
                                $pp_item = array(
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
                                array_push($pp_arr["records"], $pp_item);
                            }
                            sendJson(200, '', $pp_arr);
                        } else {
                            sendJson(200, 'Unexcepted Error');
                        }
                    }
                } catch (\Throwable $th) {
// Обработка ошибок: Если в процессе выполнения запроса возникают ошибки, код обрабатывает их и возвращает соответствующие статусы и сообщения.
// Возврат ошибочного статуса для недопустимых методов запроса: Если метод запроса не POST, возвращается соответствующий статус и сообщение об ошибке.
                    sendJson(500, 'Internal Server Error');
                }
                break;

            default:
            sendJson(405, 'Invalid Request Method. HTTP method should be POST');
            break;
    }
}
sendJson(405, 'Invalid Request Method. HTTP method should be POST');
