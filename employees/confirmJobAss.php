<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// include database and object files
include_once '../config/databaseApi.php';
include_once '../objects/attends.php';
include_once '../objects/login.php';
require_once '../vendor/autoload.php';



// get database connection
$database = new DatabaseApi();
$db = $database->getConnection();
$login = new Login($db);
$login ->User_ID = isset($_GET['User_ID']) ? $_GET['User_ID'] : die();
$login ->Token = isset($_GET['Token']) ? $_GET['Token'] : die();
$login->user_Authentication();

if($login->Authentication==1) {

    $confass = new Attends($db);
    $data = json_decode(file_get_contents("php://input"));

    if (
        !empty($data->order_exit_id)&&
        !empty($data->order_exit_emp_id)&&
        !empty($data->order_exit_status)&&
        !empty($data->order_exit_type)&&
        !empty($data->order_exit_starttime)&&
        !empty($data->order_exit_endtime)&&
        !empty($data->created_by)

    ) {
        $guid = $confass->generateRandomString(8);
        $confass->guid_id = $guid;
        $confass->order_hol_id = $data->order_exit_id;
        $confass->order_hol_emp_id = $data->order_exit_emp_id;
        $confass->order_hol_status = $data->order_exit_status;
        $confass->order_hol_type = $data->order_exit_type;
        $confass->order_hol_start = $data->order_exit_starttime;
        $confass->order_hol_end = $data->order_exit_endtime;
        $confass->created_by = $data->created_by;
        $confass->created_on = date('Y-m-d H:i:s');

    if ($data->order_exit_status == 1) {

           // $confass->insertAtAttends();
         if($confass->updateorderexit()){

             http_response_code(200);
             echo json_encode(array("Result" => "9", "Message" => "job ass confirmed"));
         }else{

             http_response_code(503);
             echo json_encode(array("Result" => "10","message" => "Unable to confirm job ass"));
         }

    } else {

        if($confass->rejectordrer()){

            http_response_code(200);
            echo json_encode(array("Result" => "11", "Message" => " job ass rejected"));
        }else{

            http_response_code(503);
            echo json_encode(array("Result" => "12","message" => "Unable to reject job ass"));
        }

    }
    }else {

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