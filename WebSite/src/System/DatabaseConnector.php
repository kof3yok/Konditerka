<?php
class Database{
  
    // Указываем свои собственные учетные данные базы данных
    private $host = "185.86.15.144";
    private $port = "3307";
    private $db_name = "pirogapp";
    private $username = "admin";
    private $password = "Demre0735.";
    public $conn;
  
    // Получение соединения с базой данных
    public function getConnection(){
  
        $this->conn = null;
  
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";port=". $this->port. ";dbname=" . $this->db_name, $this->username, $this->password);
            //$this->conn->exec("set names utf8");
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
  
        return $this->conn;
    }
}
?>
