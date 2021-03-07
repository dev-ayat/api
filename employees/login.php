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
include_once '../objects/phpass.php'  ;

// get database connection
$database = new DatabaseApi();
$db = $database->getConnection();
define('PHPASS_HASH_STRENGTH', 8);
define('PHPASS_HASH_PORTABLE', false);
$app_hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
$app_hasher ->emp_username = isset($_GET['emp_username']) ? $_GET['emp_username'] : die();
$app_hasher ->emp_password = isset($_GET['emp_password']) ? $_GET['emp_password'] : die();

$login = new Login($db);

$login ->emp_username = isset($_GET['emp_username']) ? $_GET['emp_username'] : die();
$login ->emp_password = isset($_GET['emp_password']) ? $_GET['emp_password'] : die();
$login ->emp_token = $app_hasher->HashPassword(isset($_GET['emp_username']) ? $_GET['emp_username'] : die());

//$password = hash("sha256",$_GET['emp_username']);
//echo $password;
// read the details of product to be edited
$login->insertToken();
$login->login();

if($login->login_status!=null){
//print_r(md5(rand() . microtime() . time() . uniqid()));exit;

    $login_arr=array();
    $login_arr["login_info"]=array();
    $login_item = array(
        "login_status" =>  $login->login_status,
        "Token" =>  $login->Token,
        "fullname" =>  $login->fullname,
        "Emp_CD" =>  $login->Emp_CD,
        "User_ID" =>  $login->User_ID,
        "emp_id_num" =>  $login->emp_id_num,
    );
    array_push($login_arr["login_info"], $login_item);
    // set response code - 200 OK
    http_response_code(200);

    // make it json format
    echo json_encode($login_arr);
}

else{
    // set response code - 404 Not found
    http_response_code(404);

    // tell the user product does not exist
    echo json_encode(array("Result" => "0","message" => "No matches login info"));
}
?>