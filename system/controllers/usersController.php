<?php

class usersController extends Controller{
	public function indexAction() {
		$stmt = $this->db->prepare("SELECT * FROM users WHERE company_id = :company_id ORDER BY level");
		$stmt->execute(array(
			':company_id'=>$this->user->getCompany()
		));
		$users = $stmt->fetchAll();
		$data = array(
			'users'=>$users,
			'title'=>'Users'
		);
		render('users/index.html.twig', $data);
	}
	public function viewAction($id) {
		$stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id AND company_id = :company_id");
		$stmt->execute(
			array(
				':id'=>$id,
				':company_id'=>$this->user->getCompany()
			)
		);
		$users = $stmt->fetch();
		$data = array(
			'title'=>'User Details for ' . $users['name'],
			'users'=>$users
		);
		render('users/view.html.twig', $data);
	}
	public function editAction($id) {
		if (!($this->user->getLevel() > OPERATOR_LEVEL)) {
			$_SESSION['error'][] = l("no_permission");
			header("Location: /home");
		} else {
		if (isset($_POST['action']) && $_POST['action'] == 'edit') {
			$data = array();
			if ($_POST['password'] != '') {
				$password = $_POST['password'];
				$password2 = $_POST['password2'];
				if ($password != $password2) {
					$_SESSION['errors'][] = l('password_mismatch');
					header("Location: users/view/" . $id);
				}
				else {
					$data['password'] = $password;
				}
			}
			$data['name'] = $_POST['name'];
			$data['email'] = $_POST['email'];
			$data['active'] = ($_POST['active'] == true) ? 1 : 0;
			
			if ($this->user->editUser($id, $data)) {
			$_SESSION['flash'][] = l("success_edit_user");
			} else {
				$_SESSION['errors'][] = l("error_edit_user");
			}
			header("Location: /users/view/" . $id);
		}}
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
	public function newAction() {
		if (!($this->user->getLevel() > OPERATOR_LEVEL)) {
			$_SESSION['error'][] = l("no_permission");
			header("Location: /home");
		}
		if (isset($_POST['action']) && $_POST['action'] == 'new') {
		
		}
		render('users/new.html.twig', array(
			'title'=>"New User",
		));
	}
	public function logoutAction() {
		$this->user->logout();
	}
}