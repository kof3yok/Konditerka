<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../src/System/DatabaseConnector.php';
include_once '../Service/orderaddress.php';
include_once '../Service/sendJson.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $method = $_GET['method'];
    switch ($method) {
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
                sendJson(500, 'Internal Server Error');
            }
            break;
        default:
            sendJson(405, 'Invalid Request Method. HTTP method should be POST');
            break;
    }
}
sendJson(405, 'Invalid Request Method. HTTP method should be POST');
