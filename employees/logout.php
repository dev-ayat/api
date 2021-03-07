<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// include database and object files
include_once '../config/databaseApi.php';
include_once '../objects/login.php';


/// get database connection
$database = new DatabaseApi();
$db = $database->getConnection();
$login = new Login($db);
$login ->User_ID = isset($_GET['User_ID']) ? $_GET['User_ID'] : die();
$login ->Token = isset($_GET['Token']) ? $_GET['Token'] : die();
$login->user_Authentication();

if($login->Authentication==1) {

    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->user_id))
    {
        $login->user_id = $data->user_id;
        if($login->logout()){

                http_response_code(200);
                echo json_encode(array("Result" => "1", "Message" => " logout sucessfuly"));
            }else{

                http_response_code(503);
                echo json_encode(array("Result" => "2","message" => "Fail to logout"));
            }


    }else{

        // set response code - 400 bad request
        http_response_code(400);

        // tell the user
        echo json_encode(array("Result" => "3","Message" => "Data is incomplete."));
    }
}else{
    // set response code - 404 Not found
    http_response_code(404);

    // tell the user product does not exist
    echo json_encode(array("Result"=>"0","Message" => "No matches login info"));
}
?>