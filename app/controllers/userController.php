<?php

class userController extends Controller{
	public function __construct($app, $db){
		parent::__construct($app, $db);
		require 'system/classes/passwordHash.php';
		$this->hasher = new PasswordHash(14,false);
	}
	public function loginAction(){
		if(isset($_POST['action']) && $_POST['action'] == 'login'){
			$password = $_POST['password'];
			$email = $_POST['email'];
			$rememberme = (isset($_POST['rememberme'])) ? true : false;
			if($this->login($email, $password, $rememberme)){

			} else {
				$_SESSION['errors'][] = $this->error;
			}
		}
		$this->render("user/login.html.twig");
	}
	protected function login($email, $password, $rememberme){
		$stmt = $this->db->prepare("SELECT users.*, levels.name as levelName, levels.level as level FROM users INNER JOIN levels ON users.level = levels.id WHERE email = :email LIMIT 1");
		$stmt->execute(array(':email'=>$email));

		$user = $stmt->fetch(PDO::FETCH_ASSOC);
		$result = $this->hasher->CheckPassword($password, $user['password']);
		if($result === true){
			if(is_null($user['sesshash']) || $user['sesshash'] == ''){
				$hashing = $this->db->prepare("UPDATE users SET sesshash = :sesshash WHERE `id` = :id");
				$hash = hash('sha512', $user['password'].microtime(true).$user['email']);
				$res = $hashing->execute(array(
					':id'=>$user['id'],
					':sesshash'=>$hash
				));
			} else {
				$hash = $user['sesshash'];
				$res = true;
			}
			if($res){
				if($rememberme){
					$expires = time() + 60*60*24*7*2;
				} else {
					$expires = 0;
				}
				if(DEV_ENV)
					$secure = false;
				else
					$secure = true;

				setcookie('controlVC_uid', $user['id'], $expires,'/', ROOT, $secure);
				setcookie('controlVC_hash', $hash, $expires,'/', ROOT, $secure);
				$return = true;
			}
		} else {
			$this->error = "Invalid Username or Password";
			$return = false;
		}
		return $return;
	}
}