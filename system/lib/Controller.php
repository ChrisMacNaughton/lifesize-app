<?php

class Controller {
protected $user;
protected $db;
	public function __construct($user, $db, $controller, $action) {
		$this->user = $user;
		$this->db = $db;
		$this->controller = $controller;
		$this->action = $action;
	}
	public function beforeAction() {
		$this->user->hasPermission($this->controller, $this->action, 'user/login');
	}
}