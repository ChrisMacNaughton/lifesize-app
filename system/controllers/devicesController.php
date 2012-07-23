<?php

class devicesController extends Controller{
	public function beforeAction() {
		parent::beforeAction();
		$stmt = $this->db->prepare("SELECT max FROM companies WHERE id = :company_id");
		$stmt->execute(array(':company_id'=>$this->user->getCompany()));
		$max = $stmt->fetch();
		$this->maxDevices = $max['max'];
	}
	public function indexAction() {
		$stmt = $this->db->prepare("SELECT * FROM devices WHERE company_id = :id");
		$stmt->execute(
			array(
			':id'=>$this->user->getCompany(),
		));
		$devices =  $stmt->fetchAll();
		$data = array(
		'title'=>'Devices',
		'devices'=>$devices
		);
		render ('devices/index.html.twig', $data);
	}
	public function editAction($id) {
		$stmt = $this->db->prepare("SELECT * FROM devices WHERE id = :id");
		$stmt->execute(array(
			':id'=>$id,
		));
		$name = $_POST['name'];
		$new_pass = $_POST['password'];
		$license = $_POST['license'];
		$device = $stmt->fetch();
		$ip = $device['ip'];
		try {
		echo "The new IP works!";
			$vc = new Net_SSH2($_POST['ip']);
			$stmt = $this->db->prepare("UPDATE devices SET ip = :ip WHERE id = :id");
			$stmt->execute(array(
				':ip'=>$_POST['ip'],
				':id'=>$id
			));
		} catch(Exception $e) {
		echo "Using the old IP";
		$vc = new Net_SSH2($ip);}
		if ($vc->login('auto', $device['password'])) {
			$make = $vc->exec('get system model');
			$make = explode(',', $make);
			$make = $make[0];
			$results = array();
			switch ($make) {
				case 'LifeSize':
					if ($device['name'] != $name) {
						$data = explode(chr(0x0a), $vc->exec("set system name '" . $name . "'"));
						$results['name']= $data[1];
						if ($results['name'] == 'ok,00') {
						$stmt = $this->db->prepare("UPDATE devices SET name = :name WHERE id = :id");
						$stmt->execute(array(
							':name'=>$name,
							':id'=>$id
						));
						}
					} else {
						$results['name'] = "ok,00";
					}
					if ($device['password'] != $new_pass) {
						$data = explode(chr(0x0a), $vc->exec("set password " . $new_pass));
						$results['password'] = $data[1];
						if ($results['password'] == 'ok,00') {
							$stmt = $this->db->prepare("UPDATE devices SET password = :password WHERE id = :id");
							$stmt->execute(array(
								':password'=>$new_pass,
								':id'=>$id
							));
						}
					} else {
						$results['password'] = "ok,00";
					}
					/*
					if ($device['license'] != $license) {
					echo "<!-- updating license -->";
						$vc->write("set system licensekey -i << EOF");
						$vc->write($license);
						$vc->write("EOF");
						$res = $vc->read('$');
						$res .= $vc->read('$');
						echo "<!-- Result: $res -->";
						$data = $vc->exec("get system licensekey -t maint");
						echo "<pre>";print_r($data); echo"</pre>";
						$data = explode(chr(0x0a), $data);
						if ($data[0] == $license) {
							$results['license'] = $data[2];
						}
						if ($results['license'] == 'ok,00') {
							$stmt = $this->db->prepare("UPDATE devices SET license = :license WHERE id = :id");
							$stmt->execute(array(
								':license'=>$license,
								':id'=>$id
							));
						}
						
					} else {
						$results['license'] = "ok,00";
					}
					*/
					foreach ($results as $key=>$value) {
						if($value != "ok,00") {
							$_SESSION['errors'][] = l('error_updating') . $key;
						}
					}
					//var_dump($_SESSION['errors']);
					//header("Location: /devices/view/" . $id);
					break;
			}
		} else {
			/*
			*	Update queue function
			*/
		}
	}
	public function viewAction($id) {
		$stmt = $this->db->prepare("SELECT devices.*, makes.name as Make, models.name AS Model FROM devices LEFT JOIN models ON devices.model_id = models.id LEFT JOIN makes ON devices.make_id = makes.id WHERE devices.id = :id AND devices.company_id = :company_id");
		$stmt->execute(
			array(
			':id'=>$id,
			':company_id'=>$this->user->getCompany(),
		));
		$device = $stmt->fetch();
		$data = array(
			'devices'=>$device,
			'title'=>'Device ( '. $device['name'] . ') Details'
		);
		render('devices/view.html.twig', $data);
	}
}