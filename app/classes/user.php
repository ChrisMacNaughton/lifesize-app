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
	public function register($user) {
		$password = substr(md5(hash('sha512', rand(1,1000))), 0, 10);
		$loginUrl =  'http://' . PATH . '/user/login';
		$signature = 'The ' . COMPANY_NAME . ' Team';
		$emailBody = sprintf("Hello, %s\n\nYour login details are: \n\nEmail: %s\nPassword: %s\n\nYou will be required to change this after your first login.\n\nPlease visit %s to login!\n\nIf the link above doesn't work, copy this link into your browser: %s\n\nThank you!\n%s", $user[':user_name'], $user[':email'],$password,'<a href="' . $loginUrl . '">Login</a>', $loginUrl, $signature);
		
		echo $emailBody;
		//use amazon email function (amazon SES) to send registration email
	}
	public function login($email, $password) {
		
	}
	public function logout() {
		session_destroy();
	}
}