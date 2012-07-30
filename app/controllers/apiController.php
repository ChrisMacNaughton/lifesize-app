<?php

class apiController {
	public function __construct($controller, $action, $app, $db) {
		$this->controller = $controller;
		$this->action = $action;
		
		$this->app = $app;
		$this->db = $db;
		global $user;
		$this->user = $user;
	}
	public function beforeAction() {
		
	}
	public function devicesAction() {
		if ($this->user->getLevel() < 3) {
			if (!isset($errors))
			$errors = array(
				'code'=>'401: NO PERMISSION',
				'error'=>"You don't have permission"
			);
		}
		$action = isset($_GET['action']) ? $_GET['action'] : false;
		if (!$action) {
			if (!isset($errors))
			$errors = array(
				'code'=>'400: BAD REQUEST',
				'error'=>"Invalid Request"
				);
			
		}
		if (count($_POST) == 0) {
			if (!isset($errors))
			$errors = array(
				'code'=>'400: BAD REQUEST',
				'error'=>'Malformed Request',
				'details'=>'There are no posted variables'
			);
		}
		if (isset($errors)) {
			header( 'HTTP/1.1 ' . $errors['code']);
				$error = array('error'=>$errors['error'],
				'details'=>$errors['details']);
			die(json_encode($error));
		}
		
		switch($action) {
			case 'enable':
				$stmt = $this->db->prepare("UPDATE devices SET active = :active, modified = :now WHERE id = :id");
				if ($_POST['active'] == 'true')
					$active = 1;
				else if ($_POST['active'] == 'false')
					$active = 0;
				$options = array(
					':id'=>$_POST['id'],
					':active'=>$active,
					':now'=>time()
				);
				$result = $stmt->execute($options);
				
				$err = $stmt->errorInfo();
				$result = array(
					'Status'=>($result) ? "Success":"Error",
					'Errors'=>($result) ? null : array(
						'Code'=>$err[0],
						'DriverError'=>$err[1],
						'DriverMessage'=>$err[2]
					),
					'Updated'=>$_POST['id']
				);
				$enabled = ($active) ?"activated":"de-activated";
				if ($result)
				{
					$stmt = $this->db->prepare("INSERT INTO log (user, action,details,timestamp) VALUES (:user, :action, :details, :now)");
					$stmt->execute(array(
						':user'=>$this->user->getID(),
						':action'=>'enable/disable_device',
						':details'=>"User $enabled " . $_POST['id'],
						':now'=>time()
					));
				}
				echo json_encode($result);
				break;
		}
	}
}