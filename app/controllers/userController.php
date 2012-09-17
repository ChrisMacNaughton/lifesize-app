<?php

class userController extends Controller {
	public function loginAction() {
		if ($this->user->isAuthenticatedFully()) {
			session_write_close();
			header("Location: /user/view/" . $this->user->getId());
		}
		$data = array('title'=>'Login');
		if (isset($_POST['action']) && $_POST['action'] == 'login') {
			if ($this->user->login($_POST['email'], $_POST['password'], $_POST['company'])) {
				
				if ($this->user->reset()) {
					$_SESSION['flash'][] = l('reset_password');
					session_write_close();
					header("Location: /user/edit/" . $this->user->getId());
				} else {
					session_write_close();
					header("Location: /devices");
				}
			} else {
				$_SESSION['errors'] = $this->user->errors;
			}
		}
		$this->render('users/login.html.twig', $data);
	}
	public function newAction() {
	if ($this->user->getLevel() > 2) {
		$user = array(
			':user_name'=>$_POST['name'],
			':email'=>$_POST['email'],
			':level'=>$_POST['level'],
			':company_id' => $this->user->getCompanyID(),
		);
		//print_r($user);
		$result = $this->user->register($user);
		//print_r($result);
		echo json_encode($result);
		$stmt = $this->db->prepare("INSERT INTO log (user, action,details,timestamp) VALUES (:user, :action, :details, :now)");
					$stmt->execute(array(
						':user'=>$this->user->getID(),
						':action'=>'add_user',
						':details'=>$result['id'],
						':now'=>time()
					));
					
		}
	}
	public function resetAction(){
	$data['title'] = "Reset Password";
	if (isset($_POST['email'])) {
		$stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email AND company_id = :company");
		$res = $stmt->execute(array(
			':email'=>$_POST['email'],
			':company'=>$_POST['company']
		));
		if (!$res)
			$_SESSION['errors'][] = l('email_company_nomatch');
		else {
			global $options;
			$user = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$user = $user[0];
			$email = $user['email'];
			
			$new_pass = substr(md5(hash('sha512', rand(1,1000))), 0, 10);
			$stmt = $this->db->prepare("UPDATE users SET password = :pass, reset = 1 WHERE id = :id");
			$stmt->execute(array(
				':id'=>$user['id'],
				':pass'=>$this->user->hashPass($new_pass)
			));
			$loginUrl =  'http://' . PATH . '/user/login';
			
			$signature = 'The ' . COMPANY_NAME . ' Team';
			$message = sprintf("Hello, %s\n\nYour password has been reset! Your login details are now: \n\nEmail: %s\nPassword: %s\nCompany: %s\n\nYou will be required to change this after your first login.\n\nPlease visit %s to login!\n\nThank you!\n%s", $user['name'], $user['email'],$new_pass,$user['company_id'],$loginUrl, $signature);
			
			$from = "no-reply@control.vc";
			$to = $user["email"];
			$subject = COMPANY_NAME . " Support";
			$email = new AmazonSES($options);
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
			if ($response) {
				$_SESSION['flash'][] = l('success_resetting_password');
				session_write_close();
				header("Location: /user/login");
			}
		}
	}
		$this->render('users/reset.html.twig', $data);
	}
	public function viewAction($id) {
		$data = array('title'=>"User");
		$res = $this->user->getUserInfo($id);
		if ($res === false && $this->user->getLevel() < 3) {
			$_SESSION['error'][] = l('no_permission');
			session_write_close();
			header("Location: /user/view/" . $this->user->getID());
		}
		$stmt = $this->db->prepare("SELECT name, users.id AS id FROM users INNER JOIN log ON users.id = log.user WHERE log.action='add_user' AND details = :userid");
		$stmt->execute(array(':userid'=>$id));
		
		$data['user'] = $res;
		$data['user']['creator'] = $stmt->fetch(PDO::FETCH_ASSOC);
		$data['user']['gravatar'] = md5(strtolower(trim($data['user']['email'])));
		$this->render('users/viewOne.html.twig', $data);
	}
	public function editAction($id){
	
			$data = array(
				'title'=>"Edit"
			);
			$data['user'] = $this->user->getUserInfo($id);
		if ($this->user->getLevel() < 3 && $this->user->getID() != $id )
		{
			$_SESSION['error'][] = l('no_permission');
			session_write_close();
			header("Location: /user/view/" . $this->user->getID());
		}
		if (isset($_POST['action'])){
			if($_POST['action'] == 'editPassword'){
				$result = $this->user->changePass($id, $_POST['old_pass'], $_POST['password'], $_POST['password2']);
				
				$errors = $this->user->getErrors();
				if ($result === false) {
					foreach ($errors as $err)
						$data['errors'][] = $err;
				}
			}
			else if ($_POST['action'] == 'editLevel') {
				
				$stmt = $this->db->prepare("UPDATE users SET level = :level WHERE id = :id");
				$options = array(':level'=>$_POST['level'], ':id'=>$data['user']['id']);
				$stmt->execute($options);
				echo "<!-- ";print_r($options);print_r($stmt->errorInfo()); echo"-->";
				header("Location: /user/edit/".$id);
			}
		}
		if ($this->user->getID() == $id) {
			$page = 'users/edit.html.twig';
		} else {
			$page = 'users/editOther.html.twig';
		}
		$this->render($page, $data);
	}
	public function logoutAction() {
		$this->user->logout();
	}
}