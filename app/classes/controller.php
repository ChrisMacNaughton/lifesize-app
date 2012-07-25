<?php

class Controller {
	protected $controller, $action, $app, $db, $user;
	public function beforeAction() {
	
	}
	public function __construct($controller, $action, $app, $db) {
		$this->controller = $controller;
		$this->action = $action;
		$this->app = $app;
		$this->db = $db;
		global $user;
		$this->user = $user;
	}
	public function render($file, ARRAY $data = null) {
		global $app;
		$loader = new Twig_Loader_Filesystem('app/views');
		$twig = new Twig_Environment($loader, array(
			'cache'=>false,
			'debug'=>true
		));
		
		$twig->addExtension(new Twig_Extension_Debug());
		$start = $app['start'];
		unset($app['start']);
		$app['system']['load_time'] = microtime_diff($start);
		$data['app'] = $app;
		echo $twig->render($file, $data);
	}
}