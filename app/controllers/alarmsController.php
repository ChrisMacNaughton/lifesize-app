<?php

class alarmsController extends Controller {
	public function indexAction(){
		$data = array(
			'headercolor'=>'FF9933',
		);
		if(isset($_POST['action']) && $_POST['action'] == 'update'){
			unset($_POST['action']);
			$stmt = $this->writedb->prepare("INSERT INTO devices_alarms (`alarm_id`,`device_id`) VALUES (:alarm, :device)");
			$user = $this->writedb->prepare("INSERT INTO users_alarms (`user_id`,`alarm_id`,`device_id`, `enabled`) VALUES (:user, :alarm, :device,:enabled) ON DUPLICATE KEY UPDATE `enabled` = :enabled");
			foreach($_POST as $key=>$value){
				$name = explode('|', $key);
				$alarm = $name[0];
				$device = $name[1];
				switch($value){
					case "on":
						$enabled = 1;
						break;
					case "off":
						$enabled = 0;
						break;
				}
				$options = array(
					':alarm'=>$alarm,
					':device'=>$device
				);
				$stmt->execute($options);
				$options[':user']=$this->user->getID();
				$options[':enabled'] = $enabled;
				$user->execute($options);
			}
		}
		$stmt = $this->db->prepare("SELECT alarms.id as id, alarms.name as alarmname, alarms.description as description, devices.name as devicename, companies_devices.id as deviceid, companies_devices.ip as deviceip
FROM alarms
INNER JOIN devices
INNER JOIN companies_devices ON companies_devices.hash = devices.id
WHERE companies_devices.company_id =:id
ORDER BY alarms.name, devices.online DESC, devices.name, devices.id");
		$stmt->execute(array(':id'=>$this->user->getCompany()));
		$alarms = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$devices_alarms_stmt = $this->db->prepare("SELECT active FROM devices_alarms WHERE device_id = :device AND alarm_id = :alarm");
		$user_alarms_stmt =  $this->db->prepare("SELECT enabled FROM users_alarms WHERE device_id = :device AND alarm_id = :alarm");
		foreach($alarms as $key=>$alarm){
			$options = array(
				':device'=>$alarm['deviceid'],
				':alarm'=>$alarm['id']
			);
			$devices_alarms_stmt->execute($options);
			$res = $devices_alarms_stmt->fetch(PDO::FETCH_ASSOC);
			$active = $res['active'];

			$user_alarms_stmt->execute($options);
			$res = $user_alarms_stmt->fetch(PDO::FETCH_ASSOC);
			$enabled = $res['enabled'];
			$alarms[$key]['enabled'] = $enabled;
			$alarms[$key]['active'] = $active;
		}

		$data['alarms'] = $alarms;
		//$data['devices'] = $devices;
		//$data['alarms'] = $res;
		$this->render('alarms/index.html.twig', $data);
	}
	public function deviceAction($devic_id){

	}
}