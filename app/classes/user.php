<?php

class User {
protected $db;

protected $id, $name, $email, $level, $phone, $hasher;

protected $authenticatedFully = false;
protected $company = array();

	public function __construct() {
		global $db;
		$this->hasher = new PasswordHash(12,false);
		$this->db = $db;
		$id = (isset($_COOKIE['userid'])) ? $_COOKIE['userid'] : 0;
		$hash = (isset($_COOKIE['hash'])) ? $_COOKIE['hash'] : 0;
		
		//echo "<!--";print_r($_SESSION);echo"-->";
		$stmt = $this->db->prepare("SELECT * FROM users WHERE id=:id AND sesshash = :hash");
		$stmt->execute(array(
			':id'=>$id,
			':hash'=>$hash
		));
		$user = $stmt->fetch(PDO::FETCH_ASSOC);
		foreach ($user as $key=>$val) {
			$this->$key = $val;
		}
		if ($this->id != 0 && $this->id != null) {
			$this->authenticatedFully = true;
			$stmt = $this->db->prepare("SELECT * FROM companies WHERE id = :id");
			$stmt->execute(array(':id'=>$this->company_id));
			$this->company = $stmt->fetch(PDO::FETCH_ASSOC);
			unset($this->password);
		}
	}
	public function hashPass($pass) {
		return $this->hasher->HashPassword($pass);
	}
	public function reset() {
		return (bool)$this->reset;
	}
	public function changePass($id, $old_pass, $password, $password2) {
		$stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
		$stmt->execute(array(
			':id'=>$id
		));
		$res = $stmt->fetch(PDO::FETCH_ASSOC);
		
		if (!$this->hasher->CheckPassword($old_pass, $res['password'])) {
			$errors[] = l('old_password_nomatch');
		}
		if ($password != $password2) {
			$errors[] = l('password_nomatch');
		}
		$valid = $this->is_valid_password($password);
		if ($valid === false) {
		foreach ($this->errors['password'] as $err) {
			$errors[] = $err;
		}
			unset($this->errors['password']);
		}
		if (count($errors) == 0) {
			$hashed = $this->hasher->HashPassword($password);
			$stmt = $this->db->prepare("UPDATE users SET password = :password, reset = 0 WHERE id = :id");
			$options = array(
				':id'=>$id,
				':password'=>$hashed
			);
			
			$result = $stmt->execute($options);
			
		}
		 
		
		if ($result === false) {
			$errors[] = $this->db->errorInfo();
		}
		
		if (count($errors) == 0) {
			return true;
		} else {
			$this->errors = $errors;
			return false;
		}
	}
	protected function is_valid_password($password) {
		if (strlen($password) < 8) {
			$this->errors['password'][] = l('error_password_length');
		}
		
		
		if (count($this->errors['password']) == 0)
			return true;
		else
			return false;
	}
	public function getErrors() {
		return $this->errors;
	}
	public function getUserInfo($id) {
		$stmt = $this->db->prepare("SELECT id, name, company_id, email, phone, level, created FROM users WHERE id = :id");
		$stmt->execute(array(':id'=>$id));
		$res = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($res['company_id'] == $this->company_id)
			return $res;
		else 
			return false;
	}
	public function getUser() {
		$user = array(
			'id'=>$this->id,
			'name'=>$this->name,
			'email'=>$this->email,
			'level'=>$this->level,
			'phone'=>$this->phone,
			'company'=>$this->company,
			'auth'=>$this->authenticatedFully
		);
		return $user;
	}
	public function getID() {
		return $this->id;
	}
	public function getLevel() {
		return $this->level;
	}
	public function isAuthenticatedFully() {
		return $this->authenticatedFully;
	}
	public function getCompany($id = null){
		if (is_null($id)) {
			return $this->company['name'];
		}
	}
	public function getCompanyId(){
		return $this->company['id'];
	}
	public function getCompanyDetails() {
		return $this->company;
	}
	public function register($user) {
		global $options;
		$password = substr(md5(hash('sha512', rand(1,1000))), 0, 10);
		$user[':id'] = 'usr-' . substr(hash('sha512', rand(1,1000)), 0, 10);
		$user[':password'] = $this->hasher->HashPassword($password);
		$loginUrl =  'http://' . PATH . '/user/login';
		$signature = 'The ' . COMPANY_NAME . ' Team';
		$message = sprintf("Hello, %s\n\nYour login details are: \n\nEmail: %s\nPassword: %s\nCompany: %s\n\nYou will be required to change this after your first login.\n\nPlease visit %s to login!\n\nThank you!\n%s", $user[':user_name'], $user[':email'],$password,$user[':company_id'],$loginUrl, $signature);
		
		$from = "no-reply@control.vc";
		$to = $user[":email"];
		$user[':name'] = $user[':user_name'];
		unset($user[':user_name']);
		$subject = COMPANY_NAME . " Registration";
		$user[':reset'] = 1;
		$user[':created'] = time();
		$email = new AmazonSES($options);
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
		$userid = $user[':id'];
		//print_r($stmt->errorInfo());
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
			return array('id'=>$userid, 'name'=>$user[':name']);
		else
			return false;
	}
	public function login($email, $password, $company, $rememberme = false) {
		$stmt = $this->db->prepare("SELECT password FROM users WHERE email = :email AND company_id = :id");
		$stmt->execute(array(':email'=>$email, ':id'=>$company));
		$res = $stmt->fetch(PDO::FETCH_ASSOC);
		
		$result = $this->hasher->CheckPassword($password, $res['password']);
		if ($result=== true) {
			$stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email AND company_id = :id");
			$stmt->execute(array(':email'=>$email, ':id'=>$company));
			$user = $stmt->fetch(PDO::FETCH_ASSOC);
			//create sesshash
			if(is_null($user['sesshash']) || $user['sesshash'] == ''){
				$hashing = $this->db->prepare("UPDATE users SET sesshash = :sesshash WHERE `id` = :id");
				$hash = hash('sha512', $user['password'].time().$user['email']);
				$res = $hashing->execute(array(
					':id'=>$user['id'],
					':sesshash'=>$hash
				));
			} else {
				$hash = $user['sesshash'];
				$res = true;
			}
			if ($res){
				//$_COOKIE['hash'] = $hash;
				
			//set local object vars
			foreach ($user as $key=>$val) {
				$this->$key = $val;
			}
			if($rememberme){
				$expires = time() + 60*60*24*7*2;
			} else {
				$expires = 0;
			}
			if(DEV_ENV)
				$secure = true;
			else
				$secure = false;
			setcookie('userid', $user['id'], $expires,'/', $secure);
			setcookie('hash', $hash, $expires,'/', $secure);

			$this->authenticatedFully = true;
			$stmt = $this->db->prepare("SELECT * FROM companies WHERE id = :id");
			$stmt->execute(array(':id'=>$this->company_id));
			$this->company = $stmt->fetch(PDO::FETCH_ASSOC);
			unset($this->password);
			
			return true;
			}
		}
		else {
			$this->errors[] = l('invalid_email_company_password');
			return false;
		}
	}
	public function logout() {
		session_destroy();
		setcookie('userid', '', 0,'/');
		setcookie('hash', '', 0,'/');
		$this->db->query("UPDATE users SET sesshash = null WHERE id = '" . $this->id . "'");
		header("Location: /user/login?errors=" . urlencode($this->db->errorInfo()));
	}
}