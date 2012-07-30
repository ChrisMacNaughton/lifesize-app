<?php

class userController extends Controller {
	public function loginAction() {
		$data = array('title'=>'Login');
		if (isset($_POST['action']) && $_POST['action'] == 'login') {
			if ($this->user->login($_POST['email'], $_POST['password'], $_POST['company'])) {
				
				if ($this->user->reset()) {
					$_SESSION['flash'][] = l('reset_password');
					session_write_close();
					header("Location: /user/edit/" . $this->user->getId());
				} else {
					session_write_close();
					header("Location: /user/view/" . $this->user->getId());
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
		print_r($user);
		$result = $this->user->register($user);
		print_r($result);
		$stmt = $this->db->prepare("INSERT INTO log (user, action,details,timestamp) VALUES (:user, :action, :details, :now)");
					$stmt->execute(array(
						':user'=>$this->user->getID(),
						':action'=>'add_user',
						':details'=>"User added a new user: " . $user[':name'],
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
			
			$from = "chmacnaughton@gmail.com";
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
		if ($res === false && $this->user->getLevel() < 4) {
			$_SESSION['error'][] = l('no_permission');
			session_write_close();
			header("Location: /user/view/" . $this->user->getID());
		}
		$data['user'] = $res;
		$this->render('users/viewOne.html.twig', $data);
	}
	public function editAction($id){
	
			$data = array(
				'title'=>"Edit"
			);
		if ($this->user->getLevel() < 3 && $this->user->getID() != $id )
		{
			$_SESSION['error'][] = l('no_permission');
			session_write_close();
			header("Location: /user/view/" . $this->user->getID());
		}
		if (isset($_POST['action']) && $_POST['action'] == 'editPassword'){
			
			$result = $this->user->changePass($id, $_POST['old_pass'], $_POST['password'], $_POST['password2']);
			
			$errors = $this->user->getErrors();
			if ($result === false) {
				foreach ($errors as $err)
					$data['errors'][] = $err;
			}
		}
		if ($this->user->getID() == $id) {
			$this->render('users/edit.html.twig', $data);
		}
	}
	public function logoutAction() {
		$this->user->logout();
	}
}