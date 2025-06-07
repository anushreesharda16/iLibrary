<?php

class Database 
{
   private $servername = "localhost";
   private $username = "root";
   private $password = "";
   private $db_name = "library_db";
   public $conn;
   public function connect() 
   {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->db_name);
        if($this->conn->connect_error) 
        {
            die("Connection failed.");
        }
        else 
        {
            return $this->conn;
        }
   }
}

?>