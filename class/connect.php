<?php
session_start();
header('Access-Control-Allow-Origin: http://localhost');
// ระหว่าง dev

class connectDb
{
    public $username = "root";
    public $password = "";
    public $db = "pos-portfolio";
    public $host = "127.0.0.1";
    public $conn;
    function __construct()
    {
        try {
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->db", $this->username, $this->password);
            // set the PDO error mode to exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // echo "Connected successfully";
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }
}
