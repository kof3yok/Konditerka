<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../src/System/DatabaseConnector.php';
include_once '../Service/cart.php';
include_once '../Service/sendJson.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $method = $_GET['method'];
    switch ($method) {
        case 'create':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->userid) ||
                    !isset($data->priceid) ||
                    !isset($data->quantity) ||
                    empty(trim($data->userid)) ||
                    empty(trim($data->priceid)) ||
                    empty(trim($data->quantity))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['user', 'priceid', 'quantity']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $cart = new Cart($db);

                    $cart->PriceID = trim($data->priceid);
                    $cart->UserID = trim($data->userid);
                    $cart->Quantity = trim($data->quantity);
                    $result = $cart->Create();

                    $cart_arr = array();
                    $cart_arr["records"] = array();
                    sendJson(200, '', $cart_arr);
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
                    $cart = new Cart($db);

                    $cart->UserID = trim($data->userid);
                    $cart->ID = trim($data->id);
                    $result = $cart->Delete();

                    $cart_arr = array();
                    sendJson(200, '', $cart_arr);
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
                    !isset($data->quantity) ||
                    empty(trim($data->id)) ||
                    empty(trim($data->quantity))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['id', 'address']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $cart = new Cart($db);

                    $cart->ID = trim($data->id);
                    $cart->Quantity = trim($data->quantity);
                    $result = $cart->Update();

                    $cart_arr = array();
                    $cart_arr["records"] = array();
                    sendJson(200, '', $cart_arr);
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
        case "getall":
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->token) ||
                    empty(trim($data->token))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['user']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $cart = new Cart($db);

                    $cart->UserID = trim($data->token);
                    $result = $cart->GetAllForUser();
                    $num = $result->rowCount();

                    $cart_arr = array();
                    $cart_arr["records"] = array();
                    if ($num > 0) {
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                            // extract($row);
                            $cart_item = array(
                                "Image" => $row["ImageData"],
                                "ID" => $row["ID"],
                                "Catalog" => $row["Catalog"],
                                "Name" => $row["Name"],
                                "Description" => $row["Description"],
                                "Ingredients" => $row["Ingredients"],
                                "NutritionalValue" => $row["NutritionalValue"],
                                "ProductID" => $row["ProductID"],
                                "PriceID" => $row["PriceID"],
                                "PriceName" => $row["PriceName"],
                                "Price" => $row["Price"],
                                "Quantity" => $row["Quantity"],
                                "UserID" => $row["UserID"],
                                "CreationDate " >= $row["CreationDate"],
                            );
                            array_push($cart_arr["records"], $cart_item);
                        }
                        sendJson(200, '', $cart_arr);
                    } else {
                        sendJson(200, '',  $cart_arr);
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
