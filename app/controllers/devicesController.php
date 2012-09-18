<?php

class devicesController extends Controller {
	public function beforeAction() {
		parent::beforeAction();
		$stmt = $this->db->prepare("SELECT devices.id, devices.name, devices.ip, devices.password, codes.name AS status, devices.active, devices.online FROM devices LEFT JOIN codes ON devices.status = codes.code WHERE company_id = :id ORDER BY name, ip, added");
		$this->company = $this->user->getCompanyDetails();
		$stmt->execute(array(':id'=>$this->company['id']));
		$this->devices = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$subscription = explode('_', $this->company['subscription_id']);
		//echo "<!--";print_r($subscription);echo"-->";
		$this->max_devices = ($subscription[1] != "-") ? $subscription[1] : 0;
	}
	public function indexAction() {
		$data = array(
			'title'=>'Devices',
			'devices'=>$this->devices
		);
		$this->render('devices/index.html.twig', $data);
	}
	public function alarmsAction($dev_id) {
		if($dev_id != "") {
			$data = array('title'=>"Alarms");
			$data['device_id']=$dev_id;
			$stmt = $this->db->prepare("SELECT devices.name, devices.id, codes.name AS status FROM devices LEFT JOIN codes ON devices.status = codes.code WHERE active = 1 AND id = :id LIMIT 1");
			$stmt->execute(array(':id'=>$dev_id));
			$data['device'] = $stmt->fetch(PDO::FETCH_ASSOC);
			$data['db_errors'][] = $stmt->errorInfo();
			$list = $this->db->query("SELECT * FROM alarms")->fetchAll(PDO::FETCH_ASSOC);

			$stmt = $this->db->prepare("SELECT D.user_id, D.device_id, D.alarm_id, D.last_notified, D.n, D.active, D.enabled, A.name, A.description FROM devices_alarms AS D RIGHT JOIN alarms AS A ON D.alarm_id = A.id WHERE user_id = :user AND device_id = :device");
			$stmt->execute(array(':user'=>$this->user->getID(), ':device'=>$dev_id));
			$alarms = $stmt->fetchAll(PDO::FETCH_ASSOC);
			//echo "<!--";print_r($alarms);echo"-->";
			$i=0;
			foreach ($list as $alarm) {
				//echo "<!--" . $alarm['id'] . "-->";
				foreach ($alarms as $device){
					$id = array_search($alarm['id'], $device);
					//echo "<!-- ID: $id -->";
					if ($id){
						//echo "<!-- Device: ";print_r($device);echo " -->";

						$list[$i]['enabled'] = $device['enabled'];
						$list[$i]['last_notified'] = $device['last_notified'];
					} else {
						if(!isset($list[$i]['enabled']))
							$list[$i]['enabled'] = 0;
						if(!isset($list[$i]['last_notified']))
							$list[$i]['last_notified'] = null;
					}
				}
				$list[$i]['device_id'] = $dev_id;
				$i++;
			}
			$data['alarms'] = $list;
			//$data['alarms'] = $alarms;
			$data['db_errors'][] = $stmt->errorInfo();
			echo $this->render('devices/alarms/device.html.twig', $data);
		} else {
			$data = array('title'=>"Alarms");
			$stmt = $this->db->prepare("SELECT D.user_id, D.device_id, D.alarm_id, D.last_notified, D.n, D.active, D.enabled, A.name, A.description, devices.name FROM devices_alarms AS D LEFT JOIN alarms AS A ON D.alarm_id = A.id LEFT JOIN devices AS devices ON devices.id = D.device_id WHERE user_id = :user AND D.enabled = 1");
			$stmt->execute(array(':user'=>$this->user->getID()));
			$data['alarms']= $stmt->fetchAll(PDO::FETCH_ASSOC);			
			echo $this->render('devices/alarms/index.html.twig', $data);
		}
	}
	public function imgAction($id) {
		$stmt = $this->db->prepare("SELECT screenshot FROM devices WHERE id = :id AND company_id = :company");
		$stmt->execute(array(
				':id'=>$id,
				':company'=>$this->company['id']
		));
		$img = $stmt->fetch(PDO::FETCH_ASSOC);
		$im = imagecreatefromstring(base64_decode($img['screenshot']));
		header('Content-Type: image/png');
		imagepng($im);
		imagedestroy($im);
		//echo $image;
	}
	public function editAction($id) {
		$stmt = $this->db->prepare("SELECT id, name, ip, company_id, online, status, duration, model_id, software_version_id, updated, screenshot FROM devices WHERE id = :id AND company_id = :company");
		$stmt->execute(array(
				':id'=>$id,
				':company'=>$this->company['id']
		));

		$device = $stmt->fetch(PDO::FETCH_ASSOC);
		if(isset($_POST['action']) && $_POST['action'] == 'edit') {
			unset($_POST['action']);
			foreach ($_POST as $target=>$detail){
				switch($target){
					case "name":
						$t = "name";
						break;
					case "password":
						$t="password";
						break;
					case "ip":
						$t="ip";
						break;
					default:
						break(2);
				}
				if($detail != "" && $device[$t] != $detail){

					$edits[] = array(
						':id'=>'edit-'. substr(sha1($device['id'] . $device['name'] . 'edit at' . microtime(true)),0,10),
						':object'=>'system',
						':target'=>$t,
						':detail'=>$detail,
						':added'=>time(),
						':user'=>$this->user->getID()
					);
				}
			}
			echo "<!-- Edits: ";print_r($edits);echo "-->";
		}
		$data['title'] = "View Device";
		$data['device'] = $device;
		$stmt = $this->db->prepare("SELECT sum(duration) AS duration FROM devices WHERE company_id = :id");
		$stmt->execute(array(':id'=>$this->company['id']));
		$res = $stmt->fetch(PDO::FETCH_ASSOC);
		$data['device']['duration_raw'] = $data['device']['duration'];
		$data['device']['duration'] = formatTime($device['duration']);
		$data['device']['global_duration_raw'] = $res['duration'];
		$data['device']['global_duration'] = formatTime($res['duration']);
		//print_r($device);
		$this->render('devices/edit.html.twig', $data);
	}
	public function viewAction($id) {
		$stmt = $this->db->prepare("SELECT id, name, ip, company_id, online, status, duration, model_id, software_version_id, updated FROM devices WHERE id = :id AND company_id = :company");
		$stmt->execute(array(
				':id'=>$id,
				':company'=>$this->company['id']
		));

		$device = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt = $this->db->prepare("SELECT Round(Avg(TxV1PktsLost),2), Round(Avg(RxV1PktsLost),2), Round(Avg(TxV1AvgJitter),2), Round(Avg(RxV1AvgJitter),2), AVG(TIME_TO_SEC(Duration)), Round(Avg(TxV1PktsLost) / SEC_TO_TIME(AVG(TIME_TO_SEC(Duration))), 2) FROM devices_history WHERE TIME_TO_SEC(Duration) > 60 AND HOUR(StartTime) = :time AND device_id = :id AND TxV1PktsLost / TIME_TO_SEC(Duration) < :ratio");
		for($i = 0; $i < 24; $i++) {
			$stmt->execute(array(
				':id'=>$device['id'],
				':time'=>$i,
				':ratio'=>5
				));
			$statistics[] = $stmt->fetch(PDO::FETCH_NUM);
			//echo "<!-- $i: ";print_r($statistics[$i]);echo "-->";
		}
		$stmt = $this->db->prepare("SELECT Id, Duration, StartTime, Round(TxV1PktsLost / TIME_TO_SEC(Duration), 2) AS Ratio FROM devices_history WHERE device_id = :id AND TxV1PktsLost / TIME_TO_SEC(Duration) >= :ratio ORDER BY TxV1PktsLost / TIME_TO_SEC(Duration) DESC");
		$stmt->execute(array(':ratio'=>5,
			':id'=>$device['id']));
		$data['worst_calls'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$data['stats'] = $statistics;
		$data['title'] = "View Device";
		$data['device'] = $device;
		$stmt = $this->db->prepare("SELECT sum(duration) AS duration FROM devices WHERE company_id = :id");
		$stmt->execute(array(':id'=>$this->company['id']));
		$res = $stmt->fetch(PDO::FETCH_ASSOC);
		$data['device']['duration_raw'] = $data['device']['duration'];
		$data['device']['duration'] = formatTime($device['duration']);
		$data['device']['global_duration_raw'] = $data['device']['global_duration'];
		$data['device']['global_duration'] = formatTime($res['duration']);
		//print_r($device);
		$this->render('devices/view.html.twig', $data);
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