<?php
function GetAll()
{
    // Get user input
    require_once '../src/System/DatabaseConnector.php';
    require_once '../Service/userorder.php';
    require_once '../Service/user.php';
    $database = new Database();
    $db = $database->getConnection();
    $uo = new UserOrder($db);
    $u = new User($db);
    $last10 = $uo->GetLast10();
    $allOrder = $uo->GetAllForDashboard();
    $allUser = $u->GetAll();

    $num = $last10->rowCount();
    $all = array();
    $all["records"] = array();
    if ($num > 0) {
        while ($row = $last10->fetch(PDO::FETCH_ASSOC)) {
            $all_item = array(
                "ID" =>   $row['ID'],
                "UserID" => $row['UserID'],
                "Username" => $row['Username'],
                "Price" => $row['Price'],
                "Quantity" => $row['Quantity'],
                "Status" => $row['Status'],
                "CreationDate" => $row['CreationDate'],
            );
            array_push($all["records"], $all_item);
        }
    } else {
        $all["records"] = null;
    }
    $all["OrderCount"] = $allOrder['Count'];
    $all["TotalPrice"] = $allOrder['Price'];
    $all["UserCount"] = $allUser['Count'];
    return $all;
}
