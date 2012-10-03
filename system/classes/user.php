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
	public function __construct($db){
		$this->db = $db;

		if(!isset($_COOKIE['controlVC_uid'])) $_COOKIE['controlVC_uid'] = "";
		if(!isset($_COOKIE['controlVC_hash'])) $_COOKIE['controlVC_hash'] = "";
		
		$stmt = $db->prepare("SELECT users.*, levels.name as levelName, levels.level as level FROM users INNER JOIN levels ON users.level = levels.id WHERE users.id = :id AND users.sesshash = :hash LIMIT 1");
		$stmt->execute(array(
			':id'=>$_COOKIE['controlVC_uid'],
			':hash'=>$_COOKIE['controlVC_hash']
			));
		if($stmt->rowCount() > 0){
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$this->info = $result;
			$this->logged_in = true;
			$stmt = $this->db->prepare("SELECT U.user_id, C.id, C.name, U.added, C.partner, U.own from users_companies as U INNER JOIN companies as C ON C.id = U.Company_id WHERE user_id = :id");
			$stmt->execute(array(':id'=>$this->info['id']));
			$this->companies = $stmt->fetchAll(PDO::FETCH_ASSOC);

			$stmt = $this->db->prepare("SELECT devices.* 
FROM devices
INNER JOIN companies_devices ON devices.id = companies_devices.device_id
INNER JOIN companies ON companies_devices.company_id = companies.id
INNER JOIN users_companies ON companies.id = users_companies.company_id
INNER JOIN users ON users.id = users_companies.user_id
WHERE users.id =:id");
			$stmt->execute(array(':id'=>$this->info['id']));
			$this->devices=$stmt->fetchAll(PDO::FETCH_ASSOC);
		}
	}
	public function getInfo(){
		return $this->info;
	}
	public function is_logged_in(){
		return $this->logged_in;
	}
}