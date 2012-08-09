<?php

class devicesController extends Controller {
	public function beforeAction() {
		parent::beforeAction();
		$stmt = $this->db->prepare("SELECT devices.id, devices.name, devices.ip, devices.password, codes.name AS status, devices.active FROM devices LEFT JOIN codes ON devices.status = codes.code WHERE company_id = :id ORDER BY added");
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
			$id_num = substr(hash('sha512',(rand(1,100000))), 0, 10);
			$stmt = $this->db->prepare("SELECT * FROM devices WHERE id = :id");
			$stmt->execute(array(':id'=>'dev-'.$id_num));
			
			while ($stmt->rowCount() > 0) {
				$id_num = substr(hash('sha512',(rand(1,100000))), 0, 10);
				$stmt->execute(array(':id'=>'dev-'.$id_num));
			}
			
			$stmt = $this->db->prepare("INSERT INTO devices (id, ip, password, name, company_id, status, online, added, active) VALUES (:id, :ip, :password, :name, :company, 10, 0, :added, :active)");
			$options = array(
				':id'=>'dev-' . $id_num,
				':ip'=>$_POST['ip'],
				':password'=>(isset($_POST['password'])) ?$_POST['password'] : 'Lifesize',
				':name'=>$_POST['name'],
				':company'=>$this->company['id'],
				':added'=>time(),
				':active'=>1
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
					$stmt = $this->db->prepare("INSERT INTO log (user, action,details,timestamp) VALUES (:user, :action, :details, :now)");
					$stmt->execute(array(
						':user'=>$this->user->getID(),
						':action'=>'add_device',
						':details'=>"User added device dev-" . $id_num,
						':now'=>time()
					));
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