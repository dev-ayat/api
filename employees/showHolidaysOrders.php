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

    $showholiday = new Holiday($db);
    $data = json_decode(file_get_contents("php://input"));
  //  print_r($data);exit;
    if (
        !empty($data->emp_cd)

    ) {

        // set product property values
        $showholiday->emp_cd = $data->emp_cd;
        $stmt=$showholiday->showHolidayOrders();
        $num = $stmt->rowCount();

        if ($num>0) {

            $showholiday_arr = array();
            $showholiday_arr["holiday_orders"] = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $showholiday_item = array(
                    "order_hol_id" => $order_hol_id,
                    "order_hol_emp_id" => $order_hol_emp_id,
                    "holday_type_name" => $holday_type_name,
                    "order_hol_type" => $order_hol_type,
                    "order_hol_count" => $order_hol_count,
                    "order_hol_start" => $order_hol_start,
                    "order_hol_end" => $order_hol_end,
                    "order_hol_note" => $order_hol_note,
                    "order_hol_status" => $order_hol_status,
                    "order_created_on" => $order_created_on,
                    "order_created_by" => $order_created_by
                );
                array_push($showholiday_arr["holiday_orders"], $showholiday_item);
            }
            // set response code - 200 OK
            http_response_code(200);

            // make it json format
            echo json_encode($showholiday_arr);
        } else {
            // set response code - 404 Not found
            http_response_code(404);

            // tell the user product does not exist
            echo json_encode(array("Result" => "23","message" => "No holidays order found"));
        }
    }else {

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