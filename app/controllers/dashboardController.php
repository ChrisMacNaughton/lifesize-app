<?php

class dashboardController extends Controller {
	public function indexAction(){
		$data = array('user'=>$this->user, 'title'=>'Dashboard');
		$data['headercolor'] = '99ccff';
		$data['devices_count'] = count($this->user->devices);
		$count = 0;$calling = 0;$updating = 0;
		foreach ($this->user->devices as $dev){
			if($dev['type'] == 'camera') $count++;
			if($dev['in_call'] == 1)$calling++;
			if($dev['update'] == 1)$updating++;
		}
		$data['updating'] = $updating;
		$data['in_a_call'] = $calling;
		$data['video_count'] = $count;
		$stmt = $this->db->prepare("SELECT count(*) AS count 
FROM devices_history
INNER JOIN devices ON devices_history.device_id = devices.id
INNER JOIN companies_devices AS cd ON cd.hash = devices.id
WHERE cd.company_id = :id");
		$stmt->execute(array(':id'=>$this->user->getCompany()));
		$res = $stmt->fetch(PDO::FETCH_ASSOC);
		$data['call_count'] = $res['count'];
		$stmt = $this->db->prepare("SELECT SUM(duration) AS sum
FROM devices
INNER JOIN companies_devices AS cd ON cd.hash = devices.id
WHERE cd.company_id = :id");
		$stmt->execute(array(':id'=>$this->user->getCompany()));
		$res = $stmt->fetch(PDO::FETCH_ASSOC);
		$time = ($res['sum'] / 60);
		$scale = "minutes";
		if($time > 360){
			$time = ($time/60);
			$scale = "hours";
		}
		if($time > 360){
			$time = ($time/24);
			$scale = "days";
		}
		if($time > 365){
			$time = ($time / 365);
			$scale = "years";
		}
		$data['call_time'] = round($time, 2); $data['scale'] = $scale;

	$stmt = $this->db->prepare("SELECT count(DISTINCT devices_history.device_id) AS sum
FROM devices_history
INNER JOIN devices ON devices_history.device_id = devices.id
INNER JOIN companies_devices AS cd ON cd.hash = devices.id
WHERE cd.company_id = :id");
		$stmt->execute(array(':id'=>$this->user->getCompany()));
		$res = $stmt->fetch(PDO::FETCH_ASSOC);
		$data['devices_used'] = $res['sum'];
		
		$data['unused_devices'] = $data['devices_count'] - $data['devices_used'];

		$stmt = $this->db->prepare("SELECT count(*) AS sum FROM `devices_alarms`
INNER JOIN companies_devices ON devices_alarms.device_id = companies_devices.id
WHERE companies_devices.company_id = :id
AND devices_alarms.active = 1");
		$stmt->execute(array(':id'=>$this->user->getCompany()));
		$res = $stmt->fetch(PDO::FETCH_ASSOC);
		$data['all_alarms'] = $res['sum'];

		$stmt = $this->db->prepare("SELECT COUNT( * ) AS sum
FROM users_alarms
INNER JOIN devices_alarms ON users_alarms.device_id = devices_alarms.device_id
AND users_alarms.alarm_id = devices_alarms.alarm_id
WHERE users_alarms.user_id = :id
AND users_alarms.enabled = 1
AND devices_alarms.active =1");
		$stmt->execute(array(':id'=>$this->user->getID()));
		$res = $stmt->fetch(PDO::FETCH_ASSOC);
		$data['my_alarms'] = $res['sum'];

		$stmt = $this->db->prepare("SELECT devices.name AS name, companies_devices.ip AS ip, alarms.description AS description
FROM  `devices_alarms` 
INNER JOIN alarms ON alarms.id = devices_alarms.alarm_id
INNER JOIN companies_devices ON devices_alarms.device_id = companies_devices.device_id
INNER JOIN devices ON companies_devices.id = devices_alarms.device_id
WHERE companies_devices.company_id = :id
AND devices_alarms.active =1
ORDER BY devices_alarms.updated DESC
LIMIT 1");

		$stmt->execute(array(':id'=>$this->user->getCompany()));
		$res = $stmt->fetch(PDO::FETCH_ASSOC);
		$data['alarm'] = $res;
		$this->render("dashboard.html.twig", $data);
	}

}