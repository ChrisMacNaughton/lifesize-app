<?php

class updateController extends Controller {
	public function indexAction() {
		$stmt = $this->db->prepare($query);
		$stmt->execute();
		$stmt = $this->db->prepare("SELECT ip, id FROM devices WHERE updated < :updated");
		$stmt->execute(array(
		':updated'=>time() -600,
		));
		$devices = $stmt->fetchAll();
		var_dump($devices);
		foreach ($devices as $device) {
		echo "Device: " . $device['id'] . '<br />';
			$url = PATH . "/update/device/" . $device['id'];
			$ch = curl_init($url);
			
			curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
			curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1);

			curl_exec($ch);
			curl_close($ch);
			
			
		}
		
		$url = PATH . "/ping";
		$ch = curl_init($url);
			
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);

		curl_exec($ch);
		curl_close($ch);
	}
	public function deviceAction($id) {
		ignore_user_abort(true);
		set_time_limit(60);
		$stmt = $this->db->prepare("SELECT ip, password, license, maxCallId FROM devices WHERE id = :id");
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
			if ($call[0] > $device['maxCallId'])
			$final['callTime'] += time_to_seconds($call[8]);
			$final['callCount']++;
			$max_call = $call[0];
		}
		$final['maxCallId'] = $max_call;
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
			$model = $data[1];
		}
		$stmt = $this->db->prepare("SELECT id FROM models WHERE name = :name");
		$stmt->execute(array(':name'=>$model));
		$res = $stmt->fetch();
		$final['model_id'] = $res['id'];
		
		$data = $this->cleanLs('get system version', $ls);
		if ($data[5] == 'ok,00'){
			$data = explode(',',$data[0]);
			$final['version'] = $data[1];
		}
		//echo "<pre>";print_r($data);echo"</pre>";
		
		$final['history'] = $calls;
		echo "<pre>";
		print_r($final);
		echo "</pre>";
		$query = array(
			':name'=>$final['name'],
			':version'=>$final['version'],
			':callTime'=>$final['callTime'],
			':callCount'=>$final['callCount'],
			':calling'=>$final['calling'],
			':license'=>$final['license'],
			':model'=>$final['model_id'],
			':max'=>$final['maxCallId'],
		//	':version'=>$final['version'],
			':updated'=>time(),
			':id'=>$id
		);
		$stmt = $this->db->prepare("UPDATE devices SET name = :name, version = :version, call_length = :callTime, call_count = :callCount, calling = :calling, license = :license, model_id = :model, maxCallId = :max, updated = :updated, online_updated = :updated WHERE id = :id");
		$stmt->execute($query);
	}
	
	private function cleanLs($request, $ssh) {
		return explode(chr(0x0a), $ssh->exec($request));
	}
}