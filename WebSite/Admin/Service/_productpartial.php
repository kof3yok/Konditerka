<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_update'])) {

    include_once '../../src/System/DatabaseConnector.php';
    include_once '../../Service/product.php';
    $database = new Database();
    $db = $database->getConnection();
    $product = new Product($db);
    $product->ID = $_POST["ID"];
    $product->CatalogID = $_POST["catalog"];
    $product->Name = $_POST["Name"];
    $product->Description = $_POST["Description"];
    $product->Ingredients = $_POST["Ingredients"];
    $product->NutritionalValue = $_POST["NutritionalValue"];
    if ($product->ID != 0) {
        $product->Update();
    } else {
        $result = $product->Create();
        $product->ID = $result["ID"];
    }
    header('Location: ../productedit.php?id=' . $product->ID);
} else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['picture_upload'])) {
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
        include_once '../../src/System/DatabaseConnector.php';
        include_once '../../Service/ProductPicture.php';
        $database = new Database();
        $db = $database->getConnection();
        $pp = new ProductPicture($db);
        $pp->ProductID = $_POST["ID"];
        $tmpFilePath = $_FILES["file"]["tmp_name"];
        $fileContent = file_get_contents($tmpFilePath);
        $pp->ImageData = $fileContent;
        $pp->First = false;
        $pp->Status = true;
        $pp->Create();
    }
    header('Location: ../productedit.php?id=' . $pp->ProductID);
} else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['picture_default'])) {

    include_once '../../src/System/DatabaseConnector.php';
    include_once '../../Service/ProductPicture.php';
    $database = new Database();
    $db = $database->getConnection();
    $pp = new ProductPicture($db);
    $pp->ID = $_POST["pictureid"];
    $pp->ProductID = $_POST["ID"];
    // $pp->First = true;
    $pp->UpdateFirst();

    header('Location: ../productedit.php?id=' . $pp->ProductID);
} else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_passive'])) {

    include_once '../../src/System/DatabaseConnector.php';
    include_once '../../Service/product.php';
    $database = new Database();
    $db = $database->getConnection();
    $product = new Product($db);
    $product->ID = $_POST["ID"];
    if ($product->ID != 0) {
        $product->Delete();
    } else {
    }
    header('Location: ../productedit.php?id=' . $product->ID);
} else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_active'])) {

    include_once '../../src/System/DatabaseConnector.php';
    include_once '../../Service/product.php';
    $database = new Database();
    $db = $database->getConnection();
    $product = new Product($db);
    $product->ID = $_POST["ID"];
    if ($product->ID != 0) {
        $product->Active();
    } else {
    }
    header('Location: ../productedit.php?id=' . $product->ID);
}
function GetAll($filter)
{
    // Get user input
    require_once '../src/System/DatabaseConnector.php';
    require_once '../Service/product.php';
    $database = new Database();
    $db = $database->getConnection();
    $product = new Product($db);
    $product->Filter = $filter;
    return $product->GetAll();
}
function GetProduct($id)
{
    // Get user input
    require_once '../src/System/DatabaseConnector.php';
    require_once '../Service/product.php';
    $database = new Database();
    $db = $database->getConnection();
    $product = new Product($db);
    $product->ID = $id;
    $stmt= $product->GetProductById();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function GetPrices($id)
{
    // Get user input
    require_once '../src/System/DatabaseConnector.php';
    require_once '../Service/productprice.php';
    $database = new Database();
    $db = $database->getConnection();
    $pp = new ProductPrice($db);
    $pp->ProductID = $id;
    return $pp->GetAllForProduct();
}
function GetOrders($id)
{
    // Get user input
    require_once '../src/System/DatabaseConnector.php';
    require_once '../Service/userorderdetail.php';
    $database = new Database();
    $db = $database->getConnection();
    $uod = new UserOrderDetail($db);
    $uod->ProductID = $id;
    return $uod->GetAllByProduct();
}
function GetImages($id)
{
    // Get user input
    require_once '../src/System/DatabaseConnector.php';
    require_once '../Service/ProductPicture.php';
    $database = new Database();
    $db = $database->getConnection();
    $pp = new ProductPicture($db);
    $pp->ProductID = $id;
    return $pp->GetAllForProduct();
}
function GetCatalog()
{
    require_once '../src/System/DatabaseConnector.php';
    require_once '../Service/catalog.php';
    $database = new Database();
    $db = $database->getConnection();
    $catalog = new Catalog($db);
    return $catalog->GetAll();
}
