<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

// include database and object files
include_once '../config/databaseApi.php';
include_once '../objects/login.php';
include_once '../objects/dashboard.php';
include_once __DIR__ . '/../vendor/autoload.php';

////id= 923861747770-vuh9adgf8injtaipa6ms1s2iu2akk6nt.apps.googleusercontent.com
//    //se=GvSDn5h-fWdRVrjJ1Qh431Sq;
//$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
//$redirect_uri = 'http://localhost:8080';
//$client = new Google_Client();
//$client->setAuthConfig(__DIR__ . '/../credentials.json');
////$client->addScope("https://www.googleapis.com/auth/drive");
//$client->addScope(['email', 'profile']);
//
//$client->setRedirectUri($redirect_uri);
//$client->authenticate($request->input('code'));
//$tokens = $client->getAccessToken();
//print_r($token);exit;
//if (isset($_GET['code'])) {
//    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
//    echo $token;
//}

//if ($client->getAuth()->isAccessTokenExpired()) {
//    $client->getAuth()->refreshTokenWithAssertion();
//}

 //Get the json encoded access token.
//$token = $client->getAccessToken();
 //get database connection
$database = new DatabaseApi();
$db = $database->getConnection();
$login = new Login($db);
$login ->User_ID = isset($_GET['User_ID'])? $_GET['User_ID'] : die();
$login ->Token = isset($_GET['Token'])  ? $_GET['Token'] : die();
$login->user_Authentication();

if($login->Authentication==1){

    // prepare product object
    $dashboard = new Dashboard($db);
    $data = json_decode(file_get_contents("php://input"));
    if (!empty($data->emp_admin)){
        $dashboard->emp_admin = $data->emp_admin;
        $dashboard->dashboard();
        if($dashboard->employee_no!=0){

            $dashboard_arr=array();
            $dashboard_arr["dashboard"]=array();
            $dashboard_item = array(
                "emp_admin" =>  $dashboard->emp_admin,
                "employee_no" => $dashboard->employee_no,
                "emp_attend_no" => $dashboard->emp_attend_no,
                "emp_absant_no" => $dashboard->emp_absant_no,
                "emp_spec_no" => $dashboard->emp_spec_no,
                "emp_workjob_no" => $dashboard->emp_workjob_no,
                "emp_without_no" => $dashboard->emp_without_no

            );
            array_push($dashboard_arr["dashboard"], $dashboard_item);
            http_response_code(200);

            echo json_encode($dashboard_arr);
        }else{
            http_response_code(404);

            echo json_encode(array("Result" => "13","Message" => "No employees for this user."));
        }
    }
    else {

        http_response_code(400);

        echo json_encode(array("Result" => "3",
                               "Message" => "Data is incomplete."));
    }


}

else{
    http_response_code(404);

    echo json_encode(array("Result"=>"0","Message" => "No matches login info"));
}


?>