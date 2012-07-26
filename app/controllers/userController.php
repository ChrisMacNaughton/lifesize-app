<?php

class userController extends Controller {
	public function loginAction() {
		$data = array('title'=>'Login');
		if (isset($_POST['action']) && $_POST['action'] == 'login') {
			echo "<pre>";var_dump($_POST);echo "</pre>";
			if ($this->user->login($_POST['email'], $_POST['password'], $_POST['company'])) {
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
		echo $id;
	}
}