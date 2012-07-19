<?php

class User {
	
	protected $user = array();
	protected $hasher;
	public function __construct($user = null) {
		require_once 'passwordHash.php';
		
		$this->hasher = new PasswordHash(10, FALSE);
		if (is_null($user)) {
			$this->user = array(
				'userid'=>0,
				'username'=>'guest'
			);
		} else {
			$this->user = $user;
		}
	}
	public function isAuthenticated() {
		return false;
	}
}