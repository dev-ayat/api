<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// include database and object files
include_once '../config/databaseApi.php';
include_once '../objects/holiday.php';
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

    $confHoliday = new Holiday($db);
    $data = json_decode(file_get_contents("php://input"));
    if (
    !empty($data->order_hol_id)&&
    !empty($data->order_hol_emp_id)&&
    !empty($data->order_hol_status)&&
    !empty($data->order_hol_type)&&
    !empty($data->order_hol_count)&&
    !empty($data->order_hol_start)&&
    !empty($data->order_hol_end)&&
    !empty($data->created_by)


    ) {
        $guid =$confHoliday->generateRandomString(8);
    $confHoliday->guid_id = $guid;
    $confHoliday->order_hol_id = $data->order_hol_id;
    $confHoliday->order_hol_emp_id = $data->order_hol_emp_id;
    $confHoliday->order_hol_status = $data->order_hol_status;
    $confHoliday->order_hol_type = $data->order_hol_type;
    $confHoliday->order_hol_count = $data->order_hol_count;
    $confHoliday->order_hol_start = $data->order_hol_start;
    $confHoliday->order_hol_end = $data->order_hol_end;
    $confHoliday->created_by = $data->created_by;
    $confHoliday->created_on = date('Y-m-d H:i:s');

    if($data->order_hol_status==1) {
        $confHoliday->checkHoliday();
            if ($confHoliday->holday_remaining >= $data->order_hol_count) {
                $confHoliday->confirmHoliday();
                $confHoliday->updateHolidaycounts();
                $confHoliday->insertSummeryHoliday();
                $confHoliday->updateAttends();
                // set response code - 200 ok
                http_response_code(200);

                // tell the user
                echo json_encode(array("Result" => "5", "Message" => " holiday confirmed"));
            } else {
                if ($confHoliday->rejectHoliday()) {

                    http_response_code(200);
                    echo json_encode(array("Result" => "6", "Message" => "No enough holidays,holiday rejected"));
                } else {

                    http_response_code(503);
                    echo json_encode(array("Result" => "7", "message" => "Unable to reject holiday"));
                }
            }

//        } else {
//
//            http_response_code(503);
//            echo json_encode(array("Result" => "0", "message" => "Unable to "));
//        }
    }  else{
            if($confHoliday->rejectHoliday()){

                http_response_code(200);
                echo json_encode(array("Result" => "8", "Message" =>"holiday rejected"));
            }else{

                http_response_code(503);
                echo json_encode(array("Result" => "7","message" => "Unable to reject holiday"));
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