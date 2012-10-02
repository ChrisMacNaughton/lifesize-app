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
		
		$stmt = $db->prepare("SELECT * FROM users WHERE id = :id AND sesshash = :hash LIMIT 1");
		$stmt->execute(array(
			':id'=>$_COOKIE['controlVC_uid'],
			':hash'=>$_COOKIE['controlVC_hash']
			));
		
		if($stmt->rowCount() > 0){
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$this->info = $result;
			$this->logged_in = true;
		}
	}
	public function getInfo(){
		return $this->info;
	}
	public function is_logged_in(){
		return $this->logged_in;
	}
}