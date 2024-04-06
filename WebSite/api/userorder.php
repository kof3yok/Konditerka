<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../src/System/DatabaseConnector.php';
include_once '../Service/userorder.php';
include_once '../Service/sendJson.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $method = $_GET['method'];
    switch ($method) {
        case 'create':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->UserID) ||
                    !isset($data->Price) ||
                    empty(trim($data->UserID)) ||
                    empty(trim($data->Price))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['UserID', 'Price']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $userorder = new UserOrder($db);

                    $userorder->UserID = trim($data->UserID);
                    $userorder->Price = trim($data->Price);
                    $result = $userorder->Create();
                    if ($result === null) {
                        sendJson(200, 'Order cannot created!');
                    } else {
                        $pp_arr = array();
                        $pp_arr["records"] = array();
                        $pp_item = array(
                            "ID" => $result['ID']
                        );
                        array_push($pp_arr["records"], $pp_item);
                        sendJson(200, '', $pp_arr);
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
        case 'getall':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->UserID) ||
                    empty(trim($data->UserID))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['UserID']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $userorder = new UserOrder($db);

                    $userorder->UserID = trim($data->UserID);
                    $result = $userorder->GetAllByUser();
                    $num = $result->rowCount();
    
                    $userorder_arr = array();
                    $userorder_arr["records"] = array();
                    if ($num > 0) {
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                            // extract($row);
                            $userorder_item = array(
                                "ID" => $row['ID'],
                                "UserID" => $row['UserID'],
                                "Price" => $row['Price'],
                                "CreationDate" => $row['CreationDate'],
                                "Address" => $row['Address'],
                                "Status" => $row['Status'],
                                "Driver" => $row['Driver']
                            );
                            array_push($userorder_arr["records"], $userorder_item);
                        }
                        sendJson(200, '', $userorder_arr);
                    } else {
                        sendJson(200, '', $userorder_arr);
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
