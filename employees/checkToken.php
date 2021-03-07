<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

// include database and object files
include_once '../config/databaseApi.php';
include_once '../objects/login.php';

$database = new DatabaseApi();
$db = $database->getConnection();
$login = new Login($db);
$login ->User_ID = isset($_GET['User_ID']) ? $_GET['User_ID'] : die();
$login ->Token = isset($_GET['Token']) ? $_GET['Token'] : die();
$login->user_Authentication();

if($login->Authentication==1) {

    $login_arr=array();
    $login_arr["Authentication"]=array();
    $login_item = array(
        "Auth_status" =>  $login->Authentication

    );
    array_push($login_arr["Authentication"], $login_item);
    // set response code - 200 OK
    http_response_code(200);

    // make it json format
    echo json_encode($login_arr);
}

else{
    $login_arr=array();
    $login_arr["Authentication"]=array();
    $login_item = array(
        "Auth_status" => "0"

    );
    array_push($login_arr["Authentication"], $login_item);
    // set response code - 200 OK
    http_response_code(200);

    // make it json format
    echo json_encode($login_arr);
}
?>