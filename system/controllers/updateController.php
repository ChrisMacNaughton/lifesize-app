<?php

class updateController extends Controller {
	public function indexAction() {
		$updated = settings('updated');
		if ($updated > time() - 600) {
			$this->db->query("UPDATE settings SET value=" . time() . " WHERE setting = updated");
		} else {
			die();
		}
		$stmt = $this->db->prepare("SELECT ip, id FROM devices");
		$stmt->execute();
		$devices = $stmt->fetchAll();
		foreach ($devices as $device) {
			$url = "/update/device/" . $device['id'];
			$ch = curl_init($url);
			
			curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
			curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1);

			curl_exec($ch);
			curl_close($ch);
		}
	}
	public function deviceAction($id) {
		$stmt = $this->db->prepare("SELECT ip, password, license FROM devices WHERE id = :id");
		$stmt->execute(array(':id'=>$id));
		$device = $stmt->fetch();
		$ls = new Net_SSH2($device['ip']);
		if (!$ls->login('auto', $device['password'])) {
			die("something broke");
		}
		$online = 1;
		//get current system name
		$data = $this->cleanLs('get system name', $ls);
		if ($data[2] == 'ok,00')
			$final['name'] = $data[0];
		//get current ssytem software version
		$data = $this->cleanLs('get system version', $ls);
		if ($data[9] == 'ok,00') {
			$data = explode(',', $data[0]);
			$final['version'] = $data[1];
		}
		//get call history
		$data = $this->cleanLs('status call history -X -D ^', $ls);
		$calls = array();
		$callTime = 0;
		if ($data[count($data) - 2] == 'ok^00'){
			array_pop($data);array_pop($data);array_pop($data);
			foreach ($data as $call) {
				$calls[] = explode('^', $call);
				
			}
		}
		//get call time and count
		foreach ($calls as $call) {
			$final['callTime'] += time_to_seconds($call[8]);
			$final['callCount']++;
		}
		//is the system currently participating in a call?
		$data = $this->cleanLs('status call active', $ls);
		if ($data[1] == 'ok,00' && $data[0] == '')
			$final['calling'] = 0;
		else
			$final['calling'] = 1;
		
		$data = $this->cleanLs('get system licensekey -t maint', $ls);
		
		if ($data[2] == 'ok,00')
			$final['license'] = $data[0];
		else 
			$final['license'] = $device['license'];
		
		$data = $this->cleanLs('get system model', $ls);
		if ($data[2] == 'ok,00') {
			$data = explode(',', $data[0]);
			$make = $data[0];
			$model = $data[1];
		}
		$stmt = $this->db->prepare("SELECT id FROM makes WHERE name = :name");
		$stmt->execute(array(':name'=>$make));
		$res = $stmt->fetch();
		$final['make_id'] = $res['id'];
		$stmt = $this->db->prepare("SELECT id FROM models WHERE name = :name");
		$stmt->execute(array(':name'=>$model));
		$res = $stmt->fetch();
		$final['model_id'] = $res['id'];
		
		
		
		
		$final['history'] = $calls;
		echo "<pre>";
		print_r($final);
		echo "</pre>";
	}
	
	private function cleanLs($request, $ssh) {
		return explode(chr(0x0a), $ssh->exec($request));
	}
}