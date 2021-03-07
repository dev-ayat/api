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
    $holidayconst = new Constants($db);
    $stmt = $holidayconst->holiday_constant();
    $num = $stmt->rowCount();

// check if more than 0 record found
if($num>0){

    // attends array
    $hol_const_arr=array();
    $hol_const_arr["holiday_types"]=array();

    // retrieve our table contents
    // fetch() is faster than fetchAll()
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
        $holiday_item=array(
            "holday_type_id" => $holday_type_id,
            "holday_type_name" => $holday_type_name,
        );

        array_push($hol_const_arr["holiday_types"], $holiday_item);
    }

    // set response code - 200 OK
    http_response_code(200);

    // show products data in json format
    echo json_encode($hol_const_arr);
    }
    else{

        // set response code - 404 Not found
        http_response_code(404);

        // tell the user no products found
        echo json_encode(
            array("Result"=>"15","message" => "No constants found.")
        );
    }
}else{
    // set response code - 404 Not found
    http_response_code(404);

    // tell the user product does not exist
    echo json_encode(array("Result"=>"0","Message" => "No matches login info"));
}