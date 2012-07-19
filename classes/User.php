<?php

class User {
	
	protected $user = array();
	protected $hasher;
	protected $db;
	public function __construct($db) {
		$this->db = $db;
		$this->hasher = new PasswordHash(10, FALSE);
	}
	public function isAuthenticated() {
		return false;
	}
	public function login($username, $company, $password, $rememberme) {
	
		echo "Response: ";
		$response = $this->db->get_item(array(
			'TableName'=>'users',
			'Key'=> $this->db->attributes(array(
				'HashKeyElement'=>mysql_real_escape_string($username)
			)),
			'company'=> $this->db->attributes(array(
				'HashKeyElement'=>mysql_real_escape_string($company)
			)),
			'AttributesToGet'=>array('Name','password')
		));
		echo "<pre>";
		if ($response->isOK()) {
		var_dump($response);
			$response = $response->body;
			try {
				$verify = $response->Item->password->S->to_string();
			} catch(Exception $e) {
				$_SESSION['errors'][] = "Invalid username, company, or password";
			}
		} else {
			print_r($response);
		}
		echo "</pre>";
	}
}