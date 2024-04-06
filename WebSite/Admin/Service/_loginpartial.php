<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include_once '../../src/System/DatabaseConnector.php';
    include_once '../../Service/login.php';
    // Get user input
    $username = $_POST["username"];
    $password = $_POST["password"];

    $database = new Database();
    $db = $database->getConnection();
    $login = new Login($db);
    $login->username = $username;
    $login->password = $password;

    $admin = $login->AdminLogin();
    if ($admin === null || $admin == false) {
        header("location: ../index.php?loginsuccess=false");
    } else {
        session_start();
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['userId'] = $admin['ID'];
        header("Location: ../dashboard.php");
        exit();
    }
}
?>