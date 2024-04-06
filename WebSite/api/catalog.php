<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../src/System/DatabaseConnector.php';
include_once '../Service/catalog.php';
include_once '../Service/sendJson.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $method = $_GET['method'];
    switch ($method) {
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
            sendJson(405, 'Invalid Request Method. HTTP method should be POST');
            break;
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
}
sendJson(405, 'Invalid Request Method. HTTP method should be POST');
