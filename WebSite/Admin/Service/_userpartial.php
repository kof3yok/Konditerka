<?php
function GetAll($filter)
{
    // Get user input
    require_once '../src/System/DatabaseConnector.php';
    require_once '../Service/user.php';
    $database = new Database();
    $db = $database->getConnection();
    $uo = new User($db);
    return $uo->GetAllByFilter($filter);
}
