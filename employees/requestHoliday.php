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
include_once '../objects/holiday.php';

// get database connection
$database = new DatabaseApi();
$db = $database->getConnection();

$login = new Login($db);
$login ->User_ID = isset($_GET['User_ID']) ? $_GET['User_ID'] : die();
$login ->Token = isset($_GET['Token']) ? $_GET['Token'] : die();
$login->user_Authentication();

if($login->Authentication==1) {

    $requestHoliday = new Holiday($db);
    $data = json_decode(file_get_contents("php://input"));
   // print_r($data);exit;
    if (
        !empty($data->h_sumary_emp_cd) &&
        !empty($data->h_sumary_type) &&
        !empty($data->h_sumary_start_date) &&
        !empty($data->h_sumary_end_date) &&
        !empty($data->h_sumary_created_by)
    ) {

        $diff = strtotime($data->h_sumary_end_date) - strtotime($data->h_sumary_start_date);
        $h_sumary_count=abs(round($diff / 86400));
        $guid =$requestHoliday->generateRandomString(8);
        $requestHoliday->guid_id = $guid;
        $requestHoliday->h_sumary_emp_cd = $data->h_sumary_emp_cd;
        $requestHoliday->h_sumary_type = $data->h_sumary_type;
        $requestHoliday->h_sumary_start_date = $data->h_sumary_start_date;
        $requestHoliday->h_sumary_count = $h_sumary_count;
        $requestHoliday->h_sumary_end_date = $data->h_sumary_end_date;
        $requestHoliday->h_sumary_status = "0";
        $requestHoliday->h_sumary_note = $data->h_sumary_note;
        $requestHoliday->h_sumary_created_by = $data->h_sumary_created_by;
        $requestHoliday->h_sumary_created_on = date('Y-m-d H:i:s');

        // create the holiday
        if ($requestHoliday->requestHoliday()) {

            // set response code - 201 created
            http_response_code(201);

            // tell the user
            echo json_encode(array("Result" => "16","Message" => "Holiday order was created."));
        } // if unable to create the holiday, tell the user
        else {

            // set response code - 503 service unavailable
            http_response_code(503);

            // tell the user
            echo json_encode(array("Result" => "17","Message" => "Unable to create holiday order."));

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