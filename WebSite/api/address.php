<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../src/System/DatabaseConnector.php';
include_once '../Service/address.php';
include_once '../Service/sendJson.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $method = $_GET['method'];
    switch ($method) {
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
        default:
            sendJson(405, 'Invalid Request Method. HTTP method should be POST');
            break;
    }
}
sendJson(405, 'Invalid Request Method. HTTP method should be POST');
