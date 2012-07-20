<?php

class User {
	
	protected $user = array();
	protected $hasher;
	protected $db;
	public function __construct($db) {
		$this->db = $db;
		$this->hasher = new PasswordHash(10, FALSE);
	}
	public function isAuthenticated() {
		return false;
	}
	public function login($username, $company, $password, $rememberme) {
		$username = mysql_real_escape_string($username);
		$company = mysql_real_escape_string($password);
		$password = mysql_real_escape_string($password);
		
	}
}