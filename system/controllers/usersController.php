<?php

class usersController extends Controller{
	public function indexAction() {
		$data = array(
			'title'=>'Users'
		);
		render('users/index.html.twig', $data);
	}
	public function loginAction() {
		if ($this->user->isAuthenticated()) {
			header('Location: /users');
		}
		if (isset($_POST['action']) && $_POST['action'] == 'login') {
			$email = $_POST['email'];
			$company = $_POST['company'];
			$password = $_POST['password'];
			$remember = (isset($_POST['remember'])) ? 1 : 0;
			if ( $this->user->login($email, $company, $password, $remember)) {
				header('Location: /users');
			}
		}
		render('users/login.html.twig',array());
	}
	public function logoutAction() {
		$this->user->logout();
	}
}