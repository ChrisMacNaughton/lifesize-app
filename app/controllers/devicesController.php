<?php

class devicesController extends Controller {
	public function beforeAction() {
		parent::beforeAction();
		$stmt = $this->db->prepare("SELECT devices.id, devices.name, devices.ip, devices.password, codes.name AS status FROM devices LEFT JOIN codes ON devices.status = codes.code WHERE company_id = :id");
		$this->company = $this->user->getCompanyDetails();
		$stmt->execute(array(':id'=>$this->company['id']));
		$this->devices = $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	public function indexAction() {
		$data = array(
			'title'=>'Devices',
			'devices'=>$this->devices
		);
		$this->render('devices/index.html.twig', $data);
	}
	public function newAction() {
		$data = array(
			'title'=>'New Device'
		);
		if (isset($_POST['action']) && $_POST['action'] == 'new') {
			echo "<!--";print_r($_POST);echo"-->";
			$stmt = $this->db->prepare("INSERT INTO devices (ip, password, name, company_id) VALUES (:ip, :password, :name, :company)");
			$options = array(
				':ip'=>$_POST['ip'],
				':password'=>(isset($_POST['password'])) ?$_POST['password'] : 'Lifesize',
				':name'=>$_POST['name'],
				':company'=>$this->company['id'],
			);
			foreach ($options as $key=>$value) {
				if ((is_null($value) || $value == '') && $key != ':name') {
					$errors[]= l($key) . l('not_blank');
				}
			}
			if (count($errors) == 0) {
				$result = $stmt->execute($options);
				if ($result) {
					$_SESSION['flash'][] = l('success_add_device');
				} else {
					$err = $stmt->errorInfo();
					$_SESSION['errors'][] = $err[2];
				}
			} else {
				foreach ($errors as $err) {
					$_SESSION['errors'][] = $err;
				}
			}
		}
		$this->render('devices/new.html.twig', $data);
	}
}