<?php
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
include_once '../config/core.php';
include_once '../shared/utilities.php';
include_once '../objects/login.php';
include_once '../objects/generalization.php';

// utilities
$utilities = new Utilities();

// get database connection
$database = new DatabaseApi();
$db = $database->getConnection();

$login = new Login($db);
$login ->User_ID = isset($_GET['User_ID']) ? $_GET['User_ID'] : die();
$login ->Token = isset($_GET['Token']) ? $_GET['Token'] : die();
$login->user_Authentication();

if($login->Authentication==1) {

    $generalization = new Generalization ($db);
    $data = json_decode(file_get_contents("php://input"));
    // print_r($data);exit;
    if (
    !empty($data->emp_cd)
    ) {
        $generalization->emp_cd = $data->emp_cd;
        $stmt=$generalization->getGeneralization($from_record_num, $records_per_page);
        $num = $stmt->rowCount();
        if ($num>0) {

            $generalization_arr = array();
            $generalization_arr["gen_data"] = array();
            $generalization_arr["paging"]=array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $gen_item = array(
                    "in_gen_cd"=>$in_gen_cd,
                    "in_gen_desc"=>$in_gen_desc,
                    "in_gen_urgent"=>$in_gen_urgent,
                    "in_gen_company"=>$in_gen_company,
                    "file_attach"=>$file_attach,
                    "in_gen_created_by"=>$in_gen_created_by,
                    "in_gen_created_on"=>$in_gen_created_on
                );
                array_push($generalization_arr["gen_data"], $gen_item);
            }
            // include paging
            $total_rows=$generalization->count();
            $page_url="{$home_url}generalization/generalization.php?";
            $page=$data->page_no;
            $paging=$utilities->getPaging($page, $total_rows, $records_per_page, $page_url);
            $generalization_arr["paging"]=$paging;

            http_response_code(200);

            echo json_encode($generalization_arr);
        } else {
            http_response_code(404);

            echo json_encode(array("Result" => "14","message" => "No generalization found"));
        }
    }else {

        http_response_code(400);

        echo json_encode(array("Result" => "3", "Message" => "Data is incomplete."));
    }
}else{
    http_response_code(404);

    echo json_encode(array("Result"=>"0","Message" => "No matches login info"));
}
