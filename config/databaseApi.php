<?php
/**
 * Created by PhpStorm.
 * User: MOH
 * Date: 05/10/2020
 * Time: 04:52 م
 */

class DatabaseApi
{

//    private $host = "localhost";
//    private $db_name = "hr";
//    private $username = "root";
//    private $password = "";

    private $host = "unity1.ml";
    private $db_name = "unity1_hr";
    private $username = "unity1_hr";
    private $password = "kdXwUC)Ti$3f";


    public $conn;

    // get the database connection
    public function getConnection(){

        $this->conn = null;

        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}