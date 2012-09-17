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
	public function alarmsAction() {
		$action = $this->init(1);
		$user_id = $this->user->getID();
		$alarm_id = $_POST['id'];
		$device_id = $_POST['device'];
		if ($_POST['active'] == 'true')
			$active = 1;
		else if ($_POST['active'] == 'false')
			$active = 0;
		switch($action) {
			case "toggle":
				$stmt = $this->db->prepare("SELECT * FROM devices_alarms WHERE user_id = :id AND alarm_id = :alarm LIMIT 1");
				$stmt->execute(array(':id'=>$user_id, ':alarm'=>$alarm_id));
				$res = $stmt->rowCount();
				if ($res == 0) {
					$query = "INSERT INTO devices_alarms (alarm_id, device_id, user_id, active) VALUES (:alarm, :device, :user, :active)";
				} else {
					$query = "UPDATE devices_alarms SET active = :active WHERE alarm_id = :alarm AND device_id = :device AND user_id = :user";
				}
				$stmt = $this->db->prepare($query);
				//print_r($stmt);
				$options = array(
					':active'=>$active,
					':user'=>$user_id,
					':alarm'=>$alarm_id,
					':device'=>$device_id
					);
				//print_r($options);
				$stmt->execute($options);
				$ret = $stmt->rowCount();
				if($ret == 1) {
					echo "True";
				} else {
					echo "False";
				}
				//echo "SET active = " . $active . " " . $alarm_id . " for " . $user_id;
				break;

		}
	}
	public function eventsAction() {
		$action = $this->init(2);
		switch($action) {
			case 'delete':
				$options = array(
					':id'=>$_POST['id']
				);
				$stmt = $this->db->prepare("SELECT notes FROM events WHERE id = :id");
				$stmt->execute($options);
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt = $this->db->prepare("DELETE FROM events WHERE id = :id");
				$result = $stmt->execute($options);
				
				$err = $stmt->errorInfo();
				$result = array(
					'Status'=>($result) ? "Success":"Error",
					'Errors'=>($result) ? null : array(
						'Code'=>$err[0],
						'DriverError'=>$err[1],
						'DriverMessage'=>$err[2]
					),
					'Deleted'=>$_POST['id']
				);
				$stmt = $this->db->prepare("INSERT INTO log (user, action,details,timestamp) VALUES (:user, :action, :details, :now)");
					$stmt->execute(array(
						':user'=>$this->user->getID(),
						':action'=>'delete_event',
						':details'=>"User deleted event: " . $options[':id'] . ", notes: " . $data['notes'],
						':now'=>time()
					));
				echo json_encode($result);
				break;
		}
	}
	protected function init($permission) {
		if ($this->user->getLevel() < $permission) {
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
		return $action;
	}
	public function userAction() {
		$action = $this->init(3);
		$id = $_POST['id'];

		if($_GET['action'] == "delete") {
			$stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
			$res = $stmt->execute(array(
				':id'=>$id
				));
			$stmt = $this->db->prepare("INSERT INTO log (user, action,details,timestamp) VALUES (:user, :action, :details, :now)");
				$stmt->execute(array(
					':user'=>$this->user->getID(),
					':action'=>'delete_user',
					':details'=>$id,
					':now'=>time()
				));
		}
		echo json_encode($res);
		
	}
	public function devicesAction() {
		$action = $this->init(3);
		
		switch($action) {
			case 'enable':
				$stmt = $this->db->prepare("UPDATE devices SET active = :active, modified = :now, duration=0, status=10, updated = 0 WHERE id = :id");
				if ($_POST['active'] == 'true')
					$active = 1;
				else if ($_POST['active'] == 'false')
					$active = 0;
				$options = array(
					':id'=>$_POST['id'],
					':active'=>$active,
					':now'=>time()
				);
				$id = $_POST['id'];
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
				if ($active == 0) {
					$this->db->query("DELETE * FROM devices_history WHERE device_id = '" . $id . "'");

				}
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