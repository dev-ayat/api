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
include_once '../objects/attends.php';

// get database connection
$database = new DatabaseApi();
$db = $database->getConnection();

$login = new Login($db);
$login ->User_ID = isset($_GET['User_ID']) ? $_GET['User_ID'] : die();
$login ->Token = isset($_GET['Token']) ? $_GET['Token'] : die();
$login->user_Authentication();

if($login->Authentication==1) {

    $requestJobAss = new Attends($db);
    $data = json_decode(file_get_contents("php://input"));
   // print_r($data);exit;
    if (!empty($data->order_emp_id) &&
        !empty($data->order_exit_type) &&
        !empty($data->order_exit_date) &&
        !empty($data->order_exit_fromtime) &&
        !empty($data->order_exit_totime)&&
        !empty($data->order_created_by)
    ) {
        $guid =$requestJobAss->generateRandomString(8);
        $requestJobAss->guid_id = $guid;
        $requestJobAss->order_emp_id = $data->order_emp_id;
        $requestJobAss->order_exit_type = $data->order_exit_type;
        $requestJobAss->order_exit_date = $data->order_exit_date;
        $requestJobAss->order_exit_fromtime = $data->order_exit_fromtime;;
        $requestJobAss->order_exit_totime = $data->order_exit_totime;
        $requestJobAss->order_exit_note = $data->order_exit_note;
        $requestJobAss->order_exit_status ="0";
        $requestJobAss->order_created_by = $data->order_created_by;
        $requestJobAss->order_created_on = date('Y-m-d H:i:s');

        // create the holiday
        if ($requestJobAss->requestjobAssignment()) {

            // set response code - 201 created
            http_response_code(201);

            // tell the user
            echo json_encode(array("Result" => "18","Message" => "exit order was created."));
        } // if unable to create the holiday, tell the user
        else {

            // set response code - 503 service unavailable
            http_response_code(503);

            // tell the user
            echo json_encode(array("Result" => "19","Message" => "Unable to create exit order."));

        }
    } // tell the user data is incomplete
    else {

        // set response code - 400 bad request
        http_response_code(400);

        // tell the user
        echo json_encode(array("Result" => "3",
                                "Message" => "Data is incomplete."));
    }
}else{
    // set response code - 404 Not found
    http_response_code(404);

    // tell the user product does not exist
    echo json_encode(array("Result"=>"0","Message" => "No matches login info"));
}


?>