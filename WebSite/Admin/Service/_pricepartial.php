<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_update'])) {

    include_once '../../src/System/DatabaseConnector.php';
    include_once '../../Service/productprice.php';
    $database = new Database();
    $db = $database->getConnection();
    $pp = new ProductPrice($db);
    $pp->ID = $_POST["ID"];
    $pp->ProductID = $_POST["ProductID"];
    $pp->Name = $_POST["Name"];
    $pp->Description = $_POST["Description"];
    $pp->Price = $_POST["Price"];
    if ($pp->ID != 0) {
        $pp->Update();
    } else {
        $result = $pp->Create();
        $pp->ID = $result["ID"];
    }
    header('Location: ../productedit.php?id=' . $pp->ProductID);
} else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['price_passive'])) {

    include_once '../../src/System/DatabaseConnector.php';
    include_once '../../Service/productprice.php';
    $database = new Database();
    $db = $database->getConnection();
    $pp = new ProductPrice($db);
    $pp->ID = $_POST["ID"];
    $pp->ProductID = $_POST["ProductID"];
    if ($pp->ID != 0) {
        $pp->Passive();
    } else {
    }
    header('Location: ../productedit.php?id=' . $pp->ProductID);
} else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['price_active'])) {

    include_once '../../src/System/DatabaseConnector.php';
    include_once '../../Service/productprice.php';
    $database = new Database();
    $db = $database->getConnection();
    $pp = new ProductPrice($db);
    $pp->ID = $_POST["ID"];
    $pp->ProductID = $_POST["ProductID"];
    if ($pp->ID != 0) {
        $pp->Active();
    } else {
    }
    header('Location: ../productedit.php?id=' . $pp->ProductID);
}
function GetPrice($id)
{
    // Get user input
    require_once '../src/System/DatabaseConnector.php';
    require_once '../Service/productprice.php';
    $database = new Database();
    $db = $database->getConnection();
    $pp = new ProductPrice($db);
    $pp->ID = $id;
    return $pp->Get();
}
