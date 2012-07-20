<?php

class User {
	
	protected $userid, $name, $email, $level;
	protected $company_id;
	protected $hasher;
	protected $db;
	protected $authenticatedFully = false;
	public function __construct($db) {
		$this->authenticatedFully = false;
		$this->db = $db;
		$this->hasher = new PasswordHash(10, FALSE);
		
		if(!isset($_COOKIE['vc-control_u'])) $_COOKIE['vc-control_u'] = '';
		if(!isset($_COOKIE['vc-control_h'])) $_COOKIE['vc-control_h'] = '';
		
		$stmt = $this->db->prepare("SELECT * FROM users WHERE email=:email AND sesshash=:sesshash LIMIT 1");
		
		$login = $stmt->execute(array(
				':email'=>$_COOKIE['vc-control_u'],
				':sesshash'=>$_COOKIE['vc-control_h']
			));
			$user = $stmt->fetchAll();
		if (count($user) > 0) {
			$user = $user[0];
			$this->userid = $user['id'];
			$this->name = $user['name'];
			$this->email = $user['email'];
			
			$this->company_id = $user['company_id'];
			
			$this->level = $user['level'];
			$this->authenticatedFully = true;
		} else {
			$this->userid = 0;
			$this->name = 'guest';
			$this->level = 0;
			$this->authenticatedFully = false;
		}
	}
	public function getCompany() {
		return $this->company_id;
	}
	public function isAuthenticated() {
		return $this->authenticatedFully;
	}
	public function userInfo() {
		return array(
			'name'=>$this->name,
			'email'=>$this->email,
			'level' => $this->level,
			'id' => $this->userid,
			'company_id' => $this->company_id
		);
	}
	public function login($email, $company, $password, $remember) {
		$email = mysql_real_escape_string($email);
		$company = mysql_real_escape_string($company);
		$password = mysql_real_escape_string($password);
		/*
		if ($email == 'chmacnaughton@gmail.com') {
			$this->register(array('email'=>$email, 'company'=>$company, 'password'=>$password, 'password2'=>$password, 'name'=>'chris'));
		}
		*/
		$stmt = $this->db->prepare("SELECT password, company_id, active, id FROM users WHERE email = :email LIMIT 1");
		$stmt->execute(array(':email'=>$email));
		
		$userInfo = $stmt->fetchAll();
		if (!count($userInfo) > 0) {
			$_SESSION['errors'][] = "Invalid login credentials";
			return false;
		}
		$userInfo = $userInfo[0];
		//echo "<pre>";	var_dump($userInfo); echo "</pre>";
		$stmt = $this->db->prepare("SELECT slug FROM companies WHERE id = :id");
		$stmt->execute(array(':id'=>$userInfo['company_id']));
		$slug = $stmt->fetchAll();
		$slug = $slug['0']['slug'];
		//echo $slug;
		if ($slug != $company) {
		//echo "$slug == $company";
			$_SESSION['errors'][] = "Company doesn't exist";
			return false;
		}
		$check = $this->hasher->CheckPassword( $password, $userInfo['password'] );
		if (!$check) {
			$_SESSION['errors'][] = "Invalid login credentials";
			return false;
		}
		
		if ($userInfo['active']) {
			$stmt = $this->db->prepare("UPDATE users SET sesshash = :sesshash, last_login = :last_login WHERE id = :id LIMIT 1");
			$sesshash = hash('sha512', $userInfo['password'] . time() . $email);
			$stmt->execute(array(
				':sesshash'=> $sesshash,
				':last_login'=>time(),
				':id'=>$userInfo['id']
			));
			if ($remember) {
				setcookie('vc-control_u', $email, time() + 9999999, '/');
				setcookie('vc-control_h', $sesshash, time() + 9999999, '/');
				setcookie('vc-control_remember', 1, time() + 9999999, '/');
			} else {
				setcookie('vc-control_u', $email, 0, '/');
				setcookie('vc-control_h', $sesshash,0, '/');
				setcookie('vc-control_remember', 0, 0, '/');
			}
			return true;
		} else {
			$_SESSION['errors'][] = l('error_user_inactive');
			return false;
		}
		
	}
	
	public function logout() {
		setcookie('vc-control_u','',0,'/');
		setcookie('vc-control_h','',0,'/');
		setcookie('vc-control_remember',0,0,'/');
		$this->authenticatedFully = false;
	}
	
	public function register($data) {
		$errors = array();
		$stmt = $this->db->prepare("SELECT username FROM users WHERE email= :email LIMIT 1");
		$result = $stmt->execute(array(':email'=>$data['email']));
		//var_dump($result);
		if ($result) 
			$errors[] = l('Error_email_taken');
		
		$stmt = $this->db->prepare("SELECT id FROM companies WHERE slug = :slug");
		$stmt->execute(array(':slug'=>$data['company']));
		$company = $stmt->fetchAll();
		$data['company_id'] = $company[0]['id'];
		unset($data['company']);
		if (empty($data['company_id'])) 
			$errors['company'] = l('error_company_empty');
		
		if(empty($data['password']))
			$errors['password'] = l('error_password_empty');
		
		if($data['password'] != $data['password2'])
			$errors['password'] = l('error_password_nomatch');
		
		if(empty($data['email']))
			$errors['email'] = l('error_email_empty');
			
		if(count($errors) > 0)
		{
			foreach ($errors as $error) {
				$_SESSION['errors'][] = $error;
			}
			return false;
		}
		
		unset($data['password2']);
		
		$data['password'] = $this->hasher->HashPassword( $data['password'] );
		$data['created'] = time();
		// Build the query
			$fields = array();
			$values = array();
			$value_names = array();
			foreach($data as $field => $value) {
				$fields[] = $field;
				$value_names[] = ":" . $field;
				$values[":$field"] = $value;
			}
			$fields = implode(',',$fields);
			//$values = implode(',',$values);
			$value_names = implode(',', $value_names);
			$query = "INSERT INTO users($fields) VALUES($value_names)";
			echo "<!-- $query -->";
			echo "<!-- "; print_r($values); echo "-->";
			$stmt = $this->db->prepare($query);
			$stmt->execute($values);
	}
}