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

    $showattendsorsers = new Attends($db);
    $data = json_decode(file_get_contents("php://input"));
    //  print_r($data);exit;
    if (
    !empty($data->emp_cd)

    ) {

        // set product property values
        $showattendsorsers->emp_cd = $data->emp_cd;
        $stmt=$showattendsorsers->showExitOrdersForEmp();
        $num = $stmt->rowCount();

        if ($num>0) {

            $showattends_arr = array();
            $showattends_arr["attends_orders"]  = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $showattends_item = array(
                    "order_id" => $order_id,
                    "order_emp_id" => $order_emp_id,
                    "order_exit_status"=>$order_exit_status,
                    "fullname" => $fullname,
                    "order_exit_date" => $order_exit_date,
                    "order_exit_type" => $order_exit_type,
                    "order_exit_fromtime" => $order_exit_fromtime,
                    "order_exit_totime" => $order_exit_totime,
                    "order_exit_status" => $order_exit_status,
                    "order_created_by" => $order_created_by,
                    "order_created_on" => $order_created_on,
                    "attendance_type_name" => $attendance_type_name
                );
                array_push($showattends_arr["attends_orders"], $showattends_item);
            }
            // set response code - 200 OK
            http_response_code(200);

            // make it json format
            echo json_encode($showattends_arr);
        } else {
            // set response code - 404 Not found
            http_response_code(404);

            // tell the user product does not exist
            echo json_encode(array("Result" => "22","message" => "No exit order for Employees"));
        }
    }else{
        http_response_code(400);
        echo json_encode(array("Result" => "3", "Message" => "Data is incomplete."));
    }
}else{
    // set response code - 404 Not found
    http_response_code(404);

    // tell the user product does not exist
    echo json_encode(array("Result"=>"0","Message" => "No matches login info"));
}

?>