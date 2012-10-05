<?php

class devicesController extends Controller {
	public function indexAction(){
		$data = array(
			'title'=>"Devices"
		);
		$data['headercolor'] = '99ff99';
		$data['devices'] = $this->user->devices;
		$this->render('devices/index.html.twig', $data);
	}
	public function viewAction($id){
		$data=array(
			'headercolor'=>'66cc66',
		);
		$data['device'] = $this->user->devices[$id];
		$this->render('devices/view.html.twig', $data);
	}
	public function verifyAction($id){
		$data = array(
			'headercolor'=>'00ff00'
		);
		$data['device'] = $this->user->devices[$id];
		if($data['device']['verified'] == 1){
			header("Location: ".PROTOCOL.ROOT .'/devices/view/' . $id);
			exit(0);
		}
		if(isset($_POST['confirm']) && $_POST['confirm'] == 'true'){
			$confirm = substr( sha1($id . $this->user->getCompany() . microtime(true)), 0,8);
			$options = array(
				'code'=>$confirm,
				'id'=>$id
			);
			$this->sqs->send_message(VERIFY_URL, json_encode($options));
			$stmt = $this->db->prepare("UPDATE companies_devices SET verify_code = :confirm, verify_sent = 1 WHERE device_id = :device AND company_id = :company");
			$stmt->execute(array(
				':confirm'=>$confirm,
				':device'=>$id,
				':company'=>$this->user->getCompany()
			));
			$this->user->updateDevices();
			$data['device'] = $this->user->devices[$id];
		}
		if(isset($_POST['verify']) && $_POST['verify'] == 'true'){
			if($_POST['code'] == $data['device']['verify_code']){
				$stmt = $this->writedb->prepare("UPDATE companies_devices SET verified = 1 WHERE company_id = :comp AND device_id = :dev");
				$res = $stmt->execute(array(
					':comp'=>$this->user->getCompany(),
					':dev'=>$id
				));
				if($res){
					$this->user->updateDevices();
					$data['device'] = $this->user->devices[$id];
				}
			}
		}
		$this->render("devices/verify.html.twig", $data);
	}
	public function editAction($id){
		$data = array(
			'headercolor'=>'66ff66'
		);
		if(isset($_POST['action']) && $_POST['action'] == 'edit'){
			unset($_POST['action']);
			$edited = false;
			foreach($_POST as $key=>$edit){
				if($edit != ''){
					$edited = true;
					switch($key){
						case "location":
							$stmt = $this->writedb->prepare("UPDATE devices SET location = :value WHERE id = :id");
							$stmt->execute(array(
								':value'=>$edit,
								':id'=>$id
							));
							break;
						case "licensekey":
							echo "<!-- License Update-->";
							break;
					}
				}
			}
			if($edited) {
				$this->user->updateDevices();
				$data['device'] = $this->user->devices[$id];
			}
		}
		$data['device'] = $this->user->devices[$id];
		$this->render('devices/edit.html.twig', $data);
	}
	public function addAction(){
		$data = array(
			'headercolor'=>'339933'
		);
		if(isset($_POST['action']) && $_POST['action'] == 'add'){

			$options = array(
				':ip'=>$_POST['ip'],
				':password'=>$_POST['device_pass'],
				':time'=>time()
			);

			$options[':id'] = 'dev-' . substr(hash('sha512', $options[':ip'] . microtime(true)), 0,10);
			$stmt = $this->writedb->prepare("INSERT INTO devices (`id`,`ip`,`password`, `added`) VALUES (:id, :ip, :password, :time)");
			$res = $stmt->execute($options);
			if(!$res){
				$_SESSION['errors'][] = "Failed to add the device";
				$stmt = $this->writedb->prepare("DELETE FROM devices WHERE id = :id");
				$stmt->execute(array(':id'=>$options[':id']));
			}
			$id = $options[':id'];
			$stmt = $this->writedb->prepare("INSERT INTO companies_devices (`company_id`,`device_id`,`own`) VALUES (:company, :id, 1)");
			$res = $stmt->execute(array(
				':company'=>$this->user->getCompany(),
				':id'=>$id
			));
			if(!$res){
				$_SESSION['errors'][] = "Failed to add the device";
				$stmt = $this->writedb->prepare("DELETE FROM devices WHERE id = :id");
				$stmt->execute(array(':id'=>$options[':id']));
				$stmt = $this->writedb->prepare("DELETE FROM companies_devices WHERE device_id = :id");
				$stmt->execute(array(':id'=>$options[':id']));
			}
			if(!isset($_SESSION['errors'])){
				$_SESSION['flash'][] = "Successfully added the device!";
			}
			//echo"<!--";print_r($options);echo"-->";
		}
		$this->render('devices/add.html.twig', $data);
	}
	public function deleteAction(){
		$data = array(
			'headercolor'=>'cc6666'
		);
		if(isset($_POST['id'])){
			//delete the device if my company owns it
			$id = $_POST['id'];
			echo "<!-- Deleting $id -->";

			$stmt = $this->writedb->prepare("DELETE FROM companies_devices WHERE device_id = :id AND company_id = :company AND own = 1");
			$stmt->execute(array(':id'=>$id, ':company'=>$this->user->getCompany()));
			$affected = $stmt->rowCount();
			if($affected > 0){
				$stmt = $this->writedb->prepare("DELETE FROM companies_devices WHERE device_id = :id");
				$stmt->execute(array(':id'=>$id));
				$stmt = $this->writedb->prepare("DELETE FROM devices WHERE id = :id");
				$stmt->execute(array(':id'=>$id));
			} else {
				$stmt = $this->writedb->prepare("DELETE FROM companies_devices WHERE device_id = :id AND company_id = :company");
				$stmt->execute(array(':id'=>$id, ':company'=>$this->user->getCompany()));
			}
			header("Location: ".PROTOCOL.ROOT . "/devices/delete");
				
			//redirect to clear post

		}

		$data['devices'] = $this->user->devices;
		$this->render('devices/delete.html.twig', $data);
	}
}