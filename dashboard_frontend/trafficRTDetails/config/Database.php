<?php
class Database{

    private $host = "localhost:3306";
    private $db_name = "Dashboard";
    private $username = "root";
    private $password = "root";
    public $conn;

    // get the database connection
    public function getConnection(){

        $this->conn = null;

        try{
            $this->conn = mysqli_connect($this->host, $this->username, $this->password);    // MOD DB
            mysqli_select_db($this->conn, $this->db_name);
         //   $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
         //   $this->conn->exec("set names utf8");
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>