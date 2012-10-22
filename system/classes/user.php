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
		'e616f41e091bdbdcaf48b72aa70e2b7d860b30cd'=>'G.711 a-law',
		'da39a3ee5e6b4b0d3255bfef95601890afd80709'=>''
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
		$stmt = $this->db->prepare("SELECT CD.id,
			CD.ip,
			CD.password,
			CD.own,
			CD.verified,
			D.serial,
			D.online,
			D.in_call,
			D.update,
			D.updated,
			D.added,
			D.name,
			D.make,
			D.model,
			D.version,
			D.type,
			D.updating,
			CD.location,
			D.licensekey,
			D.outgoing_call_bandwidth,
			D.incoming_call_bandwidth,
			D.outgoing_total_bandwidth,
			D.incoming_total_bandwidth,
			D.auto_bandwidth,
			D.max_calltime,
			D.max_redials,
			D.auto_answer,
			D.auto_answer_mute,
			D.auto_answer_multiway,
			D.audio_codecs,
			D.audio_active_microphone,
			D.telepresence,
			D.camera_lock,
			D.camera_far_control,
			D.camera_far_set_preset,
			D.camera_far_use_preset,
			D.line_out_bass,
			D.line_out_treble,
			D.line_in_volume,
			D.active_microphone_volume,
			D.audio_mute_device,
			D.video_call_audio_output,
			D.voice_call_audio_output,
			D.ring_tone_volume,
			D.dtmf_tone_volume,
			D.status_tone_volume
			FROM `companies_devices` AS CD
			INNER JOIN devices AS D ON CD.hash = D.id
			WHERE CD.company_id = :company");
			$stmt->execute(array(':company'=>$this->getCompany()));
			$devs=$stmt->fetchAll(PDO::FETCH_ASSOC);
			$devices = array();
			foreach($devs as $dev){
				$devices[$dev['id']] = $dev;
				$codecs = json_decode($devices[$dev['id']]['audio_codecs'], true);
				$ret_short = array(); $ret = array();
				if($codecs != ''){
					foreach($codecs as $codec){
						if(!array_search($codec, $ret_short))
						$ret_short[] = $codec;
						$ret[sha1($codec)] = array('id'=>$codec, 'name'=>$this->codecs[sha1($codec)]);
					}
				}
				$devices[$dev['id']]['audio_codecs'] = $ret;
				$devices[$dev['id']]['audio_codecs_short'] = $ret_short;
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
	public function getInfo($id){
		if($id == $this->info['id'])
			return $this->info;
		else {
			$stmt = $this->db->prepare("SELECT users.*, levels.name as levelName, levels.level as level, L.permission AS permissions, users.timezone as timezone FROM users INNER JOIN levels ON users.level = levels.id INNER JOIN levels_permissions AS L ON L.level_id = users.level WHERE users.id = :id LIMIT 1");
			$stmt->execute(array('id'=>$id));
			$user = $stmt->fetch(PDO::FETCH_ASSOC);
			unset($user['password']);
			return $user;
		}
	}
	public function getID(){
		return $this->info['id'];
	}

	public function is_logged_in(){
		return $this->logged_in;
	}
	/*
	*	var $user =  array(
	*		id=>,
	*		name=>,
	*		email=>,
	*		level=>,
	*		timezone=>,
	*		companyId=>
	*	)
	*
	*/
	public function newUser($user){
		$stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
		$stmt->execute(array(':email'=>$user['email']));
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if(count($res) != 0){
			return false;
		}
		$stmt = $this->db->prepare("INSERT INTO users (id, name, email, password, level, timezone, `as`) VALUES (:id, :name, :email, :password, :level, :timezone, :companyId)");
		$company_stmt = $this->db->prepare("INSERT INTO users_companies (user_id, company_id, added, own) VALUES (:user_id, :companyId, unix_timestamp(), 1)");
		
		$stmt->execute($user);
		$company_stmt->execute(array(
			'user_id'=>$user['id'],
			'companyId'=>$user['companyId']
		));
		return true;
	}
	public function getPlan(){
		$key = array_search($this->info['as'], $this->companies);
		return $this->companies[$key]['plan_id'];
	}
}