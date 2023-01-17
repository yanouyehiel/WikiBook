<?php

class Database{
 
	private $server = "mysql:host=localhost;dbname=social";
	private $username = "root";
	private $password = "root";
	private $options  = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,);
	protected $conn;
 	
	public function open(){
 		try{
 			$this->conn = new PDO($this->server, $this->username, $this->password, $this->options);
 			return $this->conn;
 		}
 		catch (PDOException $e){
 			echo "Il y'a quelques problèmes de connexion: " . $e->getMessage();
 		}
    }
	public function close(){
   		$this->conn = null;
 	}
 
}

$pdo = new Database();
 
?>