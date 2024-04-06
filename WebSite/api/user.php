<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../src/System/DatabaseConnector.php';
include_once '../Service/user.php';
include_once '../Service/sendJson.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $method = $_GET['method'];
    switch ($method) {
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
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['username', 'password', 'email', 'ID']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $user = new User($db);

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
