<?php

class User {
	protected $db;
	// Default/Guest user info
	protected $info = array(
		'id'=>'0',
		'username' => 'Guest',
		'name' => '',
		'group_id' => '3'
		);
	protected $logged_in = false;
	public $permissions;
	protected $codecs = array(
		'2701a82ca08574fe0e53a6d12407eba2b8c5bbfb'=>'Advanced Audio Coding - Low Complexity',
		'17b327d7aa14436173f7841aacaed1a8665f02ec'=>'Polycom(R) Siren14(TM) @ 24kbps',
		'cec18da1ae105a20df1e70f3fb469873a78a4dfa'=>'Polycom(R) Siren14(TM) @ 32kbps',
		'a3d939c5c883578209c775aae7d770f916ad1f50'=>'Polycom(R) Siren14(TM) @ 48kbps',
		'373977417c79d1e7f147190dcd1b2ad3be65d0dd'=>'Silk 24',
		'e03bf5e86bd33bc2d1fb75b791c56c5df8d28c63'=>'Silk 16',
		'aa6296e2608b6440ddd52e4c03840480f96e8baf'=>'Silk 12',
		'ee46360685063c9795358d51e22f82b23a704b4b'=>'Silk 8',
		'2243ffca4cf2685af65e8cffb50f36ae6a2a5539'=>'G.722.1',
		'9660dca981a3708615d438938f9e535199596734'=>'G.722',
		'a9b450163938b4d4bc8f5db078cd480f9f353f3b'=>'G.728',
		'954cc4d5b2e8716250bc400eb831827054079a64'=>'G.729',
		'f905115bad1dd5db8e66768e2a2f680c1e924b2e'=>'G.711 mu-law',
		'e616f41e091bdbdcaf48b72aa70e2b7d860b30cd'=>'G.711 a-law'
	);
	public function __construct($db, $writedb){
		$this->db = $db;
		$this->writedb = $writedb;
		if(!isset($_COOKIE['controlVC_uid'])) $_COOKIE['controlVC_uid'] = "";
		if(!isset($_COOKIE['controlVC_hash'])) $_COOKIE['controlVC_hash'] = "";
		
		$this->updateUser();
	}
	public function updateUser(){
		$stmt = $this->db->prepare("SELECT users.*, levels.name as levelName, levels.level as level, L.permission AS permissions, users.timezone as timezone FROM users INNER JOIN levels ON users.level = levels.id INNER JOIN levels_permissions AS L ON L.level_id = users.level WHERE users.id = :id AND users.sesshash = :hash LIMIT 1");
		$stmt->execute(array(
			':id'=>$_COOKIE['controlVC_uid'],
			':hash'=>$_COOKIE['controlVC_hash']
			));
		if($stmt->rowCount() > 0){
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$this->info = $result;
			$this->clean();
			$this->permissions = $result['permissions'];
			$this->logged_in = true;
			$stmt = $this->db->prepare("SELECT U.user_id, C.*, U.added, U.own from users_companies as U INNER JOIN companies as C ON C.id = U.Company_id WHERE user_id = :id");
			$stmt->execute(array(':id'=>$this->info['id']));
			$this->companies = $stmt->fetchAll(PDO::FETCH_ASSOC);

			$this->updateDevices();
		}
	}
	public function getTimezone(){
		return (isset($this->info['timezone']))?$this->info['timezone'] : "GMT";
	}
	public function updateDevices(){
		$stmt = $this->db->prepare("SELECT devices.* , companies_devices.own, companies_devices.verified, companies_devices.verify_sent, companies_devices.verify_code
FROM devices
INNER JOIN companies_devices ON devices.id = companies_devices.device_id
INNER JOIN companies ON companies_devices.company_id = companies.id
INNER JOIN users_companies ON companies.id = users_companies.company_id
INNER JOIN users ON users.id = users_companies.user_id
WHERE users.id =:id AND companies_devices.company_id = :company
ORDER BY devices.online DESC, devices.name, devices.id");
			$stmt->execute(array(':id'=>$this->info['id'], ':company'=>$this->getCompany()));
			$devs=$stmt->fetchAll(PDO::FETCH_ASSOC);
			$devices = array();
			foreach($devs as $dev){
				$devices[$dev['id']] = $dev;
				$codecs = json_decode($devices[$dev['id']]['audio_codecs'], true);
				if($codecs != ''){
					foreach($codecs as $codec){
						
						$ret[] = array('id'=>$codec, 'name'=>$this->codecs[sha1($codec)]);
					}
				}
				$devices[$dev['id']]['audio_codecs'] = $ret;
			}
			$this->devices = $devices;
	}
	public function getCompany(){
		return $this->info['as'];
	}
	protected function clean(){
		unset($this->info['password']);
		unset($this->info['sesshash']);
	}
	public function getCompanies($id){
		if($id == $this->info['id'])
			return $this->companies;
	}
	public function getCompanyDetails(){
		foreach($this->companies as $key=>$value){
			if($value['id'] == $this->info['as']){
				return $this->companies[$key];
			}
		}
	}
	public function getInfo(){
		return $this->info;
	}
	public function getID(){
		return $this->info['id'];
	}

	public function is_logged_in(){
		return $this->logged_in;
	}
}