<?php

class User {
	
	protected $user = array();
	public function __construct($user = null) {
		if (is_null($user)) {
			$this->user = array(
				'userid'=>0,
				'username'=>'guest'
			);
		} else {
			$this->user = $user;
		}
	}
}