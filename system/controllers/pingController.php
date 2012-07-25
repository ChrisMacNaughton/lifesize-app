<?php

class pingController extends Controller {
	public function indexAction() {
		$stmt = $this->db->prepare("SELECT ip, id FROM devices WHERE updated < :updated");
		$stmt->execute(array(
		':updated'=>time() -600,
		));
		$devices = $stmt->fetchAll();
		foreach ($devices as $device) {
		
			$url = PATH . "/ping/device/" . $device['id'];
			$ch = curl_init($url);
			
			curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
			curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1);

			curl_exec($ch);
			curl_close($ch);
		}
	}
	public function deviceAction($id) {
		ignore_user_abort(true);
		set_time_limit(60);
		$stmt = $this->db->prepare("SELECT * FROM devices WHERE id = :id");
		$stmt->execute(array(':id'=>$id));
		$device = $stmt->fetch();

		$stmt = $this->db->prepare("UPDATE devices SET online = :online, online_updated = :updated WHERE id = :id");
		$ls = new Net_SSH2($device['ip']);
		if (!$ls->login('auto', $device['password'])) {
			$online = 0;
		} else {
			$online = 1;
		}
		
		//echo $online;
		$stmt->execute(array(
			':online'=>$online,
			':updated'=>time(),
			':id'=>$device['id']
		));
	}
}