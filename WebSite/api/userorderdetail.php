<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../src/System/DatabaseConnector.php';
include_once '../Service/userorderdetail.php';
include_once '../Service/cart.php';
include_once '../Service/sendJson.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $method = $_GET['method'];
    switch ($method) {
        case 'create':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->OrderID) ||
                    !isset($data->ProductID) ||
                    !isset($data->Quantity) ||
                    !isset($data->Price) ||
                    empty(trim($data->OrderID)) ||
                    empty(trim($data->ProductID)) ||
                    empty(trim($data->Quantity)) ||
                    empty(trim($data->Price))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => [
                        'OrderID',
                        'ProductID',
                        'Quantity',
                        'Price'
                    ]]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $userorderdetail = new UserOrderDetail($db);

                    $userorderdetail->OrderID = trim($data->OrderID);
                    $userorderdetail->ProductID = trim($data->ProductID);
                    $userorderdetail->Quantity = trim($data->Quantity);
                    $userorderdetail->Price = trim($data->Price);
                    $result = $userorderdetail->Create();
                    if ($result === null) {
                        sendJson(200, 'Order cannot created!');
                    } else {
                        sendJson(200, '', [
                            'OrderID' => $result['ID']
                        ]);
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
        case 'createall':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->records)
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => [
                        'Records'
                    ]]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $userorderdetail = new UserOrderDetail($db);
                    $cart = new Cart($db);
                    $cart->UserID = $data->UserID;
                    $result = $userorderdetail->CreateAll($data->records);
                    if ($result === null) {
                        sendJson(200, 'Orders cannot created!');
                    } else {
                        $cart->DeleteAll();
                        sendJson(200, 'Orders created!');
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
                    !isset($data->OrderID) ||
                    empty(trim($data->OrderID))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['OrderID']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $userorderdetail = new UserOrderDetail($db);

                    $userorderdetail->OrderID = trim($data->OrderID);
                    $result = $userorderdetail->GetAllByOrder();
                    $num = $result->rowCount();

                    $userorder_arr = array();
                    $userorder_arr["records"] = array();
                    if ($num > 0) {
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                            // extract($row);
                            $userorder_item = array(
                                "Image" => $row["ImageData"],
                                "ID" => $row["ID"],
                                "Catalog" => $row["Catalog"],
                                "Name" => $row["Name"],
                                "Description" => $row["Description"],
                                "Ingredients" => $row["Ingredients"],
                                "NutritionalValue" => $row["NutritionalValue"],
                                "ProductID" => $row["ProductID"],
                                "Price" => $row["Price"],
                                "Quantity" => $row["Quantity"],
                                "CreationDate " >= $row["CreationDate"],
                            );
                            array_push($userorder_arr["records"], $userorder_item);
                        }
                        sendJson(200, '', $userorder_arr);
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
