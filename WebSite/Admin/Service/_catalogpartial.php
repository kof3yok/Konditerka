<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_update'])) {

    include_once '../../src/System/DatabaseConnector.php';
    include_once '../../Service/catalog.php';
    $database = new Database();
    $db = $database->getConnection();
    $catalog = new Catalog($db);
    $catalog->ID = $_POST["ID"];
    $catalog->Name = $_POST["Name"];
    if ($catalog->ID != 0) {
        $catalog->Update();
    } else {
        $result = $catalog->Create();
        $catalog->ID = $result["ID"];
    }
    header('Location: ../catalog.php');
}
function GetAll()
{
    // Get user input
    require_once '../src/System/DatabaseConnector.php';
    require_once '../Service/catalog.php';
    $database = new Database();
    $db = $database->getConnection();
    $catalog = new Catalog($db);
    return $catalog->GetAll();
}
function Get($id)
{
    // Get user input
    require_once '../src/System/DatabaseConnector.php';
    require_once '../Service/catalog.php';
    $database = new Database();
    $db = $database->getConnection();
    $catalog = new Catalog($db);
    $catalog->ID = $id;
    return $catalog->Get();
}

