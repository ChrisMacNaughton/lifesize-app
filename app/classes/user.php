<?php

class User {
protected $db;

protected $id, $name, $email, $level;

protected $company = array();

	public function __construct() {
		global $db;
		$this->db = $db;
		$id = (isset($_SESSION['userid'])) ? $_SESSION['userid'] : 0;
		$sesshash = (isset($_SESSION['sesshash'])) ? $_SESSION['sesshash'] : '';
		
		$stmt = $this->db->prepare("SELECT * FROM users WHERE id=:id AND sesshash = :sesshash");
		$stmt->execute(array(
			':id'=>$id,
			':sesshash'=>$sesshash
		));
		$user = $stmt->fetch(PDO::FETCH_ASSOC);
		foreach ($user as $key=>$val) {
			$this->$key = $val;
		}
	}
	public function register($data) {
	
	}
	public function login($email, $password) {
		
	}
	public function logout() {
		session_destroy();
	}
}