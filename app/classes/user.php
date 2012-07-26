<?php

class User {
protected $db;

protected $id, $name, $email, $level, $hasher;

protected $authenticatedFully = false;
protected $company = array();

	public function __construct() {
		global $db;
		$this->hasher = new PasswordHash(12,false);
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
	public function getID() {
		return $this->id;
	}
	public function register($user) {
		global $options;
		$password = substr(md5(hash('sha512', rand(1,1000))), 0, 10);
		
		$user[':password'] = $this->hasher->HashPassword($password);
		$loginUrl =  'http://' . PATH . '/user/login';
		$signature = 'The ' . COMPANY_NAME . ' Team';
		$message = sprintf("Hello, %s\n\nYour login details are: \n\nEmail: %s\nPassword: %s\nCompany: %s\n\nYou will be required to change this after your first login.\n\nPlease visit %s to login!\n\nThank you!\n%s", $user[':user_name'], $user[':email'],$password,$user[':company_id'],$loginUrl, $signature);
		
		$from = "chmacnaughton@gmail.com";
		$to = $user[":email"];
		$user[':name'] = $user[':user_name'];
		unset($user[':user_name']);
		$subject = COMPANY_NAME . " Registration";
		
		$email = new AmazonSES($options);
		echo "<pre>";print_r($user);echo "</pre>";
		$fields = array();
		$values = array();
		$value_names = array();
		foreach($user as $field => $value) {
			$fields[] = trim($field, ':');
			$value_names[] = $field;
		}
		$fields = implode(',',$fields);
		$value_names = implode(',', $value_names);
		$query = "INSERT INTO users ($fields) VALUES ($value_names)";
		//echo $query;
		$stmt = $this->db->prepare($query);
		$result = $stmt->execute($user);
		
		$response = $email->send_email(
			$from,
			array('ToAddresses'=>array(
				$to,
			)),
			array(
				'Subject.Data' => $subject,
				'Body.Text.Data' => $message
			)
		);
		if ($response && $result)
			return true;
	}
	public function login($email, $password, $company) {
		$stmt = $this->db->prepare("SELECT password FROM users WHERE email = :email AND company_id = :id");
		$stmt->execute(array(':email'=>$email, ':id'=>$company));
		$res = $stmt->fetch(PDO::FETCH_ASSOC);
		
		$result = $this->hasher->CheckPassword($password, $res['password']);
		if ($result=== true) {
			$stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email AND company_id = :id");
			$stmt->execute(array(':email'=>$email, ':id'=>$company));
			$res = $stmt->fetch(PDO::FETCH_ASSOC);
			$this->id = $res['id'];
			$this->name = $res['name'];
			$this->level = $res['level'];
			$this->email = $res['email'];
			$this->authenticatedFully = true;
			$stmt = $this->db->prepare("SELECT * FROM companies WHERE id = :id");
			$stmt->execute(array(':id'=>$company));
			$this->company = $stmt->fetch(PDO::FETCH_ASSOC);
			return true;
		}
		else {
			$this->errors[] = l('invalid_email_company_password');
			return false;
		}
	}
	public function logout() {
		session_destroy();
	}
}