<?php

require_once("../config.php");

class dal {

	public $db;

	public function __construct() {
		global $config_db;
		try {
			$conn = "mysql:host=".$config_db["server"].";dbname=".$config_db["database"];
			$this->db = new PDO($conn, $config_db["username"], $config_db["password"]);
		}
		catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public function __destruct() {
		$this->db = null;
	}

}



?>