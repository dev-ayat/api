<?php
/**
 * Created by PhpStorm.
 * User: MOH
 * Date: 06/11/2020
 * Time: 08:01 م
 */

/**
 * Created by PhpStorm.
 * User: MOH
 * Date: 06/11/2020
 * Time: 06:02 م
 */

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// include database and object files
include_once '../config/databaseApi.php';
include_once '../objects/login.php';
include_once '../objects/salary.php';

// get database connection
$database = new DatabaseApi();
$db = $database->getConnection();

$login = new Login($db);
$login ->User_ID = isset($_GET['User_ID']) ? $_GET['User_ID'] : die();
$login ->Token = isset($_GET['Token']) ? $_GET['Token'] : die();
$login->user_Authentication();

if($login->Authentication==1) {

    $salary = new Salary($db);
    $data = json_decode(file_get_contents("php://input"));
    // print_r($data);exit;
    if (
    !empty($data->emp_cd)&&
    !empty($data->year_no)
    ) {

        // set product property values
        $salary->emp_cd = $data->emp_cd;
        $salary->year_no = $data->year_no;
        $stmt=$salary->salary_data();
        $num = $stmt->rowCount();
        if ($num>0) {

            $salary_arr = array();
            $salary_arr["salary_data"] = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                $sal_item = array(
                    "employee_id"=>$employee_id,
                    "total_salary"=>$total_salary,
                    "total_working_minutes"=>$total_working_minutes,
                    "working_period"=>$working_period,
                    "payment_due"=>$payment_due,
                    "payment_date"=>$payment_date
                );
                array_push($salary_arr["salary_data"] , $sal_item);
            }
            http_response_code(200);

            echo json_encode($salary_arr);
        } else {
            http_response_code(404);

            echo json_encode(array("Result" => "20","message" => "No salary data found"));
        }
    }else {

        http_response_code(400);

        echo json_encode(array("Result" => "3", "Message" => "Data is incomplete."));
    }
}else{
    // set response code - 404 Not found
    http_response_code(404);

    // tell the user product does not exist
    echo json_encode(array("Result"=>"0","Message" => "No matches login info"));
}