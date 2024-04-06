<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once './src/System/DatabaseConnector.php';
include_once './Service/login.php';

if (!empty($_GET['name'])) {
	$database = new Database();
	$db = $database->getConnection();
	$login = new Login($db);
	$result = $login->Login("", "");
	$count = $result->fetchAll();
	// $statement = $dbConnection->prepare("SELECT * FROM User");
	// $statement->execute();
	// $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
	//$result=mysqli_query($con,"SELECT * FROM User");

	response(200, "Product Found", $count);
	// if(mysqli_num_rows($result)<1)
	// {
	// 	response(200,"Product Not Found",NULL);
	// }
	// else
	// {
	// 	response(200,"Product Found",$result);
	// }
} else {
	response(400, "Invalid Request", NULL);
}

function response($status, $status_message, $data)
{
	header("HTTP/1.1 " . $status);

	$response['status'] = $status;
	$response['status_message'] = $status_message;
	$response['data'] = $data;

	$json_response = json_encode($response);
	echo $json_response;
}
