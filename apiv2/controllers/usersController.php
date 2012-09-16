<?php

class usersController extends defaultController {
	public function putAction() {
		$data = array();
		if(!isset($this->request['put']['id'])){
			throw new Exception("User ID is not set", 400);
		}
	}
}