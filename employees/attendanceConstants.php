<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once '../config/databaseApi.php';
include_once '../objects/constants.php';
include_once '../objects/login.php';

// get database connection
$database = new DatabaseApi();
$db = $database->getConnection();
$login = new Login($db);
$login ->User_ID = isset($_GET['User_ID']) ? $_GET['User_ID'] : die();
$login ->Token = isset($_GET['Token']) ? $_GET['Token'] : die();
$login->user_Authentication();

if($login->Authentication==1) {
$attendnceconst = new Constants($db);
$stmt = $attendnceconst->attendance_constant();
$num = $stmt->rowCount();

// check if more than 0 record found
if($num>0){

    // attends array
    $att_const_arr=array();
    $att_const_arr["attendance_types"]=array();

    // retrieve our table contents
    // fetch() is faster than fetchAll()
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
        $attendance_item=array(
            "attendance_type_id" => $attendance_type_id,
            "attendance_type_name" => $attendance_type_name,
        );

        array_push($att_const_arr["attendance_types"], $attendance_item);
    }

    // set response code - 200 OK
    http_response_code(200);

    // show products data in json format
    echo json_encode($att_const_arr);
}
else{

    // set response code - 404 Not found
    http_response_code(404);

    // tell the user no products found
    echo json_encode(
        array("Result"=>"4","message" => "No constants found.")
    );
}}else{
    // set response code - 404 Not found
    http_response_code(404);

    // tell the user product does not exist
    echo json_encode(array("Result"=>"0","Message" => "No matches login info"));
}