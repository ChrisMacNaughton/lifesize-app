<?php

class userController extends Controller {
	public function loginAction() {
		$data = array('title'=>'Login');
		if (isset($_POST['action']) && $_POST['action'] == 'login') {
			if ($this->user->login($_POST['email'], $_POST['password'], $_POST['company'])) {
				session_write_close();
				header("Location: /user/view/" . $this->user->getId());
			} else {
				$errors = $this->user->errors;
			}
		}
		$this->render('users/login.html.twig', $data);
	}
	public function newAction() {
		//$this->user->register($user);
	}
	public function viewAction($id) {
		$data = array('title'=>"User");
		$res = $this->user->getUserInfo($id);
		if ($res === false && $this->user->getLevel() < 4) {
			$_SESSION['error'][] = l('no_permission');
			session_write_close();
			header("Location: /user/view/" . $this->user->getID());
		}
		$this->render('users/viewOne.html.twig', $data);
	}
	public function logoutAction() {
		$this->user->logout();
	}
}