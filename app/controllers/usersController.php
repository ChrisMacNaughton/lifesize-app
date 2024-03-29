<?php

class usersController extends Controller {
	public function __construct($app, $db, $writedb, $redis){
		parent::__construct($app, $db, $writedb, $redis);
		require 'system/classes/passwordHash.php';
		$this->hasher = new PasswordHash(14,false);
	}
	public function indexAction(){
		$data = array(
			'headercolor'=>'9999ff',
		);
		$tmp = $this->db->query("SELECT users.id FROM users INNER JOIN users_companies ON users_companies.user_id = users.id WHERE users_companies.company_id = 'comp-klsdiyr908'")->fetchAll(PDO::FETCH_ASSOC);
		$no_list = array();
		if($this->user->getCompany() != 'comp-klsdiyr908')
			foreach($tmp as $t)$no_list[] = $t['id'];
		$stmt = $this->db->prepare("SELECT users.name, users.id, users.email FROM users INNER JOIN users_companies ON users.id = users_companies.user_id WHERE company_id = :company");
		$stmt->execute(array(':company'=>$this->user->getCompany()));
		$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach($users as $user){
			if(!in_array($user['id'], $no_list)){
				$data['users'][] = $user;
			}
		}
		$this->render("users/index.html.twig", $data);
	}
	public function addAction(){
		$data = array(
			'headercolor'=>'3333ff',
		);
		if(isset($_POST['action']) AND $_POST['action'] == "add"){
			$password = substr(sha1(rand(1,1000) . microtime(true)), 0, 8);
			$pw = $this->hasher->hashPassword($password);
			$company = $this->user->getCompanydetails();
			$user = array(
				'id'=>'user-'.substr(sha1($_POST['name'] . $company['name'] . microtime(true).rand(1,1000)), 0, 10),
				'name'=>$_POST['name'],
				'password'=>$pw,
				'email'=>$_POST['email'],
				'level'=>$_POST['level'],
				'timezone'=>'GMT',
				'companyId'=>$company['id'],
				'registered'=>0
			);
			include dirname(dirname(dirname(__file__))) . '/system/config.php';
			$subject = "Control.VC Registration";
			$message = sprintf("Hello, %s\n\nYour account on Control.VC has been opened.  To login, your account details are:\n\n\tEmail: %s\n\tPassword: %s\n\nWe look forward to helping you manage your video conferencing!\n\nThe ControlVC Team", $user['name'], $user['email'],$password);

			//print("<!--Company: ");print_r($company);print("-->");

			$new_user = $this->user->newUser($user);
			if($new_user){
				$_SESSION['flash'][] = "Success.  The user will receive an email shortly with their login information!";

				$ses = new AmazonSES($options);
				$response = $ses->send_email(
					$email['from'],
					array('ToAddresses'=>array(
						$user['email'],
					)),
					array(
						'Subject.Data' => $subject,
						'Body.Text.Data' => $message
					)
				);
			} else {
				$stmt = $this->db->prepare("SELECT id FROM users WHERE email = :email");
				$stmt->execute(array(':email'=>$user['email']));
				$res = $stmt->fetch(PDO::FETCH_ASSOC);
				$id = $res['id'];
				$stmt = $this->db->prepare("INSERT INTO users_companies (`user_id`,`company_id`,`adda=ed`,`own`) VALUES(:id, :comp, unix_timestamp(), 0)");
				$stmt->execute(array(':id'=>$user['id'], ':comp'=>$user['companyId']));
				$_SESSION['flash'][] = "Success adding this user!";

			}
			session_write_close();
				header("Location: ".PROTOCOL.ROOT."/users/add");
		}
		$data['debug'] = (isset($_GET['debug']))?$_GET['debug']:false;
		$data['levels'] = $this->db->query("SELECT name, id FROM levels WHERE level > 0")->fetchAll(PDO::FETCH_ASSOC);
		$this->render("users/add.html.twig", $data);
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
			if($id == $this->user->getID())
				$redir="me";
			else
				$redir="users/".$id;
			session_write_close();
			header("Location: ".PROTOCOL.ROOT.'/'.$redir);
		}
		if($id == $this->user->getID()){
			$data['me'] = $this->user->getInfo($id);
			$template = "users/profile.html.twig";
		} else {
			$data['users'] = $this->user->getInfo($id);
			$template = "users/view.html.twig";
		}
		$this->render($template, $data);
	}
	public function deleteAction(){
		header("Content-Type: application/json");
		if(!isset($_POST['id'])){
			$this->render("users/delete.json.twig", array("Success"=>false));
		}
		if($_POST['id'] == $this->user->getID()){
			$this->render("users/delete.json.twig", array("Success"=>false));
		}
		$company = $this->user->getCompany();
		$opts = array(':id'=>$_POST['id']);
		$stmt = $this->db->prepare("SELECT own FROM users_companies WHERE user_id = :id AND company_id = :comp");
		$stmt->execute(array(':id'=>$_POST['id'],':comp'=>$company));
		$tmp = $stmt->fetch(PDO::FETCH_ASSOC);
		//remove user from company
		$stmt = $this->db->prepare("DELETE FROM users_companies WHERE user_id = :id AND company_id = :comp");
		$stmt->execute(array(':id'=>$_POST['id'],':comp'=>$company));
		if($tmp['own'] == 1){
			//remove the user completely
			$stmt = $this->db->prepare("DELETE FROM users_companies WHERE user_id = :id");
			$stmt->execute($opts);
			$stmt = $this->db->prepare("DELETE FROM users_alarms WHERE user_id = :id");
			$stmt->execute($opts);
			$stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
			$stmt->execute($opts);
		}
		$this->render("users/delete.json.twig", array("Success"=>true));
	}
	public function editAction($id){
		$data = array(
			'headercolor'=>'6666ff',
		);
		$data['companies'] = $this->user->getCompanies($id);
		if(isset($_POST['update'])){
			switch($_POST['update']){
				case "as":
					$stmt = $this->writedb->prepare("UPDATE users SET `as` = :as WHERE id = :id");
					$stmt->execute(array(':as'=>$_POST['as'], ':id'=>$id));
					$this->user->updateUser();
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
		$this->user->updateUser();
		$data['users'] = $this->user->getInfo($id);
		$template = "users/edit.html.twig";

		$this->render($template, $data);
	}
	public function loginAction(){
		if(isset($_POST['action']) && $_POST['action'] == 'login'){
			$password = $_POST['password'];
			$email = $_POST['email'];
			$rememberme = (isset($_POST['rememberme'])) ? true : false;
			if($this->login($email, $password, $rememberme)){
				header("Location: " . PROTOCOL . ROOT);
				exit();
			} else {
				$_SESSION['errors'][] = $this->error;
				session_write_close();
				sleep(1);
				header("Location: " . PROTOCOL . ROOT."/login");
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
				$this->db->query("UPDATE users SET last_login = unix_timestamp() WHERE id = '" . $user['id'] . "'");
				$return = true;
			}
		} else {
			$this->error = "Invalid Username or Password";
			$return = false;
		}
		return $return;
	}
	public function registerAction(){
		include dirname(dirname(dirname(__file__))) . '/system/config.php';
		if(isset($_POST['action']) AND $_POST['action'] == "sign_up"){
			$email = $_POST['email'];

			$fields = array(
				'entry.0.single'=>urlencode($email),
				'pageNumber'=>'',
				'backupCache'=>'',
				'submit'=>'Submit'
				);
			foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
			rtrim($fields_string, '&');

			$url = "https://docs.google.com/spreadsheet/formResponse?formkey=dERpUzE0RWFsYUJxOHNES29MYzF3bmc6MQ&ifq";

			//open connection
			$ch = curl_init();

			//set the url, number of POST vars, POST data
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_POST, count($fields));
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			//execute post
			$result = curl_exec($ch);
			if(strpos($result, 'Your response has been recorded.')){
				$_SESSION['flash'][] = "Thank you for your interest, we will let you know when an invitation is available!";
			}else {
				$_SESSION['error'][] = "There was a problem with your submission :(  We're looking into it!";
			}
			//close connection
			curl_close($ch);
		}
		if(isset($_POST['action']) AND $_POST['action'] == 'register'){
			unset($_POST['action']);

			$company = array(
				'id'=>'comp-'.substr(sha1($_POST['companyName'] . microtime(true).rand(1,1000)), 0, 10),
				'name'=>$_POST['companyName'],
				'address'=>$_POST['companyAddress'],
				'city'=>$_POST['companyCity'],
				'state'=>$_POST['companyState'],
				'zip'=>$_POST['companyZip'],
				'phone'=>$_POST['phone']
			);
			$password = substr(sha1(rand(1,1000) . microtime(true)), 0, 8);
			$pw = $this->hasher->hashPassword($password);
			$user = array(
				'id'=>'user-'.substr(sha1($_POST['name'] . $_POST['companyName'] . microtime(true).rand(1,1000)), 0, 10),
				'name'=>$_POST['name'],
				'password'=>$pw,
				'email'=>$_POST['email'],
				'level'=>'lev-9rtud568d5',
				'timezone'=>'GMT',
				'companyId'=>$company['id'],
				'registered'=>1
			);
			$subject = "Control.VC Registration";
			$message = sprintf("Hello, %s\n\nYour account on Control.VC has been opened.  To login, your account details are:\n\n\tEmail: %s\n\tPassword: %s\n\nWe look forward to helping you manage your video conferencing!\n\nThe ControlVC Team", $user['name'], $user['email'],$password);

			//print("<!--Company: ");print_r($company);print("-->");

			$new_user = $this->user->newUser($user);
			if($new_user){

				$stmt = $this->db->prepare("INSERT INTO companies (id, created, name, address, city, state, zip, created_by, active, phone) VALUES (:id, unix_timestamp(), :name, :address, :city, :state, :zip, :created_by, 1, :phone)");
				$company['created_by'] = $user['id'];

				$stmt->execute($company);
				$_SESSION['flash'][] = "Success registering, please check your email for your password";

				$ses = new AmazonSES($options);
				$response = $ses->send_email(
					$email['from'],
					array('ToAddresses'=>array(
						$user['email'],
					)),
					array(
						'Subject.Data' => $subject,
						'Body.Text.Data' => $message
					)
				);
				header("Location: ".PROTOCOL.ROOT . '/login');
				exit();
			} else {
				$_SESSION['errors'][] = "Error registering, please use a different email address";
				header("Location: ".PROTOCOL.ROOT.'/register');
				exit();
			}
			//print("<!--App: ");print_r($options);print("-->");
			//print("<!--User: ");print_r($user);print("-->");
		}
		$this->render("users/register.html.twig");
	}
	protected function logout(){
		session_destroy();
		setcookie('controlVC_uid', '', time()-3600,'/', ROOT);
		setcookie('controlVC_hash', '', time()-3600,'/', ROOT);
	}
}