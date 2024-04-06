<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../src/System/DatabaseConnector.php';
include_once '../Service/product.php';
include_once '../Service/sendJson.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $method = $_GET['method'];
    switch ($method) {
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
                    sendJson(500, 'Internal Server Error');
                }
                break;
    
            default:
            sendJson(405, 'Invalid Request Method. HTTP method should be POST');
            break;
    }
}
sendJson(405, 'Invalid Request Method. HTTP method should be POST');
