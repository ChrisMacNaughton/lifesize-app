<?php

class Lifesize {
	protected $ssh;
	protected $connected;
	private static $errorCodes = array(
		'00' => 'Success',
		'01' => 'No memory',
		'02' => 'File Eror',
		'03' => 'Invalid Instance',
		'04'=>'Invalid Parameter',
		'05' => 'Argument is not Repeatable',
		'06' => 'Invalid Selection Parameter Value',
		'07' => 'Missing Argument',
		'08' => 'Extra Arguments on Command Line',
		'09'=>'Invalid Command',
		'0a'=>'Ambiguous Command',
		'0b'=>'Conflicting Parameter',
		'0c'=>'Operational Error',
		'0d'=>'No Data Available',
		'0e'=>'Not In Call',
		'0f'=>'Interrupted',
		'10'=>'Ambiguous Selection',
		'11'=>'No Matching Entries',
		'12'=>'Not Supported',
	);
	
	public function __construct($ip, $pass) {
		$this->ssh = new Net_SSH2($ip);
		if ($this->ssh->login('auto', $pass)) {
			$this->connected = true;
			$this->ssh->exec('set help-mode off');
		} else {
		$this->connected = false;
		}
	}
	public function __destruct() {
		$this->ssh->exec('exit');
	}
	public function connected() {
		return $this->connected;
	}
	public function update() {
		$device = array();
		$device['name'] = $this->getName();
		$device['calling'] = $this->getCalling();
		$device['license'] = $this->getLicense();
		$device['version'] = $this->getVersion('Software Version');
		$device['make'] = $this->getMake();
		$device['model'] = $this->getModel();
		
		return $device;
	}
	public function getName() {
		$name =  $this->get('system','name');
		$name = ($name['Status'] == "Success") ? $name['Data'] : false;
		return $name;
	}
	public function getCalling() {
		$calling = $this->status('call','active');
		if ($calling['Status'] == "Success" && $calling['Data'] != null)
			return 1;
		else 
			return 0;
	}
	public function getLicense() {
		$data = $this->get('system','licensekey', array('t maint'));
		if ($data['Status'] == "Success") 
			return $data['Data'];
		else
			return false;
	}
	public function getVersion($identifier) {
		$data = $this->get('system','version');
		if ($data['Status'] == "Success") 
			return $data['Data'][$identifier];
		else
			return false;
	}
	public function getMake() {
		return $this->getMakeModel('Make');
	}
	public function getModel() {
		return $this->getMakeModel('Model');
	}
	private function getMakeModel($choice) {
		$data = $this->get('system','model');
		if ($data['Status'] == "Success") {
			$data = explode(',',$data['Data']);
			$data = array(
				'Make' => $data[0],
				'Model' => $data[1]
			);
			return $data[$choice];
		} else {
			return false;
		}
	}
	/* basic lib functions */
	private function get($object, $target, ARRAY $options = null, $debug = false) {
		$func = 'get ' . $object . ' ' . $target;
		//iterate through options to build args to be passed to the system
		if (!is_null($options)) {
			foreach ($options as $option) {
				$func .= ' -' . $option;
			}
		}
		if ($debug) { echo "<!-- $func -->"; }
		$data = $this->seperateData($this->ssh->exec($func));
		$data = array(
			'Status'=>$data['Status'],
			'Data'=>$this->makeArray($data['Data']),
		);
		return $data;
	}
	public function status($object, $target, ARRAY $options = null, $debug = false){
		$func = 'status ' . $object . ' ' . $target;
		if (!is_null($options)) {
			foreach ($options as $option) {
				$func .= ' -' . $option;
			}
		}
		if ($debug) { echo "<!-- $func -->"; }
		$data = $this->seperateData($this->ssh->exec($func));
		//seperate out status and data to clean up data
		$status = $data['Status'];
		$data = $data['Data'];
		if (strpos($data, chr(0x0a))) {
			$data = explode(chr(0x0a), $data);
		}
		if (is_array($data)) {
			foreach ($data as $row) {
				$final[] = explode(',', $row);
			}
			$data = $final;
		} else if (is_string($data)) {
			$data = explode(',',$data);
		}
		
		//RECOMBINE!!!
		$data = array(
			'Status'=>$status,
			'Data'=>$data
		);
		return $data;
	}
	public function getCode($code) {
		return self::$errorCodes[$code];
	}
	private function seperateData($data){
		$data = explode(chr(0x0a).chr(0x0a), $data);
		//return $data;
		if (isset($data[1])) {
			$status = $data[1];
			$status = $this->seperateCode($status);
		$data = $data[0];
		return array(
			'Status'=>$this->getCode($status),
			'Data'=>$data
		);
		} else {
			$status = explode(chr(0x0a), $data[0]);
			$status=$status[1];
			$status = explode(chr(0x0a), $data[0]);
			$status = $status[1];
			return array(
				'Status'=>$this->getCode($this->seperateCode($status)),
				'Data'=>NULL
			);
		}
		
	}
	private function seperateCode($status) {
		$status = explode(chr(0x0a), $status);
		$status = $status[0];
		$status = explode(',', $status);
		$status = $status[1];
		return $status;
	}
	/*
	*	builds a keyed array from Lifesize's returned data if the data is array style data
	*/
	private function makeArray($data) {
		if (strpos($data, chr(0x0a))) {
		$data = explode(chr(0x0a), $data);
			if (is_array($data)) {
				foreach ($data as $row) {
					$final[] = explode(',', $row);
				}
				$data = array();
				foreach ($final as $row) {
					$data[$row[0]] = $row[1];
				}
			}
			
			return $data;
		} else {
		return $data;
		}
	}
}