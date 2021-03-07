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

//id= 923861747770-vuh9adgf8injtaipa6ms1s2iu2akk6nt.apps.googleusercontent.com
    //se=GvSDn5h-fWdRVrjJ1Qh431Sq;
//$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
//$redirect_uri = 'http://localhost:8080';
//$client = new Google_Client();
//$client->setAuthConfig(__DIR__ . '/../credentials.json');
//$client->addScope("https://www.googleapis.com/auth/drive");
//$client->setRedirectUri($redirect_uri);
//$token = $client->getAccessToken();
//echo $_GET['code'];
//if (isset($_GET['code'])) {
//    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
//    echo $token;
//}

//if ($client->getAuth()->isAccessTokenExpired()) {
//    $client->getAuth()->refreshTokenWithAssertion();
//}

// Get the json encoded access token.
//$token = $client->getAccessToken();

 //get database connection
$database = new DatabaseApi();
$db = $database->getConnection();
$login = new Login($db);
// set ID property of record to read
$login ->User_ID = isset($_GET['User_ID'])? $_GET['User_ID'] : die();
$login ->Token = isset($_GET['Token'])  ? $_GET['Token'] : die();
$login->user_Authentication();

if($login->Authentication==1){

    // prepare product object
    $dashboard = new Dashboard($db);
    $data = json_decode(file_get_contents("php://input"));
    if (!empty($data->holiday_emp_cd)&&
        !empty($data->holiday_year)
    ){
        $dashboard->holiday_emp_cd = $data->holiday_emp_cd;
        $dashboard->holiday_year = $data->holiday_year;
        $stmt=$dashboard->userdashboard();
        $num = $stmt->rowCount();
        if($num>0){

            $dashboard_arr=array();
            $dashboard_arr["dashboard"]=array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $dashboard_item = array(
                    "holiday_type_name" => $holday_type_name,
                    "holiday_type_id" => $holday_type,
                    "holiday_count" => $holday_count,
                    "holiday_depleted" => $holday_depleted,
                    "holiday_remaining" => $holday_remaining

                );
                array_push($dashboard_arr["dashboard"], $dashboard_item);
            }
            http_response_code(200);

            echo json_encode($dashboard_arr);
        }else{
            // set response code - 404 Not found
            http_response_code(404);

            // tell the user product does not exist
            echo json_encode(array("Result" => "25","Message" => "No data found for this emp"));
        }
    }
    else {

        // set response code - 400 bad request
        http_response_code(400);

        // tell the user
        echo json_encode(array("Result" => "3",
                               "Message" => "Data is incomplete."));
    }

}

else{
    // set response code - 404 Not found
    http_response_code(404);

    // tell the user product does not exist
    echo json_encode(array("Result"=>"0","Message" => "No matches login info"));
}


?>