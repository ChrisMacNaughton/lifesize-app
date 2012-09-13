<?php

class defaultController {
	public function __construct($db, $request) {
		$this->db = $db;
		$this->request = $request;
		$this->user = $request['user'];
	}
}