<?php
// show error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// home page url
$home_url="http://unity1.ml/api/employees/";

// page given in URL parameter, default page is one
//$page = isset($_GET['page']) ? $_GET['page'] : 1;
$data = json_decode(file_get_contents("php://input"));
$page=$data->page_no;
// set number of records per page
$records_per_page = 10;

// calculate for the query LIMIT clause
$from_record_num = ($records_per_page * $page) - $records_per_page;
?>