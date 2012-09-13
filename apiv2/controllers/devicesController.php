<?php

class devicesController extends defaultController {
	public function getAction() {
		$request = $this->request;
		if($request['id'] == 'index'){
			//get a list of devices
			$stmt = $this->db->prepare("SELECT devices.id, devices.online, devices.name, devices.ip, codes.name AS status FROM devices LEFT JOIN codes ON devices.status = codes.code WHERE company_id = :id");
			$stmt->execute(array(':id'=>$this->user['company_id']));
			$devices = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			$result['result'] = $devices;
			$err = $stmt->errorInfo();
			if($err[0] != '00000') {
				$result['errors'] = $stmt->errorInfo();
			}
			//$result['user_info'] = $this->user;
			//$result['result'] = "Device List";
		}
		else {
			$stmt= $this->db->prepare("SELECT devices.name, devices.online, devices.name, devices.ip, codes.name AS status FROM devices LEFT JOIN codes ON devices.status = codes.code WHERE company_id = :id AND devices.id = :dev_id LIMIT 1");
			$stmt->execute(array(':id'=>$this->user['company_id'], ':dev_id'=>$request['id']));

			$result['result'] = $stmt->fetch(PDO::FETCH_ASSOC);
			$err = $stmt->errorInfo();
			if($err[0] != '00000') {
				$result['errors'] = $stmt->errorInfo();
			}
		}
		return $result;
	}
	public function putAction() {

		$data = array();
		if(!isset($this->request['put']['id'])){
			throw new Exception("Device ID is not set", 400);
		}
		$device_id = $this->request['put']['id'];
		unset($this->request['put']['id']);
		$time = time();
		$stmt = $this->db->prepare("INSERT INTO edits (`id`, `device_id`, `object`, `target`, `detail`, `added`) VALUES(:editid,:id,:obj,:tar,:det,:time)");
		$check = $this->db->prepare("SELECT * FROM edits WHERE device_id = :id, object=:object, target=:target, detail=:detail, completed=0");
		foreach ($this->request['put'] as $key=>$value) {
			switch($key) {
				case 'name':
					$target = "name";
					break;
				default:
					throw new Exception("Invalid target", 400);
			}
			$id = 'edit-' . substr(sha1($device_id . microtime(true) . $this->user['id']), 0, 10);
			$edit = array(
				':editid'=>$id,
				':id'=>$device_id,
				':obj'=>'system',
				':tar'=>$target,
				':det'=>$value,
				':time'=>$time
			);
			$check_array = $edit = array(
				':id'=>$device_id,
				':obj'=>'system',
				':tar'=>$target,
				':det'=>$value,
				':time'=>$time
			);
			$check->execute($check_array);
			if($check->numRows() > 0) {
				$res[$device_id . ' - ' . $target . '-' . $value][] = "Already Added";
			} else {
				$res[$device_id . ' - ' . $target . '-' . $value][] = $stmt->execute($edit);
				$res[$device_id . ' - ' . $target . '-' . $value]['err'] = $stmt->errorInfo();
			}
		}	
		$data['request'] = $this->request;
		$data['edits'] = $res;
		ksort($data);
		return $data;
	}
}