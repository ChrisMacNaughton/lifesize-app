<?php

class userController extends Controller{
	public function __construct($app, $db, $writedb){
		parent::__construct($app, $db, $writedb);
		require 'system/classes/passwordHash.php';
		$this->hasher = new PasswordHash(14,false);
	}
	public function viewAction($id){
		$data = array(
			'headercolor'=>'6666ff',
		);
		$data['companies'] = $this->user->getCompanies($id);
		if(isset($_POST['update'])){
			switch($_POST['update']){
				case "as":
					$stmt = $this->writedb->prepare("UPDATE users SET `as` = :as WHERE id = :id");
					$stmt->execute(array(':as'=>$_POST['as'], ':id'=>$id));
					break;
				case "password":
					if($_POST['password'] == $_POST['password2']){
						$stmt = $this->db->prepare("SELECT password FROM users WHERE id =:id LIMIT 1");
						$stmt->execute(arraY(':id'=>$id));
						$res = $stmt->fetch(PDO::FETCH_ASSOC);
						$p = $res['password'];
						$result = $this->hasher->CheckPassword($_POST['old_pass'], $p);
						$result = true;
						if($result){
							$new_pass = $this->hasher->hashPassword($_POST['password']);
							$stmt = $this->db->prepare("update users SET password = :password WHERE id = :id");
							if($stmt->execute(array(':password'=>$new_pass, ':id'=>$id))){
								$_SESSION['flash'][] = "Success!";
							} else {
								$_SESSION['errors'][] = "Error updating password";
							}
						}else {
							$_SESSION['errors'][] = "Old Password was incorrect";
						}
					} else {
						$_SESSION['errors'][] = "Password and Confirm don't match!";
					}
					break;
			}
		}
		if($id == $this->user->getID()){
			$data['me'] = $this->user->getInfo();
			$template = "users/profile.html.twig";
		} else {
			$template = "users/view.html.twig";
		}
		$this->render($template, $data);
	}
	public function loginAction(){
		if(isset($_POST['action']) && $_POST['action'] == 'login'){
			$password = $_POST['password'];
			$email = $_POST['email'];
			$rememberme = (isset($_POST['rememberme'])) ? true : false;
			if($this->login($email, $password, $rememberme)){
				header("Location: " . PROTOCOL . ROOT);
			} else {
				$_SESSION['errors'][] = $this->error;
			}
		}
		$this->render("users/login.html.twig");
	}
	public function logoutAction(){
		$this->logout();
		header("Location: ".PROTOCOL . ROOT . "/login");
	}
	protected function login($email, $password, $rememberme){
		$stmt = $this->db->prepare("SELECT users.*, levels.name as levelName, levels.level as level FROM users INNER JOIN levels ON users.level = levels.id WHERE email = :email LIMIT 1");
		$stmt->execute(array(':email'=>$email));

		$user = $stmt->fetch(PDO::FETCH_ASSOC);
		$result = $this->hasher->CheckPassword($password, $user['password']);
		if($result === true){
			if(is_null($user['sesshash']) || $user['sesshash'] == ''){
				$hashing = $this->writedb->prepare("UPDATE users SET sesshash = :sesshash WHERE `id` = :id");
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
	protected function logout(){
		session_destroy();
		setcookie('controlVC_uid', '', time()-3600,'/', ROOT);
		setcookie('controlVC_hash', '', time()-3600,'/', ROOT);
	}
}